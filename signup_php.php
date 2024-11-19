<?php
session_start();  // Start the session

header('Content-Type:application/json');

$host = "localhost";
$db = "online_exam2";
$password = "";
$user = "root";

$dsn = "mysql:host=$host; dbname=$db";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'];
$email = $data['email'];
$memberType = $data['memberType'];
$password = password_hash($data['password'], PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("INSERT INTO signup (username, email, memberType, password_hash) VALUES(?,?,?,?)");
    $stmt->execute([$username, $email, $memberType, $password]);

    // Store user details in session
    $_SESSION['sname'] = $username; // For the student page
    $_SESSION['email'] = $email;

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
}
?>
