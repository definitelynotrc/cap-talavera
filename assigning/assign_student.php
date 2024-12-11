<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get selected student and subjects (advisory class)
    $studentId = $_POST['student'];
    $subjects = $_POST['subjects'];

    // Validate data
    if (!empty($studentId) && !empty($subjects)) {
        // Iterate through selected subjects and insert into the user_class table
        foreach ($subjects as $subjectId) {
            // Query to get the advisory_class_id based on the selected subject
            $query = "
                SELECT ac.advisory_class_id 
                FROM class_teacher ct
                JOIN advisory_class ac ON ct.advisory_class_id = ac.advisory_class_id
                WHERE ct.sub_id = ? ";

            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("i", $subjectId);
                $stmt->execute();
                $stmt->bind_result($advisoryClassId);
                $stmt->fetch();
                $stmt->close();

                if ($advisoryClassId) {
                    // Insert the student and advisory_class_id into the user_class table
                    $insertQuery = "INSERT INTO user_class (user_id, advisory_class_id, isActive) VALUES (?, ?, 1)";
                    if ($insertStmt = $conn->prepare($insertQuery)) {
                        $insertStmt->bind_param("ii", $studentId, $advisoryClassId);
                        if ($insertStmt->execute()) {

                        } else {
                            echo "<script>alert('Error assigning subject to student: " . $insertStmt->error . "');</script>";
                        }
                        $insertStmt->close();
                    } else {
                        echo "<script>alert('Error preparing insert statement into user_class table.');</script>";
                    }
                } else {
                    echo "<script>alert('No advisory class found for the selected subject.');</script>";
                }
            } else {
                echo "<script>alert('Error preparing query to fetch advisory_class_id.');</script>";
            }
        }

        // Redirect back to the form page or show a success message
        echo "<script>window.location.href = 'add_subject_student.php';</script>";
    } else {
        echo "<script>alert('Please select a student and at least one subject.');</script>";
    }
}

$conn->close();
?>