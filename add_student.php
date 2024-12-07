<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if (isset($_POST['add'])) {
    $sections = trim($_POST['sections']);
    $status = trim($_POST['status']);
    $department = $_POST['department'];

    // Validate input
    if (!empty($sections) && !empty($status) && !empty($department)) {
        $stmt = $conn->prepare("INSERT INTO section (sections, status, dep_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $sections, $status, $department);

        if ($stmt->execute()) {
            echo "<script>alert('Section added successfully!');</script>";
            echo "<script>window.location.href = 'your-page.php';</script>"; // Redirect to avoid resubmission
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('All fields are required!');</script>";
    }
}

$conn->close();
?>