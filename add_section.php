<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $sections = trim($_POST['sections']);
    $status = "Active";
    $dep_id = $_POST['dep_id'];
    $class_id = $_POST['year_level'];

    // Validate input
    if (!empty($sections) && !empty($class_id)) {
        // Insert into the section table
        $stmt = $conn->prepare("INSERT INTO section (sections, status, dep_id) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssi", $sections, $status, $dep_id);

            if ($stmt->execute()) {
                // Get the last inserted section_id
                $section_id = $conn->insert_id;

                // Insert the section_id into the class table
                $insertStmt = $conn->prepare("INSERT INTO class (section_id, year_level) VALUES (?, ?)");
                if ($insertStmt) {
                    $insertStmt->bind_param("ii", $section_id, $class_id);

                    if ($insertStmt->execute()) {
                        echo "<script>alert('Section added and linked to class successfully!');</script>";
                        echo "<script>window.location.href = 'section.php';</script>"; // Redirect to avoid resubmission
                    } else {
                        echo "<script>alert('Error inserting into class: " . $insertStmt->error . "');</script>";
                    }

                    $insertStmt->close();
                } else {
                    echo "<script>alert('Error preparing insert into class statement.');</script>";
                }
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error preparing insert into section statement.');</script>";
        }
    } else {
        echo "<script>alert('All fields are required!');</script>";
    }
}

?>