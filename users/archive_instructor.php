<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap"; // Replace with your actual database name

// Establishing a PDO connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Archive the instructor if id is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        // Update the instructor's status to "archived"
        $stmt = $pdo->prepare("UPDATE instructor SET status = 'archived' WHERE instructor_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the instructor page
        header("Location: instructor.php?archived=true"); // Add archived query to the URL
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
