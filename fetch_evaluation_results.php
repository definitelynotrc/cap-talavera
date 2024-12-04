<?php
// fetch_evaluation_results.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['instructor_id'])) {
    $instructorId = intval($_POST['instructor_id']);

    // Query to fetch evaluations for the specific instructor
    $query = "
    SELECT 
        e.eval_id AS evaluation_id,
        e.rate_result AS rating
    FROM evaluation e
    WHERE e.class_teacher_id = ?
    ORDER BY e.date_created DESC
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing query: ' . htmlspecialchars($conn->error)]);
        exit;
    }
    $stmt->bind_param('i', $instructorId);
    $stmt->execute();
    $result = $stmt->get_result();

    $evaluations = [];
    $totalRating = 0;
    $totalEvaluations = 0;

    while ($row = $result->fetch_assoc()) {
        $evaluations[] = ['rating' => $row['rating']];
        $totalRating += $row['rating'];
        $totalEvaluations++;
    }

    // Calculate the average rating
    if ($totalEvaluations > 0) {
        $averageRating = $totalRating / $totalEvaluations;
    } else {
        $averageRating = 0;
    }

    echo json_encode([
        'evaluations' => $evaluations,
        'average_rating' => $averageRating
    ]);
}
?>