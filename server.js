const express = require("express");
const app = express();
const cors = require("cors");
app.use(
  cors({
    origin: "*", // Permitir qualquer origem
    methods: ["GET", "POST"], // Permitir apenas os métodos GET e POST
    allowedHeaders: ["Content-Type", "Authorization"], // Permitir apenas os cabeçalhos necessários
  })
);

// Definir as rotas e demais configurações do servidor

// create body parser instance
var bodyParser = require("body-parser");

// enable URL encoded for POST requests
app.use(bodyParser.json());

// creating http instance
var http = require("http").createServer(app);

// creating socket io instance
var io = require("socket.io")(http, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
  },
});

// Create instance of mysql
var mysql = require("mysql");

// make a connection
var connection = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "chat",
});

// connect
connection.connect(function (error) {
  // show error if any
  if (error) {
    console.error("Error connecting: " + error.stack);
    return;
  }
});

app.post("/mark_messages_as_read", function (request, result) {
    connection.query(
      "UPDATE mensagens SET unread = FALSE WHERE remetente = ? AND receptor = ? AND unread = TRUE",
      [request.body.sender, request.body.receiver],
      function (error, res) {
        if (error) {
          console.error("Error:", error);
          result.status(500).send("Internal server error");
          return;
        }
  
        result.json({ status: "success" });
      }
    );
  });
  

// create api to return all messages
app.post("/get_messages", function (request, result) {
    console.log("Request body:", request.body);
    connection.query(
      "SELECT *, DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') as formatted_timestamp FROM mensagens WHERE (remetente = ? AND receptor = ?) OR (remetente = ? AND receptor = ?)",
      [
        request.body.sender,
        request.body.receiver,
        request.body.receiver,
        request.body.sender,
      ],
      function (error, messages) {
        if (error) {
          console.error("Error:", error);
          result.status(500).send("Internal server error");
          return;
        }
  
        result.json(messages);
      }
    );
  });
  

app.get("/connected_users", function (req, res) {
  res.json(getConnectedUsers());
});

function getConnectedUsers() {
  let userList = [];
  for (let id in io.sockets.sockets) {
    if (io.sockets.sockets[id].username) {
      userList.push(io.sockets.sockets[id].username);
    }
  }
  return userList;
}

var users = [];

io.on("connection", function (socket) {
  console.log("Usuário conectado", socket.id);

  socket.on("get_connected_users", function () {
    socket.emit("connected_users", getConnectedUsers());
  });
  
  // anexar listener para novo usuário conectado
  socket.on("user_connected", function (username) {
    // salvar no array
    users[username] = socket.id;

    // salvar o username no objeto socket
    socket.username = username;

    // o ID do socket será usado para enviar mensagem para a pessoa individualmente

    // notificar todos os clientes conectados
    io.emit("user_connected", username);
  });

  // adicione o evento disconnect
  socket.on("disconnect", function () {
    console.log("Usuário desconectado", socket.id);

    if (socket.username) {
      delete users[socket.username];
      io.emit("user_disconnected", socket.username);
    }
  });

  // listen from client
  socket.on("send_message", function (data) {
    console.log("Data received:", data);
    // send event to receiver
    var socketId = users[data.receiver];

    io.to(socketId).emit("new_message", data);

   // inserir no banco de dados
   connection.query(
    "INSERT INTO mensagens (remetente, receptor, mensagem, timestamp) VALUES (?, ?, ?, ?)",
    [data.sender, data.receiver, data.message, new Date()],
    function (error, result) {
      if (error) {
        console.error("Error inserting message:", error);
        return;
      }
      console.log("Message inserted successfully:", result);
    }
  );
  
  
  });
});

// iniciar o servidor
http.listen(3000, function () {
  console.log("Servidor iniciado");
});
