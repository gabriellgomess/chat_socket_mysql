<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Chat App</title>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;

        }

        .container-fluid {
            height: 100%;
            display: flex;
            flex-direction: row;
            padding: 0 !important;
        }

        .users {
            flex: 1;
            max-width: 300px;
            border-right: 1px solid #ccc;
            padding: 20px;
            overflow-y: auto;
            background: #e0e0e0;
        }

        #users {
            display: flex;
            flex-direction: column;
        }

        .chat {
            flex: 3;
            display: flex;
            flex-direction: column;
            padding: 20px;
            justify-content: space-between;
        }

        .messages {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 0 20px 0 0;
        }

        .send-message {
            background-color: #ccc;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            width: fit-content;
        }

        .recepted-message {
            background-color: #f28b2680;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            width: fit-content;
        }

        .container-send-message {
            display: flex;
            justify-content: flex-end;
        }

        .container-recepted-message {
            display: flex;
            justify-content: flex-start;
        }

        .form-message {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 7px
        }

        .card-hidden {
            display: none;
        }

        .profile {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .card-conversa {
            height: 55px;
            border-radius: 14px;
            background-color: #f28b26;
            background-image: linear-gradient(to right, #f28b26, #e72b4f);
            box-shadow: 5px 5px 11px #bebebe,
                -5px -5px 11px #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            cursor: pointer;
            color: #fff;
            font-weight: bold;
        }

        .btn-send {
            background-color: #e72b4f;
            color: #fff;
        }
        .container-message{
            display: flex;
            flex-direction: column;
        }

        *::-webkit-scrollbar {
            width: 8px;
            /* width of the entire scrollbar */
        }

        *::-webkit-scrollbar-track {
            background: transparent;
            /* color of the tracking area */
        }

        *::-webkit-scrollbar-thumb {
            background-color: #f28b26;
            /* color of the scroll thumb */
            border-radius: 20px;
            /* roundness of the scroll thumb */
            border: none;
            /* creates padding around scroll thumb */
        }
    </style>
</head>

<body>
    <!-- Incluindo bibliotecas -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.socket.io/4.1.2/socket.io.min.js"></script>
    <div class="container-fluid">
        <div class="users">
            <div class="profile">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ccc" class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                </svg>
                <h1 id="my-name"></h1>
            </div>

            <div id="users">
                <div id="user-gabriel" onclick="onUserSelected('gabriel');" class="card-conversa m-3">Gabriel <span id="unread-gabriel" class="badge bg-success"></span></div>
                <div id="user-sara" onclick="onUserSelected('sara');" class="card-conversa m-3">Sara <span id="unread-sara" class="badge bg-success"></span></div>
                <div id="user-mauricio" onclick="onUserSelected('mauricio');" class="card-conversa m-3">Mauricio <span id="unread-mauricio" class="badge bg-success"></span></div>
            </div>
        </div>
        <div class="chat">
            <div style="margin-bottom: 15px;" id="conversationHeader" class="conversationHeader">

            </div>
            <div id="messages" class="messages">

            </div>
            <form class="form-message" onsubmit="return sendMessage();">
                <input id="message" class="form-control" placeholder="Digite sua mensagem...">
                <button type="submit" class="btn btn-send"><i class="fa-regular fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
    <script>
        var urlParams = new URLSearchParams(window.location.search);
        // Definindo as variáveis globais
        var sender;
        var receiver;
        var unreadMessages = {
            gabriel: 0,
            sara: 0,
            mauricio: 0
        };

        // estabelecendo a conexão do socket com o servidor
        var socket = io.connect("http://10.100.10.56:3000");

        function scrollToBottom() {
            var messages = document.getElementById('messages');
            messages.scrollTop = messages.scrollHeight;
        }


        function enterName() {
            // get username from URL parameter using URLSearchParams
            var name = urlParams.get('sender');
            $("#my-name").text(name);
            console.log("SENDER: ", name)
            // check if name is present
            if (!name) {
                alert('Por favor, informe o parâmetro "sender" na URL.');
                return false;
            }
            // send it to server
            setTimeout(() => {
                socket.emit("user_connected", name);
            }, 500);
            // save my name in global variable
            sender = name;
            // prevent the form from submitting
            return false;
        }
        // listen from server
        socket.on("user_connected", function(username) {
            var html = "";
            // // Ativar botões para usuários fixos quando eles se conectarem
            // if (["gabriel", "sara", "mauricio"].includes(username)) {
            //     var button = $("#user-" + username);
            //     button.removeClass("disabled");
            //     button.addClass("btn-primary");


            // }
            console.log("USER CONNECTED: ", username);
        });
        socket.on("user_disconnected", function(username) {
            // Desativar botões para usuários fixos quando eles se desconectarem
            if (["gabriel", "sara", "mauricio"].includes(username)) {
                var button = $("#user-" + username);
                button.addClass("disabled");
                button.removeClass("btn-primary");
                button.removeClass("card-hidden");
            }
            console.log("USER DISCONNECTED: ", username);
        });

        function requestConnectedUsers() {
            socket.emit("get_connected_users");
        }

        function onUserSelected(username) {
            // Redefine o contador de mensagens não lidas para o usuário selecionado
            unreadMessages[username] = 0;
            // Atualiza o indicador de notificação
            $("#unread-" + username).text("");
            // save selected user in global variable
            receiver = username;
            // Limpar a área de mensagens
            $("#messages").html("");
            $("#conversationHeader").html("Você está falando com <span style='font-weight: bold'>" + username + "</span>");
            // call an ajax
            $.ajax({
                url: "http://10.100.10.56:3000/get_messages",
                method: "POST",
                contentType: "application/json", // Adicione esta linha
                data: JSON.stringify({ // Modifique esta linha
                    sender: sender,
                    receiver: receiver
                }),
                success: function(messages) {
                    console.log("RESPONSE...: ", messages);

                    var html = "";
                    for (var a = 0; a < messages.length; a++) {
                        if (messages[a].remetente && messages[a].mensagem) {
                            var messageDateTime = new Date(messages[a].timestamp).toLocaleString();
                            if (messages[a].remetente === sender) {
                                html += `<div class='container-send-message'>
                                            <div class='send-message container-message'>
                                                <span style='font-weight: bold'>Eu</span>
                                                <span>${messages[a].mensagem}</span>
                                                <small style='font-size: 10px; text-align: right'>${messageDateTime}</small>
                                            </div>
                                        </div>`;
                            } else {
                                html += `<div class='container-recepted-message'>
                                            <div class='recepted-message container-message'>
                                                <span style='font-weight: bold'>${messages[a].remetente}</span>
                                                <span>${messages[a].mensagem}</span>
                                                <small style='font-size: 10px; text-align: right'>${messageDateTime}</small>
                                            </div>
                                        </div>`;

                                
                            }
                        }
                    }

                    // append in list
                    $("#messages").append(html);
                    scrollToBottom();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                }
            });
        }


        function sendMessage() {
            // get message
            var message = $("#message").val();
            // send message to server
            
            socket.emit("send_message", {
                sender: sender,
                receiver: receiver,
                message: message,
                datetime: new Date().toLocaleString()
            });
            // append your own message
            var html = "";
            let messageDateTime = new Date().toLocaleString();

            // html += "<div class='container-send-message'><div class='send-message'>Você diz: " + message + "</div></div>";
            html += `<div class='container-send-message'>
                                            <div class='send-message container-message'>
                                                <span style='font-weight: bold'>Eu</span>
                                                <span>${message}</span>
                                                <small style='font-size: 10px; text-align: right'>${messageDateTime}</small>
                                            </div>
                                        </div>`;
            $("#messages").append(html);
            scrollToBottom();
            // prevent form from submitting
            $("#message").val("");
            return false;
        }

        // listen from server
        socket.on("new_message", function(data) {
            console.log("DATA ", data)
            if (data.sender === receiver) {
                var html = "";
                // html += "<div class='container-recepted-message'><div class='recepted-message'>" + data.sender + " diz: " + data.message + "</div></div>";
                html += `<div class='container-recepted-message'>
                                            <div class='recepted-message container-message'>
                                                <span style='font-weight: bold'>${data.sender}</span>
                                                <span>${data.message}</span>
                                                <small style='font-size: 10px; text-align: right'>${data.datetime}</small>
                                            </div>
                                        </div>`;
                $("#messages").append(html);
                scrollToBottom();
            } else {
                // Incrementa o contador de mensagens não lidas
                unreadMessages[data.sender]++;
                // Atualiza o indicador de notificação
                $("#unread-" + data.sender).text(unreadMessages[data.sender]);
            }
        });



        socket.on("connected_users", function(response) {
            response.forEach(function(username) {
                if (["gabriel", "sara", "mauricio"].includes(username)) {
                    var button = $("#user-" + username);
                    button.removeClass("disabled");
                    button.addClass("btn-primary");
                    if (sender == username) {
                        button.addClass("card-hidden");
                    }
                }
            });
        });

        // Adicione esta chamada no final do script
        document.addEventListener("DOMContentLoaded", function() {
            enterName();
            setTimeout(requestConnectedUsers, 1000);
            $("#user-" + sender).hide();
        });
    </script>
</body>

</html>