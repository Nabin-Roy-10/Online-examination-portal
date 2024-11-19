<?php
session_start();
header('Content-Type: application/json');

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
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if (!isset($_SESSION['sname'])) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Handle update action
if (isset($data['action']) && $data['action'] === 'update') {
    $subject = $data['subject'];
    $question = $data['question'];
    $option1 = $data['option1'];
    $option2 = $data['option2'];
    $option3 = $data['option3'];
    $option4 = $data['option4'];
    $corr_ans = $data['corr_ans'];

    try {
        // First, check if a matching question exists based on question text and subject
        $stmt = $pdo->prepare('
            SELECT q.question
            FROM question_set AS q
            JOIN subjects AS s ON q.sub_id = s.sub_id
            WHERE q.question = ? AND s.sub_name = ?
        ');
        $stmt->execute([$question, $subject]);
        $existingQuestion = $stmt->fetch();

        if ($existingQuestion) {
            // If a matching row is found, proceed with update
            $stmt = $pdo->prepare('
                UPDATE question_set AS q
                JOIN subjects AS s ON q.sub_id = s.sub_id
                SET q.option1 = ?, q.option2 = ?, q.option3 = ?, q.option4 = ?, q.corr_ans = ?
                WHERE q.question = ? AND s.sub_name = ?
            ');
            $stmt->execute([$option1, $option2, $option3, $option4, $corr_ans, $question, $subject]);

            echo json_encode(['success' => true, 'message' => 'Row updated']);
        } else {
            // No matching question found
            echo json_encode(['success' => false, 'message' => 'No matching question found for update']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating row: ' . $e->getMessage()]);
    }
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
