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
    // Get dep_id for the section
    $query = "SELECT dep_id FROM section WHERE section_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $section = $result->fetch_assoc();

    if ($section) {
        $dep_id = $section['dep_id'];

        error_log("Section ID: $section_id, Department ID: $dep_id"); // Debug log

        // Fetch subjects
        $query = "
    SELECT 
        sub.sub_id, 
        sub.subjects, 
        ct.teacher_type, 
        u.fname AS instructor_fname, 
        u.lname AS instructor_lname,
        s.semesters AS semester  -- Use the correct column name here
    FROM subject sub
    LEFT JOIN dep_sub ds ON sub.sub_id = ds.sub_id
    LEFT JOIN class_teacher ct ON sub.sub_id = ct.sub_id
    LEFT JOIN users u ON ct.user_id = u.user_id
    LEFT JOIN semester s ON ds.sem_id = s.sem_id  -- Ensure you're using the correct table alias
    WHERE ds.dep_id = ?
";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $dep_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }

        // Log the fetched subjects
        error_log("Fetched subjects for Section ID $section_id: " . json_encode($subjects));

        echo json_encode($subjects);
    } else {
        error_log("No dep_id found for Section ID $section_id");
        echo json_encode([]);
    }

    $stmt->close();
}


$conn->close(); // Close the connection
?>