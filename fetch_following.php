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

$data = json_decode(file_get_contents("php://input"));
$user_id = $data->user_id;

$query = "SELECT u.user_id, u.user_fname, u.user_lname, u.user_username
          FROM tblfollows f
          JOIN tblusers u ON f.following_id = u.user_id
          WHERE f.follower_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $following = [];
    while ($row = $result->fetch_assoc()) {
        $following[] = $row;
    }
    echo json_encode(["success" => true, "following" => $following]);
} else {
    echo json_encode(["success" => false, "message" => "No following found."]);
}

$stmt->close();
$conn->close();
?>
