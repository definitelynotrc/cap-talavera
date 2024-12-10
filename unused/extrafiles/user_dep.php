<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Database connection details
$host = 'localhost'; // Change to your host
$dbname = 'cap'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch instructors (users with role "Instructor")
    $instructorsQuery = "SELECT user_id, CONCAT(fname, ' ', lname) AS fullname FROM users WHERE user_id NOT IN (SELECT user_id FROM user_dep WHERE isActive = 1) ";
    $instructorsStmt = $conn->prepare($instructorsQuery);
    $instructorsStmt->execute();
    $instructors = $instructorsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch departments
    $departmentsQuery = "SELECT dep_id, department FROM department";
    $departmentsStmt = $conn->prepare($departmentsQuery);
    $departmentsStmt->execute();
    $departments = $departmentsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_POST['user_id'];
        $dep_id = $_POST['dep_id'];
        $date_assigned = date('Y-m-d');
        $isActive = 1;

        // Insert assignment into user_dep table
        $assignQuery = "INSERT INTO user_dep (user_id, dep_id, isActive, date_assigned) VALUES (:user_id, :dep_id, :isActive, :date_assigned)";
        $assignStmt = $conn->prepare($assignQuery);
        $assignStmt->execute([
            ':user_id' => $user_id,
            ':dep_id' => $dep_id,
            ':isActive' => $isActive,
            ':date_assigned' => $date_assigned,
        ]);

        echo "<script>alert('Department assigned successfully!'); window.location.href = '';</script>";
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
    <title>Assign Department to Instructor</title>
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
    <h1>Assign Department to Users</h1>

    <form method="POST">
        <label for="user_id">Select Users</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- Select Users --</option>
            <?php foreach ($instructors as $instructor): ?>
                <option value="<?= htmlspecialchars($instructor['user_id']) ?>">
                    <?= htmlspecialchars($instructor['fullname']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="dep_id">Select Department</label>
        <select name="dep_id" id="dep_id" required>
            <option value="">-- Select Department --</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?= htmlspecialchars($department['dep_id']) ?>">
                    <?= htmlspecialchars($department['department']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Assign Department</button>
    </form>
</body>

</html>