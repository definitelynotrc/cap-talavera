<?php
// Database connection
$host = 'localhost';
$dbname = 'cap';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['dep_id'])) {
        $dep_id = $_GET['dep_id'];

        // Fetch subjects for the selected department
        $query = "SELECT s.sub_id, s.subjects 
                  FROM subject s
                  JOIN dep_sub ds ON s.sub_id = ds.sub_id
                  WHERE ds.dep_id = :dep_id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':dep_id', $dep_id, PDO::PARAM_INT);
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return subjects as JSON
        echo json_encode($subjects);
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
