<?php
// student_landing.php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the same CSS as admin -->
</head>
<body>

<!-- Main container for cards -->
<div class="container">
    <h2>Welcome, Student <?php echo $_SESSION['username']; ?></h2> <!-- Welcome message -->
    
    <!-- Cards Section -->
    <div class="cards-container">
        <div class="card">
            <h3>My Attendance</h3>
            <p>View your attendance records.</p>
            <a href="student_view_attendance.php">View Attendance</a> <!-- Link to view student's attendance -->
        </div>
        <div class="card">
            <h3>My Grades</h3>
            <p>View your credits and grades.</p>
            <a href="student_view_grades.php">View Grades</a> <!-- Link to view student's grades -->
        </div>
        <div class="card">
            <h3>My Profile</h3>
            <p>Manage and update your profile.</p>
            <a href="student_profile.php">View Profile</a> <!-- Link to student's profile -->
        </div>
    </div>

    <!-- Logout button -->
    <button class="logout-button" onclick="location.href='logout.php'">Logout</button>
</div>

</body>
</html>
