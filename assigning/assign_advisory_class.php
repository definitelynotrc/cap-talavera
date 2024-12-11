<?php
session_start();

// Database connection
$host = 'localhost'; // Change as needed
$dbname = 'cap'; // Change as needed
$username = 'root'; // Change as needed
$password = ''; // Change as needed

try {
    // Establish PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if (isset($_POST['submit'])) {
        // Retrieve form data
        $class_id = $_POST['class'];
        $sem_id = $_POST['semester'];
        $ay_id = $_POST['acadyear'];

        // Validate inputs
        if (empty($class_id) || empty($sem_id) || empty($ay_id)) {
            die('Please fill out all required fields.');
        }

        // Insert data into the advisory_class table
        $query = "INSERT INTO advisory_class (class_dep_id, ay_id, sem_id, isActive) VALUES (:class_dep_id, :ay_id, :sem_id, 1)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':class_dep_id', $class_id, PDO::PARAM_INT);
        $stmt->bindParam(':ay_id', $ay_id, PDO::PARAM_INT);
        $stmt->bindParam(':sem_id', $sem_id, PDO::PARAM_INT);


        if ($stmt->execute()) {
            // Redirect on success
            header('Location: class_acad_sem.php');
            exit;
        } else {
            die('Failed to assign advisory class.');
        }
    }
} catch (PDOException $e) {
    // Handle database connection or query errors
    die("Database error: " . $e->getMessage());
}
?>