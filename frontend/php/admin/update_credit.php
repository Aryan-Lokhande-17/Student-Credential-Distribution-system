<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

try {
    // Database connection
    $db = new PDO('sqlite:users.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the student ID and credits from the form
        $studentId = $_POST['student_id'];
        $credits = $_POST['credits'];

        // Check if the student already exists in the credits table
        $query = "SELECT * FROM credits WHERE student_id = :student_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $existingStudent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingStudent) {
            // Update the existing student's credits
            $updateQuery = "UPDATE credits SET credits = :credits WHERE student_id = :student_id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $updateStmt->bindParam(':credits', $credits, PDO::PARAM_INT);
            $updateStmt->execute();
            echo "Credits updated successfully!";
        } else {
            // Insert a new record for the student
            $insertQuery = "INSERT INTO credits (student_id, student_name, credits) 
                            SELECT student_id, student_name, :credits 
                            FROM users WHERE student_id = :student_id AND user_type = 'student'";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $insertStmt->bindParam(':credits', $credits, PDO::PARAM_INT);
            $insertStmt->execute();
            echo "Credits added successfully!";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
