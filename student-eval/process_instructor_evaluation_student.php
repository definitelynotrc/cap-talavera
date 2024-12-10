<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // Create PDO connection
        $conn = new PDO('mysql:host=localhost;dbname=cap', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get and sanitize inputs
        $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
        $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
        $class_teacher_id = isset($_POST['class_teacher_id']) ? (int) $_POST['class_teacher_id'] : 0;

        if ($user_id <= 0 || $class_teacher_id <= 0) {
            throw new Exception('User ID or Class Teacher ID is missing.');
        }

        // Generate a unique transaction code (e.g., a combination of the current timestamp and a random string)
        $transaction_code = strtoupper(bin2hex(random_bytes(4))) . '-' . time();

        // Calculate rate_result based on the ratings
        $totalRating = 0;
        $totalQuestions = 0;

        foreach ($_POST as $key => $value) {
            if (preg_match('/^q(\d+)$/', $key, $matches)) {
                $rate_id = (int) $value;

                if ($rate_id >= 1 && $rate_id <= 5) {
                    $totalRating += $rate_id;
                    $totalQuestions++;
                } else {
                    throw new Exception("Invalid rating value for question ID $matches[1].");
                }
            }
        }

        $rate_result = ($totalQuestions > 0) ? ($totalRating / $totalQuestions) : 0;

        // Start transaction
        $conn->beginTransaction();

        // Insert into evaluation table
        $stmt = $conn->prepare("INSERT INTO evaluation (transaction_code, remarks, rate_result, user_id, class_teacher_id, date_created) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$transaction_code, $remarks, $rate_result, $user_id, $class_teacher_id]);
        $eval_id = $conn->lastInsertId();

        // Insert into ratings table
        foreach ($_POST as $key => $value) {
            if (preg_match('/^q(\d+)$/', $key, $matches)) {
                $ques_id = $matches[1];
                $rate_id = (int) $value;

                if ($rate_id >= 1 && $rate_id <= 5) {
                    $stmt = $conn->prepare("INSERT INTO ratings (eval_id, ques_id, rate_id, date_created) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$eval_id, $ques_id, $rate_id]);
                } else {
                    throw new Exception("Invalid rating value for question ID $ques_id.");
                }
            }
        }

        // Commit transaction
        $conn->commit();

        // Redirect after successful submission
        header("Location: student_evaluation.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }

        echo "Error: " . $e->getMessage();
    } finally {
        // Close the connection
        if (isset($conn)) {
            $conn = null;
        }
    }
}
?>