<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: assets/php/login.php');
    exit();
}

// Database connection
$db = new PDO('sqlite:users.db');

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $credits = $_POST['credits'];

    // Update the credits for the selected student
    $query = "UPDATE users SET credits = :credits WHERE student_id = :student_id AND user_type = 'student'";
    $statement = $db->prepare($query);
    $statement->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $statement->bindParam(':credits', $credits, PDO::PARAM_INT);

    if ($statement->execute()) {
        echo "Credits updated successfully!";
    } else {
        echo "Failed to update credits.";
    }
}
?>
