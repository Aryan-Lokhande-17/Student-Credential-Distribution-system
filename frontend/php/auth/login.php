<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user_type is set in the POST request
    if (isset($_POST['user_type'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user_type = $_POST['user_type'];

        // Connect to SQLite database
        try {
            $db = new PDO('sqlite:users.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare and execute SQL query
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND user_type = ?");
            $stmt->execute([$username, $user_type]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Store session variables
                $_SESSION['username'] = $username;
                $_SESSION['user_type'] = $user_type;
                
                // Redirect based on user type
                if ($user_type == 'admin') {
                    header('Location: admin_landing.php');
                } else {
                    header('Location: student_landing.php');
                }
                exit();
            } else {
                $error_message = "Invalid username or password!";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please select a user type!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets\css\style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="login.php">
        <select name="user_type" required>
                <option value="">Select User Type</option>
                <option value="admin">Admin</option>
                <option value="student">Student</option>
            </select>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php
        if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>
    </div>
</body>
</html>
