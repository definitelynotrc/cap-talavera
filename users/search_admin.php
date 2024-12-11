<?php
session_start();
$userid = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap"; // Replace with your actual database name 'cap'
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM users WHERE is_archived = 0";

// Check if there is a search term
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $searchTerm = "%" . $search . "%";
    $query .= " AND (fname LIKE ? OR mname LIKE ? OR lname LIKE ? OR contact_no LIKE ? OR email LIKE ? OR role LIKE ?)";
}

// Prepare the SQL query
$stmt = $conn->prepare($query);

// Bind parameters if search term is provided
if (isset($searchTerm)) {
    $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Output the table rows
if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        echo '<tr>';
        echo '<td>' . $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname'] . ' ' . $row['suffixname'] . '</td>';
        echo '<td>' . $row['contact_no'] . '</td>';
        echo '<td>' . $row['gender'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '<td>' . $row['role'] . '</td>';
        echo '<td>';
        if ($row['is_archived'] == 0) {
            echo '<button class="btn btn-success edit-btn" onclick="openEditModal(' . $row['user_id'] . ')">Edit</button>';
            echo '<a href="student.php?archive=true&user_id=' . $row['user_id'] . '"><button class="btn btn-danger archive-btn">Archive</button></a>';
        } elseif ($row['is_archived'] == 1) {
            echo '<a href="student.php?restore=true&user_id=' . $row['user_id'] . '"><button class="btn btn-success restore-btn">Restore</button></a>';
        }
        echo '</td>';
        echo '</tr>';
    endwhile;
else:
    echo '<tr><td colspan="6">No students found</td></tr>';
endif;
?>