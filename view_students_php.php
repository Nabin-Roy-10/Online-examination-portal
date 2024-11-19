<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$db = "online_exam2"; // Change this to your database name
$user = "root";       // Your database username
$pass = "";           // Your database password

$dsn = "mysql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT userId, username, email, memberType, created_at, updated_at FROM signup WHERE memberType = "student"'); // Adjust this to your actual column names
    $stmt->execute();
    $teachers = $stmt->fetchAll();

    echo json_encode(['success' => true, 'teachers' => $teachers]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch teachers: ' . $e->getMessage()]);
}
?>
