<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        $userId = $_SESSION['reset_user_id'];

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the users table
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        $stmt->execute();
        $stmt->close();

        // Remove the reset code from the database
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();

        $successMessage = "Password successfully reset! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Error: Passwords do not match.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        form {
            background: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;

        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            align-items: center;
            flex-direction: column;

        }

        .form-group label {
            position: absolute;
            top: 14px;
            left: 9px;
            font-size: 14px;
            color: #666;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-group input {
            width: 100%;
            padding: 14px 10px;
            font-size: 16px;
            border: 1px solid black;
            border-radius: 4px;

            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border: 1px solid black;
        }

        .form-group input:focus+label,
        .form-group input:not(:placeholder-shown)+label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            color: black;
            background: #fff;
            padding: 0 4px;
        }

        input[type="submit"] {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background: black;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: #6a11cb;
        }
    </style>
</head>

<body>
    <form method="POST" action="reset_password.php">
        <h1>Reset Password</h1>
        <div class="form-group">
            <input type="password" id="new_password" name="new_password" required placeholder=" ">
            <label for="new_password">New Password:</label>
        </div>
        <div class="form-group">
            <input type="password" id="confirm_password" name="confirm_password" required placeholder=" ">
            <label for="confirm_password">Confirm Password:</label>
        </div>
        <input type="submit" value="Reset Password">
        <?php if ($successMessage): ?>
            <div class="success-message">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
    </form>

</body>

</html>