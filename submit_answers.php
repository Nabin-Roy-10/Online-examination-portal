<?php
session_start(); // Start session

header('Content-Type: application/json'); // Ensure JSON is being sent

$host = "localhost";
$db = "online_exam2";
$user = "root";
$pass = "";

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Check if the user is logged in by validating session
if (!isset($_SESSION['sname']) || !isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in']);
    exit;
}

$sname = $_SESSION['sname']; // Retrieve username from session
$email = $_SESSION['email'];

// Retrieve JSON data sent via POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Get subject name and module number from the request
$subjectName = $data['subject'] ?? null;
$modNum = $data['modNum'] ?? null;

if (!$subjectName || !$modNum) {
    echo json_encode(['success' => false, 'message' => 'Subject name or module number is missing']);
    exit;
}

try {
    // Retrieve the user ID based on the session data
    $stmt = $pdo->prepare('SELECT userId FROM signup WHERE username=? AND email=?');
    $stmt->execute([$sname, $email]);
    $userId = $stmt->fetchColumn();

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Invalid user']);
        exit;
    }

    // Log the user's exam activity
    $stmt = $pdo->prepare('INSERT INTO exam_table (userId, sname, examSub, module_num) VALUES (?,?,?,?)');
    $stmt->execute([$userId, $sname, $subjectName, $modNum]);

    // Retrieve questions based on subject and module number
    $stmt = $pdo->prepare('SELECT sub_name, question, option1, option2, option3, option4, corr_ans 
                           FROM question_set q 
                           JOIN subjects s ON q.sub_id = s.sub_id 
                           WHERE sub_name = ? AND sub_mod_num = ?');
    $stmt->execute([$subjectName, $modNum]);
    $questions = $stmt->fetchAll();

    if (count($questions) > 0) {
        echo json_encode(['success' => true, 'data' => $questions]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No questions found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to retrieve questions: ' . $e->getMessage()]);
}
?>
