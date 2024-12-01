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

    // Fetch users
    $usersQuery = "SELECT user_id, CONCAT(fname, ' ', lname) AS fullname FROM users WHERE role = 'Instructor'";
    $usersStmt = $conn->prepare($usersQuery);
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

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

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_POST['user_id'];
        $advisory_class_id = $_POST['advisory_class_id'];
        $isActive = 1;

        // Insert into user_class table
        $assignQuery = "
            INSERT INTO user_class (user_id, advisory_class_id, isActive) 
            VALUES (:user_id, :advisory_class_id, :isActive)
        ";
        $assignStmt = $conn->prepare($assignQuery);
        $assignStmt->execute([
            ':user_id' => $user_id,
            ':advisory_class_id' => $advisory_class_id,
            ':isActive' => $isActive,
        ]);

        echo "<script>alert('User successfully assigned to the advisory class!'); window.location.href = '';</script>";
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
    <title>Assign User to Advisory Class</title>
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
    <h1>Assign User to Advisory Class</h1>

    <form method="POST">
        <label for="user_id">Select User</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['user_id']) ?>">
                    <?= htmlspecialchars($user['fullname']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="advisory_class_id">Select Advisory Class</label>
        <select name="advisory_class_id" id="advisory_class_id" required>
            <option value="">-- Select Advisory Class --</option>
            <?php foreach ($advisoryClasses as $class): ?>
                <option value="<?= htmlspecialchars($class['advisory_class_id']) ?>">
                    <?= htmlspecialchars($class['year_level'] . " - " . $class['year_start'] . " (" . $class['semesters'] . ")") ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Assign User</button>
    </form>
</body>
</html>
