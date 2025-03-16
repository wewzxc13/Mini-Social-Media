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

// Fetch posts along with the like count
$query = "SELECT p.*, u.user_fname, u.user_lname, u.user_username, 
                 c.category_desc, p.post_likes, p.post_comments
          FROM tblposts p
          JOIN tblusers u ON p.user_id = u.user_id
          LEFT JOIN tblcategory c ON p.category_id = c.category_id
          ORDER BY p.post_date DESC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    echo json_encode($posts);
} else {
    echo json_encode([]);
}

$conn->close();
?>
