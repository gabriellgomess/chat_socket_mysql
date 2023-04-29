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
        var socket = io.connect("http://192.168.100.13:3000");

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
                url: "http://192.168.100.13:3000/get_messages",
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