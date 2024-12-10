<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "
    SELECT s.sub_id, s.code, s.description, CONCAT(u.fname, ' ', u.lname) AS instructor_name,
    c.year_level, sec.sections, sem.semesters, ac.advisory_class_id
    FROM class_teacher ct
    JOIN subject s ON ct.sub_id = s.sub_id
    JOIN users u ON ct.user_id = u.user_id
    JOIN advisory_class ac ON ct.advisory_class_id = ac.advisory_class_id
    JOIN class c ON ac.class_id = c.class_id
    JOIN section sec ON c.section_id = sec.section_id
    JOIN semester sem ON ac.sem_id = sem.sem_id

";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../sidebar.css">
    <title>Assign Subjects to Student</title>
    <style>
        form {
            max-width: 500px;
            margin: auto;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        button {
            background-color: #2A2185;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <nav class="topbar">

        <div class="toggle" onclick="toggleSidebar()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" stroke="white"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M9 2V22" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

        </div>

        <div class="user">
            <div class="dropdown">
                <!-- Clickable Image -->
                <button class="dropdown-btn " onclick="toggleUser()">
                    <img src="/img/admin.jpg" alt="User Profile" class="profile-img">
                </button>
                <!-- Dropdown Menu -->
                <div class="dropdown-content" style="display: none;">
                    <div>
                        <a href="#">Manage Account</a>
                    </div>
                    <div>
                        <a href="../logout.php">Logout</a>
                    </div>
                    <!-- PHP to log out user -->
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <?php include '../components/sidebar.php'; ?>
        <div class="main">
            <h1>Assign Subjects to Student</h1>

            <form action="assign_student.php" method="post"
                style="display: flex; flex-direction: column; width: 500px; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

                <!-- Select Student Dropdown -->
                <label for="student" style="font-size: 16px; margin-bottom: 8px;">Select Student:</label>
                <select id="student" name="student"
                    style="padding: 8px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 16px;">
                    <option value="">Select a Student</option>
                    <?php
                    $studentsQuery = "SELECT user_id, CONCAT(fname, ' ', lname) AS student_name FROM users WHERE role = 'Student'";
                    $studentsResult = $conn->query($studentsQuery);
                    while ($student = $studentsResult->fetch_assoc()) {
                        echo "<option value='" . $student['user_id'] . "'>" . htmlspecialchars($student['student_name']) . "</option>";
                    }
                    ?>
                </select>

                <!-- Select Class Dropdown -->
                <label for="subjects" style="font-size: 16px; margin-bottom: 8px;">Select Class:</label>
                <select name="subjects[]" id="subjects" multiple required
                    style="min-height: 200px; padding: 8px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 16px;">
                    <option value="">-- Select Class --</option>
                    <?php
                    // Fetch the subjects, section, year level, and instructor info
                    while ($row = $result->fetch_assoc()) {
                        $optionText = htmlspecialchars($row['year_level']) . " - " . htmlspecialchars($row['sections']) . " - "
                            . htmlspecialchars($row['semesters']) . " - " . htmlspecialchars($row['code'])
                            . " (" . htmlspecialchars($row['instructor_name']) . ")";
                        echo "<option value='" . $row['sub_id'] . "'>" . $optionText . "</option>";
                    }
                    ?>
                </select>

                <!-- Submit Button -->
                <button type="submit"
                    style="padding: 10px; font-size: 16px; background-color: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s;">
                    Assign Subjects
                </button>
            </form>
        </div>
    </div>
    <script src="../js/sidebar.js"></script>
</body>

</html>