<?php
header('Content-Type: application/json');
include "dbMedsuam.php";

if (!isset($_GET['appointment_id'])) {
    echo json_encode([]);
    exit;
}

$appointment = intval($_GET['appointment_id']);

$stmt = $conn->prepare("SELECT * FROM chat_messages WHERE appointment_id = ? ORDER BY sent_at ASC");
$stmt->bind_param("i", $appointment);
$stmt->execute();

$result = $stmt->get_result();
$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
