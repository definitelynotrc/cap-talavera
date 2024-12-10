<?php
require 'vendor/autoload.php'; // Ensure PHPMailer is installed and autoloaded
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();

    if ($userId) {
        // Generate a reset code
        $resetCode = rand(100000, 999999);

        // Store the reset code in the database with an expiry time
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE code = ?, expires_at = ?");
        $stmt->bind_param("issss", $userId, $resetCode, $expiry, $resetCode, $expiry);
        $stmt->execute();
        $stmt->close();

        // Send the reset code via email
        $mail = new PHPMailer(true);

        try {
            // Configure PHPMailer
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jenalynsabado29@gmail.com'; //Email Address
            $mail->Password = 'autj xsxn lljk ecvf'; //Email Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email settings
            $mail->setFrom('no-reply@neust.com', 'NEUST-MGT Faculty Evaluation System');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "Your password reset code is: <b>$resetCode</b>";

            $mail->send();

            header("Location: verify_code.php");
        } catch (Exception $e) {
            echo "Error: Could not send the reset code. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            margin: 0;
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
            max-width: 420px;
            width: 100%;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: black;
        }

        label {
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
            text-align: left;
            color: black;
        }

        input[type="email"] {
            width: 90%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid black;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: black;
            font-size: 14px;
        }

        input[type="email"]::placeholder {
            color: #c3c3c3;
        }

        input[type="email"]:focus {
            outline: none;
            border: 1px solid black;
            background: rgba(255, 255, 255, 0.3);
        }

        button {
            background: black;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #6a11cb;
        }

        button:focus {
            outline: none;
        }

        .form-footer {
            margin-top: 15px;
            font-size: 12px;
        }

        .form-footer a {
            color: black;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Forgot Password</h1>
        <form method="POST" action="forget.php">
            <label for="email">Enter your email address:</label>
            <input type="email" id="email" name="email" placeholder="example@domain.com" required>
            <button type="submit">Send Code</button>
        </form>
        <div class="form-footer">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>

</html>