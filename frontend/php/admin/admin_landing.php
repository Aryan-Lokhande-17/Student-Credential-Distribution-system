<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS -->
</head>
<body>

<!-- Main container for cards -->
<div class="container">
    <h2>Welcome Admin</h2> <!-- Welcome message -->
    
    <!-- Cards Section -->
    <div class="cards-container">
        <div class="card">
            <h3>Attendance</h3>
            <p>Manage student attendance records.</p>
            <a href="view_attendance.php">View Attendance</a> <!-- Link to view all students -->
        </div>
        <div class="card">
            <h3>Credit</h3>
            <p>Manage student credits and grades.</p>
            <a href="view_credits.php">View Credits</a> <!-- Link to view credits -->
        </div>
    </div>

    <!-- Logout button -->
    <button class="logout-button" onclick="location.href='login.php'">Logout</button>

</div>

</body>
</html>