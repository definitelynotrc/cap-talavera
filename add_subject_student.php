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
    SELECT s.sub_id, s.code, s.description, CONCAT(u.fname, ' ', u.lname) AS instructor_name 
    FROM subject s
    JOIN class_teacher ct ON s.sub_id = ct.sub_id
    JOIN users u ON ct.user_id = u.user_id
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Subjects to Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

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
    <h1>Assign Subjects to Student</h1>

    <form action="assign_subjects.php" method="post">

        <label for="student">Select Student:</label>
        <select id="student" name="student">
            <option value="">Select a Student</option>
            <?php

            $studentsQuery = "SELECT user_id, CONCAT(fname, ' ', lname) AS student_name FROM users WHERE role = 'Student'";
            $studentsResult = $conn->query($studentsQuery);
            while ($student = $studentsResult->fetch_assoc()) {
                echo "<option value='" . $student['user_id'] . "'>" . htmlspecialchars($student['student_name']) . "</option>";
            }
            ?>
        </select>


        <label for="subjects">Select Subjects:</label>
        <div id="subjectList">
            <?php
            // Fetch the subjects and their corresponding instructors
            while ($row = $result->fetch_assoc()) {
                echo "<label>";
                echo "<input type='checkbox' name='subjects[]' value='" . $row['sub_id'] . "'>";
                echo htmlspecialchars($row['code']) . " - " . htmlspecialchars($row['description']) . " (Instructor: " . htmlspecialchars($row['instructor_name']) . ")";
                echo "</label><br>";
            }
            ?>
        </div>

        <button type="submit">Assign Subjects</button>
    </form>
</body>

</html>