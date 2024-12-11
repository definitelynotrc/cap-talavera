<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$host = 'localhost'; // Change to your host
$dbname = 'cap'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

    echo "
<script>alert('Teacher successfully assigned to the advisory class!'); window.location.href = 'class_teacher.php';</script>";
}
?>