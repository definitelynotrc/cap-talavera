<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap"; // Replace with your actual database name 'cap'

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Assign Teacher to Class request (form submission)
if (isset($_POST['assign'])) {
    $teacher_type = $_POST['teacher_type'];
    $advisory_class_id = $_POST['advisory_class_id'];
    $sub_id = $_POST['sub_id'];
    $user_id = $_POST['user_id'];

    // Insert the assignment into the class_teacher table
    $stmt = $conn->prepare("INSERT INTO class_teacher (advisory_class_id, teacher_type, sub_id, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $advisory_class_id, $teacher_type, $sub_id, $user_id);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Teacher assigned successfully!'); window.location.href = 'assign_teacher.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
