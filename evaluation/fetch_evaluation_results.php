<?php
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

    $query = "
   SELECT 
    q.ques_id AS question_id,
    q.questions AS question_text,
    SUM(CASE WHEN rt.rate_id = 5 THEN 1 ELSE 0 END) AS count_5,
    SUM(CASE WHEN rt.rate_id = 4 THEN 1 ELSE 0 END) AS count_4,
    SUM(CASE WHEN rt.rate_id = 3 THEN 1 ELSE 0 END) AS count_3,
    SUM(CASE WHEN rt.rate_id = 2 THEN 1 ELSE 0 END) AS count_2,
    SUM(CASE WHEN rt.rate_id = 1 THEN 1 ELSE 0 END) AS count_1,
    COUNT(DISTINCT e.user_id) AS total_respondents
FROM evaluation e
JOIN ratings r ON e.eval_id = r.eval_id
JOIN rate rt ON r.rate_id = rt.rate_id
JOIN question q ON r.ques_id = q.ques_id
WHERE e.class_teacher_id = ?
GROUP BY q.ques_id
ORDER BY q.ques_id ASC;

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
    $totalRatings = 0;
    $totalRespondents = 0;

    while ($row = $result->fetch_assoc()) {
        $ratingCounts = [
            5 => $row['count_5'],
            4 => $row['count_4'],
            3 => $row['count_3'],
            2 => $row['count_2'],
            1 => $row['count_1']
        ];

        // Use total respondents from the query
        $totalRespondents = $row['total_respondents'];
        $questionTotalRatings = 0;

        foreach ($ratingCounts as $rating => $count) {
            $questionTotalRatings += $rating * $count;
        }

        // Optionally, calculate average rating per question
        $averageRating = $totalRespondents > 0 ? $questionTotalRatings / $totalRespondents : 0;

        // Add to response
        $evaluations[] = [
            'question_id' => $row['question_id'],
            'question_text' => $row['question_text'],
            'rating_counts' => $ratingCounts,
            'average_rating' => $averageRating,
            'total_respondents' => $totalRespondents
        ];
    }

    $averageRating = ($totalRespondents > 0) ? $totalRatings / $totalRespondents : 0;

    echo json_encode([
        'evaluations' => $evaluations,
        'average_rating' => number_format($averageRating, 2) // Format average to 2 decimal points
    ]);
}
?>