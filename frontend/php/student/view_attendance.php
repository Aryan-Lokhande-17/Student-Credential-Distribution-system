<?php
// view_attendance.php

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

function calculateAttendancePercentage($present, $absent) {
    $totalClasses = $present + $absent;
    return $totalClasses > 0 ? ($present / $totalClasses * 100) : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance - Admin</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS -->
    <script>
        let attendanceData = <?php echo json_encode($attendanceData); ?>; // Pass PHP array to JS

        document.addEventListener("DOMContentLoaded", function() {
            // Fetch updated attendance data from the server when the page loads
            fetchAttendanceData();
        });

        function fetchAttendanceData() {
            fetch('update_attendance.php')  // A new script to fetch the latest attendance data
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        attendanceData = data.attendanceData;
                        updateAttendanceDisplay();
                    }
                })
                .catch(error => {
                    console.error('Error fetching attendance data:', error);
                });
        }

        function showAttendance(studentId) {
    const modal = document.getElementById("attendanceModal");
    const modalContent = document.getElementById("modalContent");
    
    // Get current attendance data
    const currentData = attendanceData[studentId] || { present: 0, absent: 0 };

    // Calculate the attendance percentage
    const attendancePercentage = calculateAttendancePercentage(currentData.present, currentData.absent);

    // Create modal content dynamically
    modalContent.innerHTML = `
        <h2>Attendance Information for Student ID: ${studentId}</h2>
        <label for="present">Present:</label>
        <input type="number" id="present" value="${currentData.present}" min="0" onchange="updatePercentage(${studentId})">
        <br><br>
        <label for="absent">Absent:</label>
        <input type="number" id="absent" value="${currentData.absent}" min="0" onchange="updatePercentage(${studentId})">
        <br><br>
        <strong>Attendance Percentage: <span id="attendancePercentage">${attendancePercentage.toFixed(2)}%</span></strong>
        <br><br>
        <button onclick="saveAttendance(${studentId})">Save</button>
    `;
    modal.style.display = "block";
}

function saveAttendance(studentId) {
    const present = parseInt(document.getElementById("present").value);
    const absent = parseInt(document.getElementById("absent").value);

    // Update the attendance data in memory
    attendanceData[studentId] = { present: present, absent: absent };

    // Send the updated data to the server for saving in the database
    fetch('update_attendance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ studentId: studentId, present: present, absent: absent })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateAttendanceDisplay();
            alert('Attendance updated successfully!');
            closeModal();
        } else {
            alert('Failed to update attendance.');
        }
    })
    .catch(error => {
        alert('An error occurred while updating attendance.');
    });
}

        function updateAttendanceDisplay() {
            const tableBody = document.querySelector('.student-table tbody');
            tableBody.innerHTML = ''; // Clear the existing rows

            for (const studentId in attendanceData) {
                const currentData = attendanceData[studentId];
                const attendancePercentage = calculateAttendancePercentage(currentData.present, currentData.absent);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${studentId}</td>
                    <td>
                        <a href="javascript:void(0);" onclick="showAttendance(${studentId})">View Attendance</a>
                    </td>
                    <td>${attendancePercentage.toFixed(2)}%</td>
                `;
                tableBody.appendChild(row);
            }
        }

        function updatePercentage(studentId) {
            const present = parseInt(document.getElementById("present").value);
            const absent = parseInt(document.getElementById("absent").value);

            // Recalculate and update the attendance percentage dynamically
            const percentage = calculateAttendancePercentage(present, absent);
            document.getElementById("attendancePercentage").textContent = percentage.toFixed(2) + "%";
        }

        function closeModal() {
            const modal = document.getElementById("attendanceModal");
            modal.style.display = "none";
        }

        function calculateAttendancePercentage(present, absent) {
            const totalClasses = present + absent;
            return totalClasses > 0 ? (present / totalClasses * 100) : 0;
        }
    </script>
</head>
<body>

<!-- Container for students list -->
<div class="container">
    <h2>Student List</h2> <!-- Page Title -->

    <table class="student-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Actions</th>
                <th>Attendance Percentage</th> <!-- New column for attendance percentage -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): 
                $currentData = $attendanceData[$student['id']] ?? ['present' => 0, 'absent' => 0];
                $attendancePercentage = calculateAttendancePercentage($currentData['present'], $currentData['absent']);
            ?>
            <tr>
                <td><?php echo htmlspecialchars($student['username']); ?></td>
                <td>
                    <a href="javascript:void(0);" onclick="showAttendance(<?php echo $student['id']; ?>)">View Attendance</a>
                </td>
                <td><?php echo number_format($attendancePercentage, 2) . '%'; ?></td> <!-- Display attendance percentage -->
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
