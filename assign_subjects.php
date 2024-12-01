<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected student and subjects
    $student_id = $_POST['student'];
    $subjects = isset($_POST['subjects']) ? $_POST['subjects'] : [];

    // Check if subjects are selected
    if (!empty($subjects)) {
        // Loop through each selected subject
        foreach ($subjects as $subject_id) {
            // Insert each subject assignment into the database
            $stmt = $conn->prepare("INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $subject_id);
            $stmt->execute();
        }
        echo "Subjects have been successfully assigned to the student.";
    } else {
        echo "No subjects selected.";
    }
}
?>