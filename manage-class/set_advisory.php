<?php
session_start();
$userid = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
$host = 'localhost'; // Change to your host
$dbname = 'cap'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $ay_id = $_POST['ay_id'];
    $sem_id = $_POST['sem_id'];
    $date_assigned = date('Y-m-d');
    $isActive = 1;

    // Insert assignment into advisory_class table
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

    echo "<script>alert('Advisory Class assigned successfully!'); window.location.href = 'manage_instructor_class.php';</script>";
}

?>