<?php
// Allow CORS and set content type to JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Database connection parameters
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_socmed";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? '';
$post_context = $data['post_context'] ?? '';
$post_date = $data['post_date'] ?? '';
$category_id = $data['category_id'] ?? '';

if (empty($user_id) || empty($post_context) || empty($post_date) || empty($category_id)) {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);
    exit();
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO tblposts (user_id, post_context, post_date, category_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $user_id, $post_context, $post_date, $category_id);

// Execute the query
if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Post inserted successfully."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $stmt->error
    ]);
}

// Close connection
$stmt->close();
$conn->close();
?>
