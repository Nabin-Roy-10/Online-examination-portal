<?php
session_start(); // Start the session to check user login status
header('Content-Type: application/json'); // Ensure JSON is being sent

$host = "localhost";
$db = "online_exam2";
$user = "root";
$pass = "";

// Enable error reporting for debugging
ini_set('display_errors', 1); // Set to 1 to display errors during development
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log'); // Adjust the error log path

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

// Handle delete action
if (isset($data['action']) && $data['action'] === 'delete') {
    $question = $data['question'];

    if ($question) {
        try {
            $stmt = $pdo->prepare('DELETE FROM question_set WHERE question = ?');
            $stmt->execute([$question]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Row deleted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete row']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error deleting row: ' . $e->getMessage()]);
        }
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'No question provided']);
        exit;
    }
}

// Retrieve questions based on subject and module number
$subjectName = $data['subject'];
$modNum = $data['modNum'];

if (!$subjectName || !$modNum) {
    echo json_encode(['success' => false, 'message' => 'Subject name or module number is missing']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT sub_name, question, option1, option2, option3, option4, corr_ans FROM question_set q JOIN subjects s ON q.sub_id = s.sub_id WHERE sub_name = ? AND sub_mod_num = ?');
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
