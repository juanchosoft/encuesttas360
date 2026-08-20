<?php
// Configurar la zona horaria
date_default_timezone_set('America/Bogota');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT Integrado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>

        .chat-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .chat-box {
            height: 400px;
            overflow-y: scroll;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #fff;
        }
        .chat-message {
            margin-bottom: 10px;
        }
        .chat-message.user {
            text-align: right;
            color: #007bff;
        }
        .chat-message.bot {
            text-align: left;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <h3 class="text-center">ChatGPT</h3>
        <div id="chat-box" class="chat-box">
            <!-- Mensajes se renderizan aquí -->
        </div>
        <form id="chat-form">
            <div class="input-group mt-3">
                <input type="text" id="user-input" class="form-control" placeholder="Escribe tu mensaje..." required>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('chat-form');
        const chatBox = document.getElementById('chat-box');

        // Manejar el envío del formulario
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            // Obtener el mensaje del usuario
            const userInput = document.getElementById('user-input').value;

            // Mostrar el mensaje del usuario en el chat
            const userMessage = document.createElement('div');
            userMessage.classList.add('chat-message', 'user');
            userMessage.textContent = `Tú: ${userInput}`;
            chatBox.appendChild(userMessage);

            // Limpiar el campo de entrada
            document.getElementById('user-input').value = '';

            // Hacer la solicitud al backend
            const response = await fetch('chatgpt_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userInput })
            });

            const data = await response.json();

            // Mostrar la respuesta de ChatGPT en el chat
            const botMessage = document.createElement('div');
            botMessage.classList.add('chat-message', 'bot');
            botMessage.textContent = `ChatGPT: ${data.response}`;
            chatBox.appendChild(botMessage);

            // Desplazar el chat al final
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>
</body>
</html>
