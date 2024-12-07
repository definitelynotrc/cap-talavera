<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['edit'])) {
    $section_id = $_POST['section_id'];
    $sections = $_POST['sections'];
    $status = $_POST['status'];
    $department = $_POST['department'];
    $year_level = $_POST['year_level']; // Get the selected class_id value

    // Prepare and execute the update query
    $stmt = $conn->prepare("UPDATE section SET sections=?, status=?, dep_id=?, class_id=? WHERE section_id=?");
    $stmt->bind_param("ssiii", $sections, $status, $department, $year_level, $section_id);
    $stmt->execute();
    $stmt->close();

    header("Location: section.php"); // Redirect after the update
    exit();
}



?>