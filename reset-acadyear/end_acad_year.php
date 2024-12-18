<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
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

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Mark all previous academic year records as inactive
        $pdo->exec("UPDATE acad_year SET isActive = 0 WHERE isActive = 1");

        // Mark all advisory classes as inactive
        $pdo->exec("UPDATE advisory_class SET isActive = 0 WHERE isActive = 1");

        // Mark all user classes as inactive
        $pdo->exec("UPDATE user_class SET isActive = 0 WHERE isActive = 1");

        // Commit the changes
        $pdo->commit();

        // Send success response
        echo "School year ended successfully. All records have been marked as inactive.";
    } catch (Exception $e) {
        // Rollback in case of error
        $pdo->rollBack();
        http_response_code(500); // Set HTTP status code to 500
        echo "Error: " . $e->getMessage();
    }
    exit;
}
?>