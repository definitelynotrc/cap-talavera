<?php
session_start();
$userid = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

// Establish connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$query = "
    SELECT 
        ac.advisory_class_id,
        cl.year_level,
        sec.sections,
        sem.semesters,
       dep.department,
        CONCAT(ay.year_start, '-', ay.year_end) AS academic_year,
        CONCAT(u.fname, ' ', u.lname) AS instructor_name
    FROM 
        advisory_class ac
    JOIN 
        class_dep c ON ac.class_dep_id = c.class_dep_id
        JOIN department dep ON c.dep_id = dep.dep_id
    JOIN 
        class cl ON c.class_id = cl.class_id
    JOIN 
        section sec ON cl.section_id = sec.section_id
    JOIN 
        semester sem ON ac.sem_id = sem.sem_id
    JOIN 
        acad_year ay ON ac.ay_id = ay.ay_id
    JOIN 
        class_teacher ct ON ac.advisory_class_id = ct.advisory_class_id
    JOIN 
        users u ON ct.user_id = u.user_id
    ORDER BY 
        ac.advisory_class_id, u.lname;
";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->execute();

// Fetch the result
$result = $stmt->get_result();
$advisoryClasses = [];
while ($row = $result->fetch_assoc()) {
    $classKey = $row['advisory_class_id'];
    $className = $row['department'] . '-' . $row['year_level'] . '' . $row['sections'] . ' - ' . $row['semesters'] . ' - SY: ' . $row['academic_year'];
    $advisoryClasses[$classKey]['name'] = $className;
    $advisoryClasses[$classKey]['instructors'][] = $row['instructor_name'];
}


// Close the statement and connection


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
            <h1>Assign Student to a class </h1>
            <div>
                <form action="assign_student.php" method="post"
                    style="display: flex; flex-direction: column; width: 500px; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">


                    <label for="student" style="font-size: 16px; margin-bottom: 8px;">Select Student:</label>
                    <select id="student" name="student"
                        style="padding: 8px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 16px;">
                        <option value="">Select a Student</option>
                        <?php

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        } else {
                            echo "<!-- Database connected successfully -->";
                        }

                        // Debug: Check if the query executes
                        $studentsQuery = "SELECT user_id, CONCAT(fname, ' ', lname) AS student_name FROM users WHERE role = 'Student'";
                        $studentsResult = $conn->query($studentsQuery);

                        if ($studentsResult) {
                            while ($student = $studentsResult->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($student['user_id']) . "'>"
                                    . htmlspecialchars($student['student_name']) . "</option>";
                            }
                        } else {
                            echo "<!-- Query Error: " . $conn->error . " -->";
                        }
                        ?>
                    </select>


                    <label for="advisoryClasses">Select Advisory Class:</label>
                    <select name="advisory_class" id="advisoryClasses"
                        style="width: 100%; padding: 10px; font-size: 14px;">
                        <option value="">-- Select Advisory Class --</option>
                        <?php foreach ($advisoryClasses as $classId => $classInfo): ?>
                            <option value="<?= htmlspecialchars($classId) ?>">
                                <?= htmlspecialchars($classInfo['name']) ?>

                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Submit Button -->
                    <button type="submit"
                        style="margin-top: 10px;  padding: 10px; font-size: 16px; background-color: #2A2185; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s;">
                        Assign Class
                    </button>
                </form>
            </div>
            <div>
                <?php

                $studentClassQuery = "
       SELECT 
    s.fname AS student_fname,
    s.lname AS student_lname,
    department.department,
    class.year_level,
    section.sections,
    semester.semesters,
    acad_year.year_start,
    acad_year.year_end,
    GROUP_CONCAT(DISTINCT CONCAT(t.fname, ' ', t.lname) SEPARATOR ', ') AS instructor_names
FROM user_class
JOIN users s ON user_class.user_id = s.user_id
JOIN advisory_class ON user_class.advisory_class_id = advisory_class.advisory_class_id
JOIN class_dep ON advisory_class.class_dep_id = class_dep.class_dep_id
JOIN class ON class_dep.class_id = class.class_id
JOIN section ON class.section_id = section.section_id
JOIN department ON class_dep.dep_id = department.dep_id
JOIN semester ON advisory_class.sem_id = semester.sem_id
JOIN acad_year ON advisory_class.ay_id = acad_year.ay_id
JOIN class_teacher ON advisory_class.advisory_class_id = class_teacher.advisory_class_id
JOIN users t ON class_teacher.user_id = t.user_id
WHERE s.role = 'Student'
GROUP BY 
    s.user_id, 
    department.department, 
    class.year_level, 
    section.sections, 
    semester.semesters, 
    acad_year.year_start, 
    acad_year.year_end
ORDER BY 
    s.lname, 
    s.fname, 
    department.department, 
    class.year_level, 
    section.sections, 
    semester.semesters, 
    acad_year.year_start, 
    acad_year.year_end;

    ";

                $stmt = $conn->prepare($studentClassQuery);
                $stmt->execute();
                $studentClassResult = $stmt->get_result();


                ?>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #2A2185;">
                    <thead style="background-color: #2A2185; color: white;">
                        <tr>
                            <th style="padding: 8px; text-align: left;">Student Name</th>
                            <th style="padding: 8px; text-align: left;">Department</th>
                            <th style="padding: 8px; text-align: left;">Year Level</th>
                            <th style="padding: 8px; text-align: left;">Section</th>
                            <th style="padding: 8px; text-align: left;">Semester</th>
                            <th style="padding: 8px; text-align: left;">Academic Year</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($studentClassResult) {
                            while ($studentClass = $studentClassResult->fetch_assoc()) {
                                echo "<tr>";
                                echo ' <td style="padding: 8px;">' . htmlspecialchars($studentClass['student_fname']) . ' ' . htmlspecialchars($studentClass['student_lname']) . "</td>";
                                echo ' <td style="padding: 8px;">' . htmlspecialchars($studentClass['department']) . "</td>";
                                echo ' <td style="padding: 8px;">' . htmlspecialchars($studentClass['year_level']) . "</td>";
                                echo ' <td style="padding: 8px;">' . htmlspecialchars($studentClass['sections']) . "</td>";
                                echo ' <td style="padding: 8px;">' . htmlspecialchars($studentClass['semesters']) . "</td>";
                                echo ' <td style="padding: 8px;">' . htmlspecialchars($studentClass['year_start']) . ' - ' . htmlspecialchars($studentClass['year_end']) . "</td>";


                                echo "</tr>";
                            }
                        } else {
                            echo "<!-- Query Error: " . $conn->error . " -->";
                        }
                        ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <script src="../js/sidebar.js"></script>
</body>

</html>