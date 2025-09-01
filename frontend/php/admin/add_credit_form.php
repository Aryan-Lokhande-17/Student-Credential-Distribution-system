<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Database connection
$db = new PDO('sqlite:users.db');

// Fetch all students from the users table
$query = "SELECT student_id, student_name FROM users WHERE user_type = 'student'";
$statement = $db->query($query);
$students = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Credits</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Link to external CSS -->
</head>
<body>

<div class="container">
    <h2>Add Credits for Student</h2>
    
    <form action="add_credit.php" method="POST">
        <label for="student_id">Select Student:</label>
        <select id="student_id" name="student_id" required>
            <option value="">Select a student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?php echo htmlspecialchars($student['student_id']); ?>">
                    <?php echo htmlspecialchars($student['student_name']); ?> (ID: <?php echo htmlspecialchars($student['student_id']); ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        
        <label for="credits">Credits Earned:</label>
        <input type="number" id="credits" name="credits" required min="0">
        <br><br>

        <button type="submit">Add Credit</button>
    </form>

    <a href="admin_landing.php" class="back-button">Back to Dashboard</a>
</div>

</body>
</html>
