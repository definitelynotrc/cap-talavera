<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resetCode = $_POST['reset_code'];

    // Check if the code is valid
    $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE code = ?");
    $stmt->bind_param("s", $resetCode);
    $stmt->execute();
    $stmt->bind_result($userId, $expiresAt);
    $stmt->fetch();
    $stmt->close();

    if ($userId) {
        // Check if the code has expired
        if (new DateTime() < new DateTime($expiresAt)) {
            // Redirect to reset password page with user ID
            session_start();
            $_SESSION['reset_user_id'] = $userId;
            header("Location: reset_password.php");
            exit();
        } else {
            echo "Error: Code has expired.";
        }
    } else {
        echo "Error: Invalid reset code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Reset Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        input[type="text"] {
            padding: 10px;
            margin: 10px 0;
            width: 90%;
            border-radius: 5px;
            border: 1px solid black;
        }




        input[type="text"]:focus {
            outline: none;
            border: 1px solid black;
        }

        input[type="submit"] {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            border-radius: 5px;
            border: none;
            background: black;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: #6a11cb;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Verify Reset Code</h2>
        <form method="POST" action="verify_code.php">
            <label for="reset_code">Enter Reset Code:</label>
            <input type="text" id="reset_code" name="reset_code" placeholder="Enter your code" required>
            <input type="submit" value="Verify Code">
        </form>
    </div>
</body>

</html>