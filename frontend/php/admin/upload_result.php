<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['result_file']) && isset($_POST['student_id'])) {
    $studentId = $_POST['student_id'];
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['result_file']['name']);

    // Check if the upload directory exists; if not, create it
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['result_file']['tmp_name'], $uploadFile)) {
        echo "File successfully uploaded for Student ID: $studentId";
    } else {
        echo "File upload failed!";
    }
}
?>
