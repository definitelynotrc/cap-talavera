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

    $classesQuery = "SELECT class_id, year_level FROM class";  // You should define this query to fetch the `year_level`.
    $classesStmt = $conn->prepare($classesQuery);
    $classesStmt->execute();
    $classes = $classesStmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch sections
    $sectionsQuery = "SELECT section_id, sections FROM section"; // Fetch sections
    $sectionsStmt = $conn->prepare($sectionsQuery);
    $sectionsStmt->execute();
    $sections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch academic years
    $academicYearsQuery = "SELECT ay_id, year_start FROM acad_year"; // Fetch academic years
    $academicYearsStmt = $conn->prepare($academicYearsQuery);
    $academicYearsStmt->execute();
    $academicYears = $academicYearsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch semesters
    $semestersQuery = "SELECT sem_id, semesters FROM semester"; // Fetch semesters
    $semestersStmt = $conn->prepare($semestersQuery);
    $semestersStmt->execute();
    $semesters = $semestersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $class_id = $_POST['class_id'];
        $section_id = $_POST['section_id'];  // Get the section selected
        $ay_id = $_POST['ay_id'];
        $sem_id = $_POST['sem_id'];
        $isActive = 1;

        // Insert into advisory_class table (with class_id, ay_id, sem_id)
        $assignQuery = "
            INSERT INTO advisory_class (class_id, ay_id, sem_id, isActive) 
            VALUES (:class_id, :ay_id, :sem_id, :isActive)
        ";
        $assignStmt = $conn->prepare($assignQuery);
        $assignStmt->execute([
            ':class_id' => $class_id,
            ':ay_id' => $ay_id,
            ':sem_id' => $sem_id,
            ':isActive' => $isActive,
        ]);

        echo "<script>alert('Class assigned successfully!'); window.location.href = '';</script>";
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
    <title>Assign Advisory Class</title>
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
    <h1>Assign Advisory Class</h1>

    <form method="POST">
        <label for="class_id">Select Year Level</label>
        <select name="class_id" id="class_id" required>
            <option value="">-- Select Year Level --</option>
            <!-- Populate this based on your 'class' table year_level -->
            <?php foreach ($classes as $class): ?>
                <option value="<?= htmlspecialchars($class['class_id']) ?>">
                    <?= htmlspecialchars($class['year_level']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="section_id">Select Section</label>
        <select name="section_id" id="section_id" required>
            <option value="">-- Select Section --</option>
            <?php foreach ($sections as $section): ?>
                <option value="<?= htmlspecialchars($section['section_id']) ?>">
                    <?= htmlspecialchars($section['sections']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="ay_id">Select Academic Year</label>
        <select name="ay_id" id="ay_id" required>
            <option value="">-- Select Academic Year --</option>
            <?php foreach ($academicYears as $ay): ?>
                <option value="<?= htmlspecialchars($ay['ay_id']) ?>">
                    <?= htmlspecialchars($ay['year_start']) ?>
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

        <button type="submit">Assign Advisory Class</button>
    </form>
</body>
</html>
