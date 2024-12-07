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

// Handle Assign Teacher to Class request (form submission)
if (isset($_POST['assign'])) {
    $teacher_type = $_POST['teacher_type'];
    $advisory_class_id = $_POST['advisory_class_id'];
    $sub_id = $_POST['sub_id'];
    $user_id = $_POST['user_id'];

    // Fetch the semester from dep_sub table based on the selected sub_id
    $semesterQuery = "SELECT dep_sub.sem_id, semester.semesters FROM dep_sub
                      JOIN semester ON dep_sub.sem_id = semester.sem_id
                      WHERE dep_sub.sub_id = ?";
    $semesterStmt = $conn->prepare($semesterQuery);
    $semesterStmt->bind_param("i", $sub_id);
    $semesterStmt->execute();
    $semesterResult = $semesterStmt->get_result();

    if ($semesterResult->num_rows > 0) {
        $semesterData = $semesterResult->fetch_assoc();
        $sem_id = $semesterData['sem_id'];
        $semester_name = $semesterData['semesters'];
    } else {
        echo "Semester data not found for the subject.";
        exit;
    }

    // Fetch the year_level from the class table based on the selected advisory_class_id
    $classQuery = "SELECT year_level FROM class WHERE class_id = ?";
    $classStmt = $conn->prepare($classQuery);
    $classStmt->bind_param("i", $advisory_class_id);
    $classStmt->execute();
    $classResult = $classStmt->get_result();

    if ($classResult->num_rows > 0) {
        $classData = $classResult->fetch_assoc();
        $year_level = $classData['year_level'];
    } else {
        echo "Class data not found.";
        exit;
    }

    // Insert into the advisory_class table
    $ay_id = 1; // Assuming ay_id = 1 for the current academic year
    $insertAdvisoryClassQuery = "INSERT INTO advisory_class (class_id, ay_id, sem_id, isActive) 
                                 VALUES (?, ?, ?, ?)";
    $insertAdvisoryClassStmt = $conn->prepare($insertAdvisoryClassQuery);
    $isActive = 1; // Assuming the advisory class is active upon creation
    $insertAdvisoryClassStmt->bind_param("iiii", $advisory_class_id, $ay_id, $sem_id, $isActive);

    if ($insertAdvisoryClassStmt->execute()) {
        echo "Advisory class inserted successfully!<br>";
        // Get the last inserted advisory_class_id
        $advisory_class_id = $insertAdvisoryClassStmt->insert_id;

        // Now insert into the class_teacher table
        $insertClassTeacherQuery = "INSERT INTO class_teacher (advisory_class_id, teacher_type, sub_id, user_id) 
                                    VALUES (?, ?, ?, ?)";
        $insertClassTeacherStmt = $conn->prepare($insertClassTeacherQuery);
        $insertClassTeacherStmt->bind_param("isii", $advisory_class_id, $teacher_type, $sub_id, $user_id);

        if ($insertClassTeacherStmt->execute()) {
            echo "<script>alert('Teacher assigned successfully!'); window.location.href = 'manage_subject.php';</script>";
        } else {
            echo "Error in class_teacher insertion: " . $insertClassTeacherStmt->error . "<br>";
        }
        $insertClassTeacherStmt->close();
    } else {
        echo "Error in advisory_class insertion: " . $insertAdvisoryClassStmt->error . "<br>";
    }

    $insertAdvisoryClassStmt->close();
    $semesterStmt->close();
    $classStmt->close();
}

$conn->close();
?>