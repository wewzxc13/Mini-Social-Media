<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "db_socmed";

session_start();

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $password = $data['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        exit;
    }

    // Prepare and execute query
    $stmt = $pdo->prepare('SELECT user_id, user_username, user_fname, user_lname, user_birthdate, user_gender, user_password FROM tblusers WHERE user_username = :username');
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['user_password']) {
        // Password is correct
        echo json_encode([
            "success" => true,
            "message" => "Login successful.",
            "user" => [
                "user_id" => $user['user_id'],
                "user_username" => $user['user_username'],
                "user_fname" => $user['user_fname'],
                "user_lname" => $user['user_lname'],
                "user_birthdate" => $user['user_birthdate'],
                "user_gender" => $user['user_gender']
            ]
        ]);
    } else {
        // Invalid username or password
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }

} catch (PDOException $e) {
    // Database error
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
