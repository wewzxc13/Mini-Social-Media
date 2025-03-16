<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

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

// Fetch comments
$query = "SELECT c.comment_id, c.comment_text, c.comment_date, u.user_fname, u.user_lname, c.post_id
          FROM tblcomments c
          JOIN tblusers u ON c.user_id = u.user_id
          ORDER BY c.comment_date ASC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    echo json_encode($comments);
} else {
    echo json_encode([]);
}

$conn->close();
?>
