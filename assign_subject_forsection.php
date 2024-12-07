<?php
// Database connection using mysqli
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap"; // Your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section_id = isset($_POST['section_id']) ? $_POST['section_id'] : 0;
    $subjects = isset($_POST['subjects']) ? json_decode($_POST['subjects'], true) : [];

    if ($section_id > 0 && !empty($subjects)) {
        // Loop through each subject and insert it into the section_subject table
        $query = "INSERT INTO section_subjec (section_id, sub_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);

        foreach ($subjects as $subject_id) {
            $stmt->bind_param("ii", $section_id, $subject_id);  // "ii" for two integers
            $stmt->execute();
        }

        echo 'Subjects assigned successfully';
        $stmt->close();
    } else {
        echo 'Invalid data.';
    }
}

$conn->close();  // Close the connection
?>