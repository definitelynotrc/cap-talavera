<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$host = 'localhost';
$dbname = 'cap';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch departments
    $departmentsQuery = "SELECT dep_id, department FROM department";
    $departmentsStmt = $conn->prepare($departmentsQuery);
    $departmentsStmt->execute();
    $departments = $departmentsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch subjects
    $subjectsQuery = "SELECT sub_id, subjects FROM subject"; // Adjust query if necessary
    $subjectsStmt = $conn->prepare($subjectsQuery);
    $subjectsStmt->execute();
    $subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch semesters
    $semestersQuery = "SELECT * FROM semester";
    $semestersStmt = $conn->prepare($semestersQuery);
    $semestersStmt->execute();
    $semesters = $semestersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission to assign a subject to a department and semester
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dep_id = $_POST['dep_id'];
        $sub_id = $_POST['sub_id'];
        $semester_id = $_POST['sem_id'];  // New field for semester selection

        // Check if the assignment already exists
        $checkAssignmentQuery = "SELECT * FROM dep_sub WHERE dep_id = :dep_id AND sub_id = :sub_id AND sem_id = :sem_id";
        $checkAssignmentStmt = $conn->prepare($checkAssignmentQuery);
        $checkAssignmentStmt->execute([
            ':dep_id' => $dep_id,
            ':sub_id' => $sub_id,
            ':sem_id' => $semester_id  // Include semester_id in the check
        ]);

        if ($checkAssignmentStmt->rowCount() > 0) {
            echo "<script>alert('This subject is already assigned to the selected department and semester.'); window.location.href = '';</script>";
        } else {
            // Insert the assignment
            $insertDepSubQuery = "
                INSERT INTO dep_sub (dep_id, sub_id, sem_id) 
                VALUES (:dep_id, :sub_id, :sem_id)
            ";
            $insertDepSubStmt = $conn->prepare($insertDepSubQuery);
            $insertDepSubStmt->execute([
                ':dep_id' => $dep_id,
                ':sub_id' => $sub_id,
                ':sem_id' => $semester_id // Insert semester_id
            ]);

            echo "<script>alert('Subject successfully assigned to department and semester!'); window.location.href = '';</script>";
        }
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Subject to Department</title>
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

        select,
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>Assign Subject to Department</h1>

    <form method="POST">
        <label for="dep_id">Select Department</label>
        <select name="dep_id" id="dep_id" required>
            <option value="">-- Select Department --</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?= htmlspecialchars($department['dep_id']) ?>">
                    <?= htmlspecialchars($department['department']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="sub_id">Select Subject</label>
        <select name="sub_id" id="sub_id" required>
            <option value="">-- Select Subject --</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= htmlspecialchars($subject['sub_id']) ?>">
                    <?= htmlspecialchars($subject['subjects']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="sem_id">Select Semester</label>
        <select name="sem_id" id="sem_id" required>
            <option value="">-- Select Semester --</option>
            <?php foreach ($semesters as $semester): ?>
                <option value="<?= htmlspecialchars($semester['sem_id']) ?>">
                    <?= htmlspecialchars($semester['semesters']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Assign Subject to Department</button>
    </form>
</body>

</html>