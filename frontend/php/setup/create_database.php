<?php
try {
    // Create a SQLite database (users.db)
    $db = new PDO('sqlite:users.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL,
        user_type TEXT NOT NULL,
        student_id INTEGER NOT NULL,
        student_name TEXT NOT NULL,
        credits INTEGER
    )");

    // Insert sample users (admin and student)
    $stmt = $db->prepare("INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)");
    $stmt->execute(['admin', password_hash('adminpass', PASSWORD_DEFAULT), 'admin']);
    $stmt->execute(['student', password_hash('studentpass', PASSWORD_DEFAULT), 'student']);

    echo "Database setup complete!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
