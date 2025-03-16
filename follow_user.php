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

// Get the user_id and friend_id from the request
$data = json_decode(file_get_contents("php://input"));
$user_id = $data->user_id;
$friend_id = $data->friend_id;

if (!$user_id || !$friend_id) {
    echo json_encode([
        "success" => false,
        "message" => "User ID and Friend ID are required."
    ]);
    exit();
}

// Check if the follow relationship already exists
$checkQuery = "SELECT * FROM tblfollows WHERE follower_id = ? AND following_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $user_id, $friend_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "You are already following this user."
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

// Insert the new follow record
$query = "INSERT INTO tblfollows (follower_id, following_id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $friend_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Follow added successfully."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to add follow."
    ]);
}

$stmt->close();
$conn->close();
?>
