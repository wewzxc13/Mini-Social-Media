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

$query = "SELECT user_id, user_fname, user_lname, user_username 
          FROM tblusers 
          WHERE user_id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $suggested_friends = [];
    while ($row = $result->fetch_assoc()) {
        $suggested_friends[] = $row;
    }
    echo json_encode(["success" => true, "suggested_friends" => $suggested_friends]);
} else {
    echo json_encode(["success" => false, "message" => "No suggested friends found."]);
}

$stmt->close();
$conn->close();
?>
