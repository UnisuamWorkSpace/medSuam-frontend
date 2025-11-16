<?php
session_start();
include "dbMedsuam.php";

header("Content-Type: text/plain");

// 1. Check if POST arrived
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "ERROR: Not POST";
    exit;
}

// 2. Dump all POST data
echo "POST RECEIVED:\n";
print_r($_POST);

// 3. Validate fields
if (!isset($_POST['sender_id'], $_POST['receiver_id'], $_POST['appointment_id'], $_POST['message'])) {
    echo "\nERROR: Missing fields";
    exit;
}

// 4. Clear variables
$sender = intval($_POST['sender_id']);
$receiver = intval($_POST['receiver_id']);
$appointment = intval($_POST['appointment_id']);
$message = trim($_POST['message']);

echo "\nParsed values:\n";
echo "sender = $sender\n";
echo "receiver = $receiver\n";
echo "appointment = $appointment\n";
echo "message = $message\n";

// 5. Try to insert
$stmt = $conn->prepare("
    INSERT INTO chat_messages (sender_id, receiver_id, appointment_id, message)
    VALUES (?, ?, ?, ?)
");

if (!$stmt) {
    echo "\nPrepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param("iiis", $sender, $receiver, $appointment, $message);

if ($stmt->execute()) {
    echo "\nRESULT: OK";
} else {
    echo "\nRESULT: ERROR: " . $stmt->error;
}
