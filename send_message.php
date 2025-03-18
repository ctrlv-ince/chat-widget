<?php
session_start();
include("config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

// Check if all required parameters are provided
if (!isset($_POST['conversation_id']) || !isset($_POST['sender_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit();
}

$conversation_id = $_POST['conversation_id'];
$sender_id = $_POST['sender_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];
$message_type = isset($_POST['message_type']) ? $_POST['message_type'] : 'text';

// Insert message into database
$query = "INSERT INTO messages (conversation_id, sender_id, receiver_id, message, message_type, sent_at) 
          VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiiss", $conversation_id, $sender_id, $receiver_id, $message, $message_type);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message: ' . $conn->error]);
}
?>