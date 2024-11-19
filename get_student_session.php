<?php
session_start(); // Start the session

// Check if session variables are set
if (isset($_SESSION['sname']) && isset($_SESSION['email'])) {
    echo json_encode(['success' => true, 'sname' => $_SESSION['sname'], 'email' => $_SESSION['email']]);
} else {
    echo json_encode(['success' => false, 'message' => 'User session not available']);
}
?>
