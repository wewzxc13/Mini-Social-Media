<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_socmed";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Decode JSON input
$postData = json_decode(file_get_contents("php://input"), true);

$post_id = $postData['post_id'] ?? '';

if ($post_id) {

      // Prepare and execute the SQL statement to delete the post
      $stmt2 = $conn->prepare("DELETE FROM tblposts WHERE post_id = ?");
      $stmt2->bind_param("i", $post_id);
  
      if ($stmt2->execute()) {
          $conn->commit(); // Commit the transaction if everything is successful
          echo json_encode(['success' => true, 'message' => 'Post and comments deleted successfully']);
      } else {
          $conn->rollback(); // Rollback the transaction on failure
          echo json_encode(['success' => false, 'message' => 'Error deleting post: ' . $stmt2->error]);
      }
  
    // Start a transaction
    $conn->begin_transaction();

    // Prepare and execute the SQL statement to delete comments first
    $stmt1 = $conn->prepare("DELETE FROM tblcomments WHERE post_id = ?");
    $stmt1->bind_param("i", $post_id);
    
    if (!$stmt1->execute()) {
        $conn->rollback(); // Rollback the transaction on failure
        die(json_encode(['success' => false, 'message' => 'Error deleting comments: ' . $stmt1->error]));
    }

  
    $stmt1->close();
    $stmt2->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No post_id provided']);
}

$conn->close();
?>
