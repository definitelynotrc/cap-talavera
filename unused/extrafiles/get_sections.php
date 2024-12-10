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

// Get department ID from the AJAX request
$depid = $_GET['depid'];

// Fetch sections based on department ID
$sectionsQuery = "SELECT * FROM section WHERE dep_id = ?";
$stmt = $conn->prepare($sectionsQuery);
$stmt->bind_param("s", $depid);
$stmt->execute();
$result = $stmt->get_result();

// Fetch sections as an associative array
$sections = [];
while ($section = $result->fetch_assoc()) {
    $sections[] = $section;
}

// Return the sections as JSON
header('Content-Type: application/json'); // Ensure proper content type
echo json_encode($sections); // Send JSON response
?>