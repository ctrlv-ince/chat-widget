<?php
session_start();
include("config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

if (!isset($_GET['conversation_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Conversation ID not provided']);
    exit();
}

$conversation_id = $_GET['conversation_id'];
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Get messages for this conversation
$query = "SELECT * FROM messages 
          WHERE conversation_id = ? 
          ORDER BY sent_at ASC 
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $conversation_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Mark messages as read
$update_query = "UPDATE messages 
                SET is_read = 1 
                WHERE conversation_id = ? AND receiver_id = ? AND is_read = 0";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("ii", $conversation_id, $_SESSION['user_id']);
$update_stmt->execute();

echo json_encode(['status' => 'success', 'messages' => $messages]);
?>