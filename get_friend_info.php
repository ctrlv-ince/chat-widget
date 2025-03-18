<?php
session_start();
include("config.php"); // Include your database configuration

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

if (!isset($_GET['friend_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Friend ID not provided']);
    exit();
}

$friend_id = $_GET['friend_id'];

// Get friend information
// Modify this query to match your database structure
$query = "SELECT friend_id, name, email, phone FROM friends WHERE friend_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $friend_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Friend not found']);
    exit();
}

$friend = $result->fetch_assoc();
echo json_encode(['status' => 'success', 'friend' => $friend]);
?>