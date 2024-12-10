<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['student_id'])) {
    $student_id = (int) $_GET['student_id'];

    // Prepare the query to fetch all subjects and instructors
    $stmt = $conn->prepare("
        SELECT 
            s.sub_id, 
            s.subjects, 
            CONCAT(u.fname, ' ', u.lname) AS instructor_name,
            ct.teacher_type
        FROM class_teacher ct
        JOIN users u ON ct.user_id = u.user_id
        JOIN subject s ON ct.sub_id = s.sub_id
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Iterate through the result and create options for the second dropdown
        while ($row = $result->fetch_assoc()) {
            $subject_instructor = htmlspecialchars($row['subjects']) . " - " . htmlspecialchars($row['instructor_name']);
            echo "<option value='" . $row['sub_id'] . "'>" . $subject_instructor . " (" . htmlspecialchars($row['teacher_type']) . ")</option>";
        }
    } else {
        echo "<option value=''>No subjects found</option>";
    }
}
?>