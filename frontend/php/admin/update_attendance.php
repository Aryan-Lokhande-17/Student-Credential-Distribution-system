<?php
header('Content-Type: application/json');

// Check if the request is made with POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the JSON data from the body of the request
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Ensure that the necessary data is provided
    if (isset($data['studentId'], $data['present'], $data['absent'])) {
        $studentId = $data['studentId'];
        $present = $data['present'];
        $absent = $data['absent'];

        // Check if the present and absent values are valid numbers (non-negative integers)
        if (!is_int($present) || !is_int($absent) || $present < 0 || $absent < 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid attendance data']);
            exit();
        }

        // Database connection
        $db = new PDO('sqlite:users.db');

        // Check if the student exists in the attendance table
        $checkQuery = "SELECT COUNT(*) FROM attendance WHERE student_id = :student_id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $checkStmt->execute();
        $studentExists = $checkStmt->fetchColumn();

        if ($studentExists) {
            // Update the attendance data for the student
            $query = "UPDATE attendance SET present = :present, absent = :absent WHERE student_id = :student_id";
            $statement = $db->prepare($query);
            $statement->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $statement->bindParam(':present', $present, PDO::PARAM_INT);
            $statement->bindParam(':absent', $absent, PDO::PARAM_INT);

            // Execute the query
            if ($statement->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update attendance']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Student not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
