<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Retrieve user_id from query parameters
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_socmed";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Prepare and execute SQL statement
$sql = "SELECT * FROM tblposts WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if posts are found
if ($result->num_rows > 0) {
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    echo json_encode(['success' => true, 'posts' => $posts]);
} else {
    echo json_encode(['success' => true, 'posts' => [], 'message' => 'No posts found.']);
}

// Close resources
$stmt->close();
$conn->close();
?>
