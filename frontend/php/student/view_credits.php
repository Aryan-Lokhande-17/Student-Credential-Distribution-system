<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Database connection
$db = new PDO('sqlite:users.db');

// Query to get all students
$query = "SELECT * FROM users WHERE user_type = 'student'";
$statement = $db->query($query);
$students = $statement->fetchAll(PDO::FETCH_ASSOC);

// Fetch attendance data for each student
$attendanceData = [];
$query = "SELECT * FROM attendance";
$statement = $db->query($query);
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $attendanceData[$row['student_id']] = [
        'present' => $row['present'],
        'absent' => $row['absent']
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Credits - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .upload-form {
            display: none;
        }
    </style>
    <script>
        let attendanceData = <?php echo json_encode($attendanceData); ?>;

        function showAttendance(studentId) {
            const modal = document.getElementById('attendanceModal');
            const modalContent = document.getElementById('modalContent');

            // Fill the modal with attendance data
            const studentAttendance = attendanceData[studentId];
            if (studentAttendance) {
                modalContent.innerHTML = `
                    <h3>Update Attendance for Student ID: ${studentId}</h3>
                    <label for="present">Present:</label>
                    <input type="number" id="present" value="${studentAttendance.present}">
                    <br>
                    <label for="absent">Absent:</label>
                    <input type="number" id="absent" value="${studentAttendance.absent}">
                    <br><br>
                    <button onclick="saveAttendance(${studentId})">Save</button>
                    <button onclick="closeModal()">Cancel</button>
                `;
            } else {
                modalContent.innerHTML = `
                    <h3>No attendance data found for Student ID: ${studentId}</h3>
                `;
            }

            modal.style.display = 'block';
        }

        function closeModal() {
            const modal = document.getElementById('attendanceModal');
            modal.style.display = 'none';
        }

        function triggerFileUpload(studentId) {
            // Open the file upload form
            const form = document.getElementById('uploadForm-' + studentId);
            form.style.display = 'block';
        }

        function closeUploadForm(studentId) {
            const form = document.getElementById('uploadForm-' + studentId);
            form.style.display = 'none';
        }
    </script>
</head>
<body>

<!-- Container for students list -->
<div class="container">
    <h2>Student List</h2>

    <table class="student-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['username']); ?></td>
                <td>
                    <button onclick="triggerFileUpload(<?php echo $student['id']; ?>)">Upload Result</button>

                    <!-- Hidden upload form -->
                    <div class="upload-form" id="uploadForm-<?php echo $student['id']; ?>">
                        <form action="upload_result.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                            <input type="file" name="result_file" accept=".pdf,.docx,.xlsx" required>
                            <button type="submit">Upload</button>
                            <button type="button" onclick="closeUploadForm(<?php echo $student['id']; ?>)">Cancel</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="admin_landing.php" class="back-button">Back to Dashboard</a>
</div>

<!-- Modal for attendance information -->
<div id="attendanceModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modalContent"></div>
    </div>
</div>

</body>
</html>
