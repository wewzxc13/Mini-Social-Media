<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_socmed";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? '';
$post_id = $data['post_id'] ?? '';
$comment_text = $data['comment_text'] ?? '';
$comment_date = $data['comment_date'] ?? '';

if (empty($user_id) || empty($post_id) || empty($comment_text) || empty($comment_date)) {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO tblcomments (user_id, post_id, comment_text, comment_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $user_id, $post_id, $comment_text, $comment_date);

if ($stmt->execute()) {
    $comment_id = $stmt->insert_id;
    // Update post_comments count
    $updateStmt = $conn->prepare("UPDATE tblposts SET post_comments = post_comments + 1 WHERE post_id = ?");
    $updateStmt->bind_param("i", $post_id);
    $updateStmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Comment inserted successfully.",
        "comment_id" => $comment_id
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
