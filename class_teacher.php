<?php
// Database connection details
$host = 'localhost'; // Change to your host
$dbname = 'cap'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch advisory classes
    $advisoryClassesQuery = "
        SELECT ac.advisory_class_id, c.year_level, ay.year_start, s.semesters
        FROM advisory_class ac
        INNER JOIN class c ON ac.class_id = c.class_id
        INNER JOIN acad_year ay ON ac.ay_id = ay.ay_id
        INNER JOIN semester s ON ac.sem_id = s.sem_id
        WHERE ac.isActive = 1
    ";
    $advisoryClassesStmt = $conn->prepare($advisoryClassesQuery);
    $advisoryClassesStmt->execute();
    $advisoryClasses = $advisoryClassesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch users
    $usersQuery = "SELECT user_id, CONCAT(fname, ' ', lname) AS fullname FROM users WHERE role = 'Instructor'";
    $usersStmt = $conn->prepare($usersQuery);
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch subjects
    $subjectsQuery = "SELECT sub_id, subjects FROM subject"; // Replace with your subjects table and columns
    $subjectsStmt = $conn->prepare($subjectsQuery);
    $subjectsStmt->execute();
    $subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Teacher types (e.g., primary, secondary)
    $teacherTypes = ['Primary Teacher', 'Secondary Teacher']; // Adjust as needed

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $advisory_class_id = $_POST['advisory_class_id'];
        $user_id = $_POST['user_id'];
        $sub_id = $_POST['sub_id'];
        $teacher_type = $_POST['teacher_type'];

        // Insert into class_teacher table
        $assignQuery = "
            INSERT INTO class_teacher (advisory_class_id, teacher_type, sub_id, user_id) 
            VALUES (:advisory_class_id, :teacher_type, :sub_id, :user_id)
        ";
        $assignStmt = $conn->prepare($assignQuery);
        $assignStmt->execute([
            ':advisory_class_id' => $advisory_class_id,
            ':teacher_type' => $teacher_type,
            ':sub_id' => $sub_id,
            ':user_id' => $user_id,
        ]);

        echo "<script>alert('Teacher successfully assigned to the advisory class!'); window.location.href = '';</script>";
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
    <title>Assign Teacher to Class</title>
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
        select, button {
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
    <h1>Assign Teacher to Class</h1>

    <form method="POST">
        <label for="advisory_class_id">Select Advisory Class</label>
        <select name="advisory_class_id" id="advisory_class_id" required>
            <option value="">-- Select Advisory Class --</option>
            <?php foreach ($advisoryClasses as $class): ?>
                <option value="<?= htmlspecialchars($class['advisory_class_id']) ?>">
                    <?= htmlspecialchars($class['year_level'] . " - " . $class['year_start'] . " (" . $class['semesters'] . ")") ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="user_id">Select User</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['user_id']) ?>">
                    <?= htmlspecialchars($user['fullname']) ?>
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

        <label for="teacher_type">Select Teacher Type</label>
        <select name="teacher_type" id="teacher_type" required>
            <option value="">-- Select Teacher Type --</option>
            <?php foreach ($teacherTypes as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Assign Teacher</button>
    </form>
</body>
</html>
