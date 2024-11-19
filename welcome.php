<?php
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['email'])) {
    echo "Welcome, " . $_SESSION['username'] . "!";
    echo "<br>Your email: " . $_SESSION['email'];
} else {
    echo "Please sign up first.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
</head>
<body>
    <a href="landing.php">click it</a>
</body>
</html>