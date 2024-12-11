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
        e.eval_id AS evaluation_id,
        e.remarks AS evaluation_remarks,
        e.date_created AS evaluation_date,
        e.rate_result,
        r.ques_id AS question_id,
        q.questions AS question_text,
        rt.rate_name AS rate_name,
        rt.rate_id AS rate_value,

        CONCAT(u1.fname, ' ', u1.lname) AS evaluator_name, -- Class teacher's name
        CONCAT(u2.fname, ' ', u2.lname) AS evaluated_name -- Evaluated person's name
    FROM evaluation e
    JOIN class_teacher ct ON e.class_teacher_id = ct.class_teacher_id
    JOIN users u1 ON ct.user_id = u1.user_id -- Evaluator (Class teacher)
    JOIN ratings r ON e.eval_id = r.eval_id
    JOIN question q ON r.ques_id = q.ques_id
    JOIN rate rt ON r.rate_id = rt.rate_id
    JOIN users u2 ON e.user_id = u2.user_id -- Evaluated person
    WHERE e.class_teacher_id = ?
    ORDER BY e.date_created DESC, q.ques_id ASC
";


    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing query: ' . htmlspecialchars($conn->error)]);
        exit;
    }
    $stmt->bind_param('i', $instructorId);
    $stmt->execute();
    $result = $stmt->get_result();

    $evaluationTables = ''; // Initialize a variable to hold multiple tables

    if ($result->num_rows > 0) {
        $previousEvaluationId = null;
        while ($row = $result->fetch_assoc()) {
            // If a new evaluation starts, create a new table
            if ($previousEvaluationId != $row['evaluation_id']) {
                // If it's not the first evaluation, append the previous table
                if ($previousEvaluationId != null) {
                    $evaluationTables .= '</table><br>'; // Close the previous table
                }


                // Start a new table for this evaluation
                $evaluationTables .= '<table class="evaluation-table">';
                $evaluationTables .= '<tr><th colspan="6">';
                $evaluationTables .= '<div style="display: flex; flex-direction: row; justify-content: space-between;">';
                $evaluationTables .= '<span class="evaluated-name"><strong>Being Evaluated:</strong> ' . htmlspecialchars($row['evaluator_name']) . '</span><br>';
                $evaluationTables .= '<button class="printBtn " style="font-size: 12px; width: 20%; padding: 5px 10px;">Print Evaluation</button>';
                $evaluationTables .= '</div>';
                $evaluationTables .= '<div style="display: flex;  justify-content: space-between; align-items: center; flex-direction: column;">';
                $evaluationTables .= '<span class="prof"><strong>Professor/Instructor Evaluation Form</strong></span> ';
                $evaluationTables .= '<span class="directions"><strong>Directions:</strong>   This questionnaire seeks your objective, honest, and fair evaluation
                    of the Professors/Instructors performance. Please indicate your rating on the different items
                    by selecting the rating in the corresponding column provided. </span>';


                // Print button on the right

                $evaluationTables .= '</div><br>';
                $evaluationTables .= '<span><strong>Date: ' . htmlspecialchars(date("M d, Y", strtotime($row['evaluation_date']))) . '</strong></span> <br>';

                // Show Evaluator's name (but won't be printed)
                $evaluationTables .= '<span class="evaluator-name"><strong>Evaluator:</strong> ' . htmlspecialchars($row['evaluated_name']) . '</span><br>';

                $evaluationTables .= '<strong>Results:</strong> ' . htmlspecialchars($row['rate_result']) . '<br><br>';
                $evaluationTables .= '<strong>Ratings:</strong> 5 - Excellent, 4 - Very Good, 3 - Good, 2 - Fair, 1 - Poor</th></tr>';
                $evaluationTables .= '<tr><th>Question</th><th>5</th><th>4</th><th>3</th><th>2</th><th>1</th></tr>';
                $evaluationTables .= '<tr><td colspan="6"><strong>Remarks:</strong> ' . htmlspecialchars($row['evaluation_remarks']) . '</td></tr>';
            }

            // Add the question row to the table
            $evaluationTables .= '<tr>';
            $evaluationTables .= '<td>' . htmlspecialchars($row['question_text']) . '</td>';
            for ($i = 5; $i >= 1; $i--) {
                $selected = ($row['rate_value'] == $i) ? '✔' : '';
                $evaluationTables .= '<td>' . $selected . '</td>';
            }
            $evaluationTables .= '</tr>';

            $previousEvaluationId = $row['evaluation_id']; // Track the current evaluation
        }

        // Close the last table
        $evaluationTables .= '</table>';
    } else {
        $evaluationTables = '<p>No evaluations found for this instructor.</p>';
    }

    // Return the multiple tables as part of the response
    echo json_encode(['evaluationTables' => $evaluationTables]);
}
?>