<?php
session_start();
include("config.php"); // Include your database configuration

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

if (!isset($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not provided']);
    exit();
}

$user_id = $_GET['user_id'];

// Get all friends assigned to this user's conversations
// Modify this query to match your database structure
$query = "SELECT f.friend_id, f.name, c.conversation_id 
          FROM friends f
          JOIN conversations c ON f.friend_id = c.friend_id
          WHERE c.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = $row;
}

echo json_encode(['status' => 'success', 'friends' => $friends]);
?>