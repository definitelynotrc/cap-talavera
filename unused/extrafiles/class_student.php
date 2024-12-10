<?php
// Database connection
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

    // Fetch students
    $studentsQuery = "SELECT user_id, CONCAT(fname, ' ', lname) AS full_name FROM users WHERE role = 'student'";
    $studentsStmt = $conn->prepare($studentsQuery);
    $studentsStmt->execute();
    $students = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission to assign a student to a class based on department-subject pair
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dep_id = $_POST['dep_id'];
        $sub_id = $_POST['sub_id'];
        $advisory_class_id = $_POST['advisory_class_id'];
        $user_id = $_POST['user_id'];

        // Get dep_sub_id by joining dep_id and sub_id
        $getDepSubIdQuery = "SELECT dep_sub_id FROM dep_sub WHERE dep_id = :dep_id AND sub_id = :sub_id";
        $getDepSubIdStmt = $conn->prepare($getDepSubIdQuery);
        $getDepSubIdStmt->execute([
            ':dep_id' => $dep_id,
            ':sub_id' => $sub_id
        ]);
        $depSubId = $getDepSubIdStmt->fetchColumn();

        if ($depSubId) {
            // Insert student into class_student
            $insertClassStudentQuery = "
                INSERT INTO class_student (advisory_class_id, dep_sub_id, user_id) 
                VALUES (:advisory_class_id, :dep_sub_id, :user_id)
            ";
            $insertClassStudentStmt = $conn->prepare($insertClassStudentQuery);
            $insertClassStudentStmt->execute([
                ':advisory_class_id' => $advisory_class_id,
                ':dep_sub_id' => $depSubId,
                ':user_id' => $user_id
            ]);

            echo "<script>alert('Student successfully assigned to class!'); window.location.href = '';</script>";
        } else {
            echo "<script>alert('Invalid department and subject combination.'); window.location.href = '';</script>";
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
    <title>Assign Student to Class</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; margin: auto; }
        label { display: block; margin: 10px 0 5px; font-weight: bold; }
        select, button { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
        button { background-color: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Assign Student to Class</h1>

    <form method="POST">
        <label for="dep_id">Select Department</label>
        <select name="dep_id" id="dep_id" required onchange="fetchSubjects()">
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
        </select>

        <label for="advisory_class_id">Select Advisory Class</label>
        <select name="advisory_class_id" id="advisory_class_id" required>
            <option value="">-- Select Advisory Class --</option>
            <?php foreach ($advisoryClasses as $advisoryClass): ?>
                <option value="<?= htmlspecialchars($advisoryClass['advisory_class_id']) ?>">
                    <?= htmlspecialchars($class['year_level'] . " - " . $class['year_start'] . " (" . $class['semesters'] . ")") ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="user_id">Select Student</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- Select Student --</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= htmlspecialchars($student['user_id']) ?>">
                    <?= htmlspecialchars($student['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Assign Student to Class</button>
    </form>

    <script>
        function fetchSubjects() {
            const dep_id = document.getElementById('dep_id').value;
            const subjectSelect = document.getElementById('sub_id');

            // Clear previous subjects
            subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';

            if (dep_id) {
                // Fetch subjects for the selected department
                fetch('fetch_subjects.php?dep_id=' + dep_id)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.sub_id;
                            option.textContent = subject.subjects;
                            subjectSelect.appendChild(option);
                        });
                    });
            }
        }
    </script>
</body>
</html>
