<?php
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

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $data['user_id'] ?? '';
    $post_id = $data['post_id'] ?? '';

    // Validate input
    if (!$user_id || !$post_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid input'
        ]);
        exit();
    }

    // Check if like already exists
    $checkQuery = "SELECT * FROM tbllikes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Like exists, so toggle (remove like)
        $deleteQuery = "DELETE FROM tbllikes WHERE post_id = ? AND user_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ii", $post_id, $user_id);
        if (!$stmt->execute()) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete like'
            ]);
            exit();
        }

        // Decrement like count
        $updateQuery = "UPDATE tblposts SET post_likes = post_likes - 1 WHERE post_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $post_id);
        if (!$stmt->execute()) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update post likes'
            ]);
            exit();
        }
    } else {
        // Like does not exist, so add it
        $insertQuery = "INSERT INTO tbllikes (post_id, user_id, like_status) VALUES (?, ?, 'Liked')";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $post_id, $user_id);
        if (!$stmt->execute()) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to insert like'
            ]);
            exit();
        }

        // Increment like count
        $updateQuery = "UPDATE tblposts SET post_likes = post_likes + 1 WHERE post_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $post_id);
        if (!$stmt->execute()) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update post likes'
            ]);
            exit();
        }
    }

    // Fetch the updated like count
    $countQuery = "SELECT post_likes FROM tblposts WHERE post_id = ?";
    $stmt = $conn->prepare($countQuery);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $countResult = $stmt->get_result();
    $likeCount = $countResult->fetch_assoc()['post_likes'];

    // Return the updated like count
    echo json_encode([
        'success' => true,
        'new_like_count' => $likeCount
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
