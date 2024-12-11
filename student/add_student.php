<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function sendEmail($email, $name, $tempPassword)
{
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jenalynsabado29@gmail.com'; //Email Address
    $mail->Password = 'autj xsxn lljk ecvf'; //Email Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('jenalynsabado29@gmail.com', 'Admin');


    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Welcome to the NEUST Online Faculty Evaluation System!';
    $mail->Body = "
        <p>Hi {$name},</p>
        <p>Your account has been created. Here are your login credentials:</p>
        <ul>
            <li><strong>Email:</strong> {$email}</li>
            <li><strong>Password:</strong> {$tempPassword}</li>
        </ul>
        <p>Please log in and change your password immediately.</p>
    ";

    if (!$mail->send()) {
        error_log("Error sending email to {$email}: " . $mail->ErrorInfo);
    }
}

// Handle the form submission
if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $suffixname = $_POST['suffixname'];
    $contact_no = $_POST['contact_no'];
    $houseno = $_POST['houseno'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $postalcode = $_POST['postalcode'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $is_archived = 0;
    // Generate a temporary password if not provided
    $tempPassword = $password ? $password : bin2hex(random_bytes(6));

    // Hash the password
    $hashedPassword = password_hash($tempPassword, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (fname, mname, lname, suffixname, contact_no, houseno, street, barangay, city, province, postalcode, birthdate, gender, email, password, role, is_archived) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $role = 'Student'; // Explicitly define role
        $stmt->bind_param("ssssssssssssssssi", $fname, $mname, $lname, $suffixname, $contact_no, $houseno, $street, $barangay, $city, $province, $postalcode, $birthdate, $gender, $email, $hashedPassword, $role, $is_archived);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Student added successfully.";
            sendEmail($email, "{$fname} {$lname}", $tempPassword);
            header('Location: student.php');
        } else {
            $_SESSION['error_message'] = "Failed to add Instructor: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Failed to prepare SQL statement: " . $conn->error;
    }
}

$conn->close();
?>