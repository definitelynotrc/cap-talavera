<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['add'])) {
    $sections = trim($_POST['sections']);
    $status = trim($_POST['status']);
    $department = $_POST['department'];
    $year_level = $_POST['class_id']; // Get year level

    // Validate input
    if (!empty($sections) && !empty($status) && !empty($department) && !empty($year_level)) {
        $stmt = $conn->prepare("INSERT INTO section (sections, status, dep_id, class_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $sections, $status, $department, $year_level);

        if ($stmt->execute()) {
            echo "<script>alert('Section added successfully!');</script>";
            echo "<script>window.location.href = 'section.php';</script>"; // Redirect to avoid resubmission
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('All fields are required!');</script>";
    }
}

?>