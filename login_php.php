<?php
session_start();
header('Content-Type: application/json'); // Set the content type to JSON
ob_start(); // Start output buffering

// Database connection setup
$host = "localhost";
$db = "online_exam2";
$user = "root";
$pass = "";

$dsn = "mysql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    ob_clean(); // Clean the buffer to prevent any previous output
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['password']) || !isset($data['memberType'])) {
    ob_clean(); // Clean buffer before output
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$email = $data['email'];
$password = $data['password'];
$memberType = $data['memberType'];

try {
    // Query to get the user data by email
    $stmt = $pdo->prepare("SELECT * FROM signup WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Using password_verify to check hashed password
        if (password_verify($password, $user['password_hash']) && strcasecmp($user['memberType'], $memberType) === 0) {
            // Set session variables on successful login
            $_SESSION['email'] = $user['email'];
            $_SESSION['memberType'] = $user['memberType'];
            $_SESSION['sname'] = $user['username']; // Setting 'sname' as the username (assuming it's the student name)

            ob_clean(); // Clear the buffer to ensure no other output
            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            ob_clean(); // Clean buffer before output
            echo json_encode(['success' => false, 'message' => 'Invalid password or member type']);
        }
    } else {
        ob_clean(); // Clean buffer before output
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} catch (PDOException $e) {
    ob_clean(); // Clean buffer before output
    echo json_encode(['success' => false, 'message' => 'Login failed: ' . $e->getMessage()]);
}

ob_end_flush(); // Flush the buffer and end output buffering
?>
