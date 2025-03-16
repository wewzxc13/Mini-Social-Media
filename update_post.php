<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_socmed";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the raw POST data
$postData = file_get_contents("php://input");
$data = json_decode($postData, true);

$post_id = $data['post_id'] ?? '';
$post_context = $data['post_context'] ?? '';
$category_id = $data['category_id'] ?? '';
$post_date = $data['post_date'] ?? '';

if ($post_id && $post_context && $category_id && $post_date) {
    // Update post with new content, category, and date
    $sql = "UPDATE tblposts SET post_context='$post_context', category_id='$category_id', post_date='$post_date' WHERE post_id='$post_id'";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating post: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Incomplete data provided']);
}

$conn->close();
?>
