<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student'] ?? null;
    $advisoryClassId = $_POST['advisory_class'] ?? null;

    // Validate inputs
    if (!empty($studentId) && !empty($advisoryClassId)) {
        // Insert into user_class table
        $query = "INSERT INTO user_class (user_id, advisory_class_id, isActive) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ii", $studentId, $advisoryClassId);
            if ($stmt->execute()) {
                echo "<script>alert('Class assigned successfully!'); window.location.href = 'add_subject_student.php';</script>";
            } else {
                echo "<script>alert('Error: Unable to assign class.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error: Could not prepare the database query.');</script>";
        }
    } else {
        echo "<script>alert('Please select both a student and a class.');</script>";
    }
}

$conn->close();
?>