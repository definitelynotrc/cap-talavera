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
    $year_level = $_POST['year_level'];

    // Prepare and execute the update query
    $stmt = $conn->prepare("UPDATE section SET sections=?, status=?, dep_id=? WHERE section_id=?");
    $stmt->bind_param("ssii", $sections, $status, $department, $section_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE class SET year_level=? WHERE section_id=?");
    $stmt->bind_param("ii", $year_level, $section_id);
    $stmt->execute();
    $stmt->close();


    header("Location: section.php"); // Redirect after the update
    exit();
}



?>