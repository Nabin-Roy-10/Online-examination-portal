<?php
session_start();
header('Content-Type: application/json');
$host = "localhost";
$db = "online_exam2";
$user = "root";
$pass = "";

// Enable error reporting for debugging (log errors to a file instead of displaying them)
ini_set('display_errors', 0); // Disable display of errors in output
ini_set('log_errors', 1);     // Enable error logging
ini_set('error_log', '/path/to/php-error.log'); // Log errors to a file

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

// Start output buffering to capture unexpected output
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['sname'])) { // Updated to check the correct session variable
    echo json_encode(['success' => false, 'message' => 'User is not logged in']);
    exit;
}


$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

$subjectName = $data['subject'];
$modNum = $data['modNum'];
$Questions = $data['question'];
$option1 = $data['option1'];
$option2 = $data['option2'];
$option3 = $data['option3'];
$option4 = $data['option4'];
$currectAnswer = $data['curr_ans'];

if (!$subjectName || !$modNum) {
    echo json_encode(['success' => false, 'message' => 'Subject name or module number is missing']);
    exit;
}

try {
    // Insert the subject into the 'subjects' table
    $stmt = $pdo->prepare('INSERT INTO subjects (sub_name, sub_mod_num) VALUES (?, ?)');
    $stmt->execute([$subjectName, $modNum]);

    // Retrieve the subject ID
    $stmt = $pdo->prepare('SELECT sub_id FROM subjects WHERE sub_name = ?');
    $stmt->execute([$subjectName]);
    $subject = $stmt->fetch();

    if ($subject) {
        $sub_id = $subject['sub_id'];

        // Insert the question and options into the 'question_set' table
        $stmt2 = $pdo->prepare('INSERT INTO question_set (question, option1, option2, option3, option4, corr_ans, sub_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt2->execute([$Questions, $option1, $option2, $option3, $option4, $currectAnswer, $sub_id]);

        // Clear the output buffer and return JSON success message
        ob_end_clean();
        echo json_encode(['success' => true, 'message' => 'Question uploaded successfully']);
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve subject ID']);
    }
} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Failed to upload question: ' . $e->getMessage()]);
}
?>
