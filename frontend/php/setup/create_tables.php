<?php
// Create or open the SQLite database
$db = new PDO('sqlite:users.db');

// Create the users table with additional columns
$createUsersTableQuery = "
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    user_type TEXT NOT NULL,
    student_name TEXT,       -- Add student name column
    credits INTEGER DEFAULT 0 -- Add credits column
);";

$db->exec($createUsersTableQuery);

// Create the attendance table
$createAttendanceTableQuery = "
CREATE TABLE IF NOT EXISTS attendance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER UNIQUE,
    present INTEGER DEFAULT 0,
    absent INTEGER DEFAULT 0,
    FOREIGN KEY (student_id) REFERENCES users(id)
);";

// Create the credits table
$createCreditsTableQuery = "
CREATE TABLE IF NOT EXISTS credits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER UNIQUE,
    credits INTEGER DEFAULT 0,
    FOREIGN KEY (student_id) REFERENCES users(id)
);";

$db->exec($createCreditsTableQuery);

$db->exec($createAttendanceTableQuery);

// Close the database connection
$db = null;

echo "Tables created and modified successfully.";

?>