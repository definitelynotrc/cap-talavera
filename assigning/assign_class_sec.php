<?php
session_start();

// Ensure the user is logged in


$userid = $_SESSION['user_id']; // Store the user ID for potential use

$host = 'localhost'; // Change to your host
$dbname = 'cap'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['submit'])) {
        $class = $_POST['class'];
        $section = $_POST['section'];

        // Insert data into the database
        $query = "INSERT INTO class (year_level, section_id) VALUES (:class, :section)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':section', $section);

        // Execute the query
        $stmt->execute();

        // Redirect on success
        header('Location: class_section.php');
        exit;
    }
} catch (PDOException $e) {
    // Handle database connection or query errors
    die("Database error: " . $e->getMessage());
}
?>