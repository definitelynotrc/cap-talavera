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

// Handle form submission
if (isset($_POST['assign_student'])) {
    // Get form data
    $student_id = $_POST['student_id']; // Correctly use 'student_id' from the form
    $section_id = $_POST['section_id'];

    // Validate input
    if (!empty($student_id) && !empty($section_id)) {
        // Retrieve the department id (dep_id) and user_id for the selected student
        $studentQuery = "SELECT dep_id, user_id FROM user_dep WHERE user_id = ?";
        $studentStmt = $conn->prepare($studentQuery);
        $studentStmt->bind_param("i", $student_id);
        $studentStmt->execute();
        $studentResult = $studentStmt->get_result();

        if ($studentResult->num_rows > 0) {
            $studentData = $studentResult->fetch_assoc();
            $dep_id = $studentData['dep_id'];
            $user_id = $studentData['user_id'];

            // Check if the student is already assigned to the selected section
            $checkAssignmentQuery = "SELECT * FROM class_student WHERE user_id = ? AND section_id = ?";
            $checkAssignmentStmt = $conn->prepare($checkAssignmentQuery);
            $checkAssignmentStmt->bind_param("ii", $user_id, $section_id);
            $checkAssignmentStmt->execute();
            $checkAssignmentResult = $checkAssignmentStmt->get_result();

            // If the student is already assigned, show an error message
            if ($checkAssignmentResult->num_rows > 0) {
                echo "<script>alert('This student is already assigned to this section.'); window.location.href='manage_sub_student.php';</script>";
            } else {
                // Insert the student into the class_student table
                $insertQuery = "INSERT INTO class_student (dep_id, user_id, section_id) VALUES (?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("iii", $dep_id, $user_id, $section_id);

                if ($insertStmt->execute()) {
                    echo "<script>alert('Student assigned to section successfully!'); window.location.href='manage_sub_student.php';</script>";
                } else {
                    echo "Error: " . $insertStmt->error;
                }

                $insertStmt->close();
            }

            $checkAssignmentStmt->close();
        } else {
            echo "<script>alert('Student not found.'); window.location.href='manage_sub_student.php';</script>";
        }

        $studentStmt->close();
    } else {
        echo "<script>alert('Please select a student and section.'); window.location.href='manage_sub_student.php';</script>";
    }
}

$conn->close();
?>