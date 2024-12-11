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



    $stmt = $conn->prepare("UPDATE section SET sections=?, status=? WHERE section_id=?");
    $stmt->bind_param("ssi", $sections, $status, $section_id);
    $stmt->execute();
    $stmt->close();



    header("Location: section.php"); // Redirect after the update
    exit();
}



?>