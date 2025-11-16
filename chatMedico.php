<?php
session_start();
include "dbMedsuam.php";

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['consulta'])) {

        $consulta = mysqli_real_escape_string($conn, $_POST['consulta']);
        $idpaciente = mysqli_real_escape_string($conn, $_POST['paciente']);


    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-…(hash)…" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Dots:wght@400..700&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>
    <aside>
        <div class="background">
        <ul>
            <li>
                <div class="menuIconContainer">
                <a href="#"><img class="logo" src="./images/Logo_medsuam-removebg-preview (1).png" alt="logo"/></a>
                
                </div>
            </li>
            
        </ul>
        </div>
        
        
    </aside>
    <main>
        <div id="chatBox" ></div>
        <div class="inputBox">
           
                <input type="text" id="message" placeholder="Digite sua mensagem...">
                <button id="sendBtn"><i class="bi bi-send-arrow-up-fill"></i></button>
            
        </div>
    </main>
</body>
</html>
<script>
const sender_id = <?php echo json_encode($_SESSION['id_medico'] ?? 1); ?>;
const receiver_id = <?php echo json_encode($paciente?? 1);?>;
const appointment_id = <?php echo json_encode($consulta ?? 1);?>;

setInterval(loadMessages, 2000);

function loadMessages() {
    fetch("http://localhost/medSuam-frontend/getMessages.php?appointment_id=" + appointment_id)
    .then(r => r.json())
    .then(data => {
        let chatBox = document.getElementById("chatBox");
        chatBox.innerHTML = "";

        data.forEach(msg => {
            const isMe = msg.sender_id == sender_id;
            chatBox.innerHTML += `
                <div class="messageContainer ${isMe ? "right" : "left"}">
                    
                    <strong>${isMe ? "Você" : "Paciente"}:</strong> ${msg.message}
                    
                </div>
            `;
        });

        chatBox.scrollTop = chatBox.scrollHeight;
    });
}

function sendMessage() {
    let text = document.getElementById("message").value;

    const formData = new FormData();
    formData.append("sender_id", sender_id);
    formData.append("receiver_id", receiver_id);
    formData.append("appointment_id", appointment_id);
    formData.append("message", text);

    fetch("http://localhost/medSuam-frontend/sendMessages.php", {
    method: "POST",
    body: formData
})
.then(r => r.text())
.then(t => {
    console.log("SERVER RESPONSE:", t);
    loadMessages();
});

}


document.getElementById("sendBtn").onclick = sendMessage;
</script>
