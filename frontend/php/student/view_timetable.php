<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Database connection (example)
$db = new PDO('sqlite:users.db');

// Query to fetch student timetable data (modify as per your table structure)
$query = "SELECT * FROM timetable";  // Assuming a table 'timetable' exists
$statement = $db->query($query);
$timetableData = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Timetable</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS -->
</head>
<body>

<!-- Timetable List Container -->
<div class="container">
    <h2>Student Timetable</h2>
    
    <table class="timetable-table">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Subject</th>
                <th>Day</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timetableData as $timetable): ?>
            <tr>
                <td><?php echo htmlspecialchars($timetable['student_id']); ?></td>
                <td><?php echo htmlspecialchars($timetable['subject']); ?></td>
                <td><?php echo htmlspecialchars($timetable['day']); ?></td>
                <td><?php echo htmlspecialchars($timetable['time']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="admin_landing.php" class="back-button">Back to Dashboard</a>
</div>

</body>
</html>
