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

$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : 0;

if ($section_id > 0) {
    // First, get the department ID of the section
    $query = "SELECT dep_id FROM section WHERE section_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $section_id); // "i" for integer
    $stmt->execute();
    $result = $stmt->get_result();
    $section = $result->fetch_assoc();

    if ($section) {
        $dep_id = $section['dep_id']; // Get the department ID for the section

        // Fetch the subjects, and join class_teacher and user tables for instructor details
        $query = "
            SELECT 
                sub.sub_id, 
                sub.subjects, 
                ct.teacher_type, 
                u.fname AS instructor_fname, 
                u.lname AS instructor_lname 
            FROM subject sub
            LEFT JOIN dep_sub ds ON sub.sub_id = ds.sub_id
            LEFT JOIN class_teacher ct ON sub.sub_id = ct.sub_id
            LEFT JOIN users u ON ct.user_id = u.user_id
            WHERE ds.dep_id = ? 
            AND sub.sub_id IN 
                (SELECT sub_id FROM section_subjec WHERE section_id = ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $dep_id, $section_id); // "ii" for two integers
        $stmt->execute();
        $result = $stmt->get_result();

        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }

        echo json_encode($subjects); // Return the subjects with instructor details as a JSON response
    } else {
        echo json_encode([]); // No department found for the section
    }

    $stmt->close();
}

$conn->close(); // Close the connection
?>