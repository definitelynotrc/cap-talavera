<?php
session_start();




$userid = $_SESSION['user_id']; // Store the user ID for potential use

$host = 'localhost'; // Change to your host
$dbname = 'cap'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['submit'])) {
        $class = $_POST['section'] ?? null; // Assuming section corresponds to class_id
        $department = $_POST['department'] ?? null;

        // Validate input
        if (empty($class) || empty($department)) {
            echo "<script>alert('Class and Department are required fields.'); window.location.href = '';</script>";
            exit;
        }

        // Check for duplicate entries
        $checkQuery = "SELECT COUNT(*) FROM class_dep WHERE class_id = :class AND dep_id = :department";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':class', $class);
        $checkStmt->bindParam(':department', $department);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            // Entry already exists
            echo "<script>alert('This Class and Department combination already exists.'); window.location.href = '';</script>";
        } else {
            // Insert data into the database
            $query = "INSERT INTO class_dep (class_id, dep_id) VALUES (:class, :department)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':class', $class);
            $stmt->bindParam(':department', $department);

            if ($stmt->execute()) {
                echo "<script>alert('Class and Department assigned successfully!'); window.location.href = 'class_department.php';</script>";
            } else {
                echo "<script>alert('Error occurred while assigning the Class and Department.'); window.location.href = 'class_department.php';</script>";
            }
        }

    }
} catch (PDOException $e) {
    // Handle database connection or query errors
    die("Database error: " . $e->getMessage());
}
?>