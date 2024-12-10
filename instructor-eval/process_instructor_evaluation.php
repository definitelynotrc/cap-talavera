<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Create connection
    $conn = new mysqli('localhost', 'root', '', 'cap');

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Get Remarks
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

    // Ensure user_id and class_teacher_id are set and not empty
    if (isset($_POST['user_id']) && isset($_POST['class_teacher_id'])) {
        $user_id = $_POST['user_id'];
        $class_teacher_id = $_POST['class_teacher_id'];
    } else {
        // Handle missing user_id or class_teacher_id (you could redirect or show an error message)
        die('User ID or Class Teacher ID is missing.');
    }

    // Generate a unique transaction code (combining timestamp and random string)
    $transaction_code = strtoupper(bin2hex(random_bytes(4))) . '-' . time();

    // Debugging: Output transaction code to check if it's being generated
    echo "Transaction Code: $transaction_code<br>";

    // Calculate rate_result based on the ratings
    $totalRating = 0;
    $totalQuestions = 0;

    // Loop over POST data for ratings and calculate the total score
    foreach ($_POST as $key => $value) {
        if (preg_match('/^q(\d+)$/', $key, $matches)) {
            $rate_id = intval($value); // Ensure the value is an integer

            // Validate the rating before including it in the total
            if ($rate_id >= 1 && $rate_id <= 5) {  // Assuming rate_id should be between 1 and 5
                $totalRating += $rate_id;
                $totalQuestions++;
            } else {
                // Handle invalid rating value
                echo "Invalid rating value for question ID $matches[1].";
            }
        }
    }

    // Calculate the rate_result (average rating)
    $rate_result = ($totalQuestions > 0) ? ($totalRating / $totalQuestions) : 0;

    // Insert into evaluation table
    $stmt = $conn->prepare("INSERT INTO evaluation (transaction_code, remarks, rate_result, user_id, class_teacher_id, date_created) VALUES (?, ?, ?, ?, ?, NOW())");
    if ($stmt === false) {
        die('Error preparing query: ' . $conn->error);
    }
    $stmt->bind_param('ssiii', $transaction_code, $remarks, $rate_result, $user_id, $class_teacher_id);

    if (!$stmt->execute()) {
        die('Error executing query: ' . $stmt->error);
    }
    $eval_id = $stmt->insert_id;
    $stmt->close();

    // Insert into ratings table (loop over POST data for ratings)
    foreach ($_POST as $key => $value) {
        if (preg_match('/^q(\d+)$/', $key, $matches)) {
            $ques_id = $matches[1];
            $rate_id = intval($value); // Ensure the value is an integer

            // Validate the rating before inserting
            if ($rate_id >= 1 && $rate_id <= 5) {  // Assuming rate_id should be between 1 and 5
                $stmt = $conn->prepare("INSERT INTO ratings (eval_id, ques_id, rate_id, date_created) VALUES (?, ?, ?, NOW())");
                if ($stmt === false) {
                    die('Error preparing ratings query: ' . $conn->error);
                }
                $stmt->bind_param('iii', $eval_id, $ques_id, $rate_id);

                if (!$stmt->execute()) {
                    die('Error executing ratings query: ' . $stmt->error);
                }
                $stmt->close();
            } else {
                // Handle invalid rating value
                echo "Invalid rating value for question ID $ques_id.";
            }
        }
    }

    // Close connection
    $conn->close();

    header("Location: instructor_evaluation.php");
    echo "Evaluation submitted successfully!";
}
?>