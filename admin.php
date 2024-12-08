<?php
session_start();
$userid = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap"; // Replace with your actual database name 'cap'

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Email sending function
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

// Handle CSV file upload and inserting through form submission
if (isset($_POST['submit']) && isset($_FILES['csvFile'])) {
    $file = $_FILES['csvFile'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpName = $file['tmp_name'];
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (($handle = fopen($fileTmpName, 'r')) !== FALSE) {
            fgetcsv($handle);
            $stmt = $conn->prepare("
        INSERT INTO users (
            fname, mname, lname, suffixname, contact_no, houseno, street, barangay, city, 
            province, postalcode, birthdate, gender, role, email, is_archived, password
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

            if (!$stmt) {
                die("Statement preparation failed: " . $conn->error);
            }

            $studentsAdded = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 15) {
                    continue; // Ensure there are at least 15 columns
                }

                // Check if the role is "Instructor"
                if ($data[13] !== 'admin') {
                    $_SESSION['error_message'] = "Invalid role found for user {$data[0]} {$data[2]}: {$data[13]}. Skipping this user.";
                    continue; // Skip users who are not instructors
                }

                $email = $data[14];

                // Check if the email already exists
                $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
                $checkStmt->bind_param("s", $email);
                $checkStmt->execute();
                $checkStmt->store_result();

                if ($checkStmt->num_rows > 0) {
                    $_SESSION['error_message'] = "Email {$email} already exists. Skipping this user.";
                    $checkStmt->close();
                    continue; // Skip this user
                }
                $checkStmt->close();

                // Temporary password generation
                $tempPassword = bin2hex(random_bytes(6)); // 12-character temporary password
                $hashedPassword = password_hash($tempPassword, PASSWORD_BCRYPT);
                $birthdate = DateTime::createFromFormat('d/m/Y', $data[11]);

                try {
                    $dateObj = DateTime::createFromFormat('d/m/Y', $data[11]);
                    if ($dateObj === false || $dateObj === null) {
                        $formattedBirthdate = '1990-01-01'; // Default date
                    } else {
                        $formattedBirthdate = $dateObj->format('Y-m-d');
                    }
                } catch (Exception $e) {
                    $formattedBirthdate = '1990-01-01'; // Default date on exception
                }

                $fname = $data[0];
                $mname = !empty($data[1]) ? $data[1] : NULL;
                $lname = $data[2];
                $suffixname = !empty($data[3]) ? $data[3] : NULL;
                $contact_no = $data[4];
                $houseno = $data[5];
                $street = $data[6];
                $barangay = $data[7];
                $city = $data[8];
                $province = $data[9];
                $postalcode = $data[10];
                $gender = $data[12];
                $role = $data[13];
                $is_archived = 0;

                $stmt->bind_param(
                    "sssssssssssssssss",
                    $fname,
                    $mname,
                    $lname,
                    $suffixname,
                    $contact_no,
                    $houseno,
                    $street,
                    $barangay,
                    $city,
                    $province,
                    $postalcode,
                    $formattedBirthdate,
                    $gender,
                    $role,
                    $email,
                    $is_archived,
                    $hashedPassword
                );

                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "User {$data[0]} {$data[2]} inserted successfully.";
                    sendEmail($email, "$fname $lname", $tempPassword);
                    $instructorsAdded++;
                } else {
                    $_SESSION['error_message'] = "Failed to insert user {$data[0]} {$data[2]}: " . $stmt->error;
                }
            }

            fclose($handle);
            $stmt->close();
            if ($studentsAdded > 0) {
                $_SESSION['success_message'] = "$studentsAdded Student added successfully.";
            }
        } else {
            $_SESSION['error_message'] = "Error processing the CSV file.";
        }
    } else {
        $_SESSION['error_message'] = "Error uploading the file.";
    }
}
// Handle Edit request (excluding password)


if (isset($_POST['edit'])) {
    $user_id = $_POST['user_id'];
    $firstname = $_POST['fname'];
    $middlename = $_POST['mname'];
    $lastname = $_POST['lname'];
    $suffixname = $_POST['suffixname'];
    $contact = $_POST['contact_no'];
    $houseno = $_POST['houseno'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $postalcode = $_POST['postalcode'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Get the current email before update for comparison
    $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($currentEmail);
    $stmt->fetch();
    $stmt->close();

    // Generate a temporary password
    $tempPassword = bin2hex(random_bytes(4)); // Generates an 8-character random password

    // Update the user in the database with all fields
    $stmt = $conn->prepare("
        UPDATE users 
        SET fname=?, mname=?, lname=?, suffixname=?, contact_no=?, houseno=?, street=?, barangay=?, city=?, 
            province=?, postalcode=?, birthdate=?, gender=?, email=?, role=? 
        WHERE user_id=?
    ");

    // Bind the parameters
    $stmt->bind_param(
        "sssssssssssssssi",
        $firstname,
        $middlename,
        $lastname,
        $suffixname,
        $contact,
        $houseno,
        $street,
        $barangay,
        $city,
        $province,
        $postalcode,
        $birthdate,
        $gender,
        $email,
        $role,
        $user_id
    );

    // Execute and check for success
    if ($stmt->execute()) {

        $_SESSION['success_message'] = "User updated successfully.";
        if ($currentEmail != $email) {
            // Send email if the email is changed
            sendEmail($email, "{$firstname} {$lastname}", $tempPassword);
        }
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
}



// Handle Archive request
if (isset($_GET['archive'])) {
    $user_id = $_GET['user_id'];

    // Mark the user as archived (assuming an "is_archived" column)
    $stmt = $conn->prepare("UPDATE users SET is_archived=1 WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php"); // Redirect to avoid resubmitting the form
    exit();
}

// Handle Restore request (for archived users only)
if (isset($_GET['restore'])) {
    $user_id = $_GET['user_id'];

    // Restore the archived student by setting is_archived back to 0
    $stmt = $conn->prepare("UPDATE users SET is_archived=0 WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php"); // Redirect to avoid resubmitting the form
    exit();
}

// Handle filtering based on active or archived status
$filter = isset($_GET['status']) ? $_GET['status'] : 'active';
if ($filter == 'archived') {
    // Retrieve archived students
    $sql = "SELECT * FROM users WHERE is_archived = 1 AND role = 'Admin'";
} else {
    // Retrieve active students
    $sql = "SELECT * FROM users WHERE is_archived = 0 AND role = 'Admin'";
}
$result = $conn->query($sql);

// Check if there are any results
if ($result === FALSE) {
    echo "Error: " . $conn->error;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="sidebar.css">

</head>

<body>
    <nav class="topbar">

        <div class="toggle" onclick="toggleSidebar()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" stroke="white"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M9 2V22" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

        </div>

        <div class="user">
            <div class="dropdown">
                <!-- Clickable Image -->
                <button class="dropdown-btn " onclick="toggleUser()">
                    <img src="/img/admin.jpg" alt="User Profile" class="profile-img">
                </button>
                <!-- Dropdown Menu -->
                <div class="dropdown-content" style="display: none;">
                    <div>
                        <a href="#">Manage Account</a>
                    </div>
                    <div>
                        <a href="logout.php">Logout</a>
                    </div>
                    <!-- PHP to log out user -->
                </div>
            </div>
        </div>
    </nav>
    <div class="container">

        <aside class="navigation">
            <ul>
                <li class="logo">

                    <a href="index.php">
                        <span class="custom-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.05 2.53004L4.03002 6.46004C2.10002 7.72004 2.10002 10.54 4.03002 11.8L10.05 15.73C11.13 16.44 12.91 16.44 13.99 15.73L19.98 11.8C21.9 10.54 21.9 7.73004 19.98 6.47004L13.99 2.54004C12.91 1.82004 11.13 1.82004 10.05 2.53004Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M5.63 13.08L5.62 17.77C5.62 19.04 6.6 20.4 7.8 20.8L10.99 21.86C11.54 22.04 12.45 22.04 13.01 21.86L16.2 20.8C17.4 20.4 18.38 19.04 18.38 17.77V13.13"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M21.4 15V9" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>


                        </span>
                        <span class="title">NEUST</span>
                    </a>
                </li>
                <li id="dashboard">
                    <a href="dashboard.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17 10H19C21 10 22 9 22 7V5C22 3 21 2 19 2H17C15 2 14 3 14 5V7C14 9 15 10 17 10Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M5 22H7C9 22 10 21 10 19V17C10 15 9 14 7 14H5C3 14 2 15 2 17V19C2 21 3 22 5 22Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M6 10C8.20914 10 10 8.20914 10 6C10 3.79086 8.20914 2 6 2C3.79086 2 2 3.79086 2 6C2 8.20914 3.79086 10 6 10Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M18 22C20.2091 22 22 20.2091 22 18C22 15.7909 20.2091 14 18 14C15.7909 14 14 15.7909 14 18C14 20.2091 15.7909 22 18 22Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>


                        </span>
                        <span class="title">Dashboard</span></a>
                </li>
                <li id="instructor" onclick="showInstructorDropdown()">
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <span class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M19.2101 15.74L15.67 19.2801C15.53 19.4201 15.4 19.68 15.37 19.87L15.18 21.22C15.11 21.71 15.45 22.05 15.94 21.98L17.29 21.79C17.48 21.76 17.75 21.63 17.88 21.49L21.42 17.95C22.03 17.34 22.32 16.63 21.42 15.73C20.53 14.84 19.8201 15.13 19.2101 15.74Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M18.7001 16.25C19.0001 17.33 19.84 18.17 20.92 18.47" stroke="white"
                                    stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M3.40991 22C3.40991 18.13 7.25994 15 11.9999 15C13.0399 15 14.0399 15.15 14.9699 15.43"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                        </span>
                        <span class="title">Instructor</span>
                    </div>
                    <ul class="instructorDropdown">
                        <li><a href="instructor.php">Manage Instructors</a></li>
                        <li><a href="manage_subject.php">Instructor Subjects</a></li>
                    </ul>
                </li>
                <li id="student" onclick="showStudentDropdown()">
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <span class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M19.2101 15.74L15.67 19.2801C15.53 19.4201 15.4 19.68 15.37 19.87L15.18 21.22C15.11 21.71 15.45 22.05 15.94 21.98L17.29 21.79C17.48 21.76 17.75 21.63 17.88 21.49L21.42 17.95C22.03 17.34 22.32 16.63 21.42 15.73C20.53 14.84 19.8201 15.13 19.2101 15.74Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M18.7001 16.25C19.0001 17.33 19.84 18.17 20.92 18.47" stroke="white"
                                    stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M3.40991 22C3.40991 18.13 7.25994 15 11.9999 15C13.0399 15 14.0399 15.15 14.9699 15.43"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                        </span>
                        <span class="title">Student</span>
                    </div>
                    <ul class="studentDropdown">
                        <li><a href="student.php">Manage Students</a></li>
                        <li><a href="manage_sub_student.php">Student Sections</a></li>
                    </ul>
                </li>
                <li id="admin">
                    <a href="admin.php"><span class="icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M2 12.8799V11.1199C2 10.0799 2.85 9.21994 3.9 9.21994C5.71 9.21994 6.45 7.93994 5.54 6.36994C5.02 5.46994 5.33 4.29994 6.24 3.77994L7.97 2.78994C8.76 2.31994 9.78 2.59994 10.25 3.38994L10.36 3.57994C11.26 5.14994 12.74 5.14994 13.65 3.57994L13.76 3.38994C14.23 2.59994 15.25 2.31994 16.04 2.78994L17.77 3.77994C18.68 4.29994 18.99 5.46994 18.47 6.36994C17.56 7.93994 18.3 9.21994 20.11 9.21994C21.15 9.21994 22.01 10.0699 22.01 11.1199V12.8799C22.01 13.9199 21.16 14.7799 20.11 14.7799C18.3 14.7799 17.56 16.0599 18.47 17.6299C18.99 18.5399 18.68 19.6999 17.77 20.2199L16.04 21.2099C15.25 21.6799 14.23 21.3999 13.76 20.6099L13.65 20.4199C12.75 18.8499 11.27 18.8499 10.36 20.4199L10.25 20.6099C9.78 21.3999 8.76 21.6799 7.97 21.2099L6.24 20.2199C5.33 19.6999 5.02 18.5299 5.54 17.6299C6.45 16.0599 5.71 14.7799 3.9 14.7799C2.85 14.7799 2 13.9199 2 12.8799Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span><span class="title">Admin</span></a>
                </li>


                <li id="department" onclick="showDepartmentDropdown()">
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <span class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6.44 2H17.55C21.11 2 22 2.89 22 6.44V12.77C22 16.33 21.11 17.21 17.56 17.21H6.44C2.89 17.22 2 16.33 2 12.78V6.44C2 2.89 2.89 2 6.44 2Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 17.22V22" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M2 13H22" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M7.5 22H16.5" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>

                        </span>
                        <span class="title">Department</span>
                    </div>
                    <ul class="departmentDropdown" id="departmentDropdown">
                        <li>
                            <a href="department.php">Manage Departments</a>
                        </li>
                        <li>
                            <a href="subject.php">Manage Subjects</a>
                        </li>
                        <li>
                            <a href="class.php">Manage Classes</a>
                        </li>
                        <li>
                            <a href="section.php">Manage Sections</a>
                        </li>
                        <li>
                            <a href="semester.php">Manage Semesters</a>
                        </li>
                        <li>
                            <a href="acad_year.php">Manage Academic Year</a>
                        </li>
                    </ul>
                </li>


                <li id="question">
                    <a href="question.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17 18.4301H13L8.54999 21.39C7.88999 21.83 7 21.3601 7 20.5601V18.4301C4 18.4301 2 16.4301 2 13.4301V7.42999C2 4.42999 4 2.42999 7 2.42999H17C20 2.42999 22 4.42999 22 7.42999V13.4301C22 16.4301 20 18.4301 17 18.4301Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M12.0001 11.36V11.15C12.0001 10.47 12.4201 10.11 12.8401 9.82001C13.2501 9.54001 13.66 9.18002 13.66 8.52002C13.66 7.60002 12.9201 6.85999 12.0001 6.85999C11.0801 6.85999 10.3401 7.60002 10.3401 8.52002"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.9955 13.75H12.0045" stroke="white" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>


                        </span><span class="title">Question</span></a>
                </li>
                <li id="rate">
                    <a href="rate.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15.39 5.21L16.7999 8.02999C16.9899 8.41999 17.4999 8.78999 17.9299 8.86999L20.48 9.28999C22.11 9.55999 22.49 10.74 21.32 11.92L19.3299 13.91C18.9999 14.24 18.81 14.89 18.92 15.36L19.4899 17.82C19.9399 19.76 18.9 20.52 17.19 19.5L14.7999 18.08C14.3699 17.82 13.65 17.82 13.22 18.08L10.8299 19.5C9.11994 20.51 8.07995 19.76 8.52995 17.82L9.09996 15.36C9.20996 14.9 9.01995 14.25 8.68995 13.91L6.69996 11.92C5.52996 10.75 5.90996 9.56999 7.53996 9.28999L10.0899 8.86999C10.5199 8.79999 11.03 8.41999 11.22 8.02999L12.63 5.21C13.38 3.68 14.62 3.68 15.39 5.21Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8 5H2" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M5 19H2" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M3 12H2" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>


                        </span><span class="title">Rate</span></a>
                </li>
                <li id="evaluation">
                    <a href="evaluation.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.26001 11.0199V15.9899C4.26001 17.8099 4.26001 17.8099 5.98001 18.9699L10.71 21.6999C11.42 22.1099 12.58 22.1099 13.29 21.6999L18.02 18.9699C19.74 17.8099 19.74 17.8099 19.74 15.9899V11.0199C19.74 9.19994 19.74 9.19994 18.02 8.03994L13.29 5.30994C12.58 4.89994 11.42 4.89994 10.71 5.30994L5.98001 8.03994C4.26001 9.19994 4.26001 9.19994 4.26001 11.0199Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M17.5 7.63V5C17.5 3 16.5 2 14.5 2H9.5C7.5 2 6.5 3 6.5 5V7.56" stroke="white"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M12.63 10.99L13.2 11.88C13.29 12.02 13.49 12.16 13.64 12.2L14.66 12.46C15.29 12.62 15.46 13.16 15.05 13.66L14.38 14.47C14.28 14.6 14.2 14.83 14.21 14.99L14.27 16.04C14.31 16.69 13.85 17.02 13.25 16.78L12.27 16.39C12.12 16.33 11.87 16.33 11.72 16.39L10.74 16.78C10.14 17.02 9.68002 16.68 9.72002 16.04L9.78002 14.99C9.79002 14.83 9.71002 14.59 9.61002 14.47L8.94002 13.66C8.53002 13.16 8.70002 12.62 9.33002 12.46L10.35 12.2C10.51 12.16 10.71 12.01 10.79 11.88L11.36 10.99C11.72 10.45 12.28 10.45 12.63 10.99Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                        </span><span class="title">Evaluation</span></a>
                </li>
            </ul>
            </a>
        </aside>
        <div class="main">
            <div>
                <h2>Upload CSV File to Users Table</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="csvFile">Select CSV File:</label>
                    <input type="file" name="csvFile" id="csvFile" required>
                    <button type="submit" name="submit" class="btn1 btn-primary">Upload</button>
                </form>
            </div>


            <h2><?php echo ucfirst($filter); ?> Admins</h2>
            <!-- Button to open the modal -->
            <button type="button" class="btn1 btn-primary" style="" onclick="openModal()">Add Admin
                Manually</button>
            <div>
                <form method="GET" style="margin-bottom: 10px;">
                    <select style="width: 20%;" name="status" onchange="this.form.submit()">
                        <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Active Admin
                        </option>
                        <option value="archived" <?php echo ($filter == 'archived') ? 'selected' : ''; ?>>Archived Admin
                        </option>
                    </select>
                </form>
            </div>


            <div class="table-wrapper">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Contact</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname'] . ' ' . $row['suffixname']; ?>
                                    </td>
                                    <td><?php echo $row['contact_no']; ?></td>
                                    <td><?php echo $row['gender']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['role']; ?></td>
                                    <td>
                                        <!-- Action buttons -->
                                        <?php if ($row['is_archived'] == 0): ?>
                                            <button class="btn btn-success edit-btn"
                                                onclick="openEditModal(<?php echo $row['user_id']; ?>, '<?php echo $row['fname']; ?>', '<?php echo $row['mname']; ?>', '<?php echo $row['lname']; ?>', '<?php echo $row['suffixname']; ?>',  '<?php echo $row['contact_no']; ?>', '<?php echo $row['houseno']; ?>', '<?php echo $row['street']; ?>', '<?php echo $row['barangay']; ?>', '<?php echo $row['city']; ?>', '<?php echo $row['province']; ?>', '<?php echo $row['postalcode']; ?>', '<?php echo $row['birthdate']; ?>', '<?php echo $row['gender']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['role']; ?>')">Edit</button>


                                            <a href="student.php?archive=true&user_id=<?php echo $row['user_id']; ?>">
                                                <button class="btn btn-danger archive-btn">Archive</button>
                                            </a>
                                        <?php elseif ($row['is_archived'] == 1): ?>
                                            <a href="student.php?restore=true&user_id=<?php echo $row['user_id']; ?>">
                                                <button class="btn btn-success edit-btn">Restore</button>
                                            </a>
                                        <?php endif; ?>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No students found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
                    unset($_SESSION['success_message']);
                }

                if (isset($_SESSION['error_message'])) {
                    echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>


            </div>
        </div>

    </div>




    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit User</h2>
            <form action="" method="POST" class="edit-form-ins">
                <input type="hidden" id="editUserId" name="user_id">

                <!-- User Input Fields -->
                <div class="form-group-ins">
                    <label for="fname">First Name:</label>
                    <input type="text" id="editFname" name="fname" required>
                </div>
                <div class="form-group-ins">
                    <label for="mname">Middle Name:</label>
                    <input type="text" id="editMname" name="mname">
                </div>
                <div class="form-group-ins">
                    <label for="lname">Last Name:</label>
                    <input type="text" id="editLname" name="lname" required>
                </div>
                <div class="form-group-ins">
                    <label for="suffixname">Suffix Name:</label>
                    <input type="text" id="editSuffixname" name="suffixname" oninput="blockNumbers(event)">
                </div>
                <div class="form-group-ins">
                    <label for="contact_no">Contact Number:</label>
                    <input type="text" id="editContact" name="contact_no" required>
                </div>
                <!-- Address Input Fields -->
                <div class="form-group-ins">
                    <label for="houseno">House Number:</label>
                    <input type="text" id="editHouseno" name="houseno">
                </div>
                <div class="form-group-ins">
                    <label for="street">Street:</label>
                    <input type="text" id="editStreet" name="street">
                </div>
                <div class="form-group-ins">
                    <label for="barangay">Barangay:</label>
                    <input type="text" id="editBarangay" name="barangay">
                </div>
                <div class="form-group-ins">
                    <label for="city">City:</label>
                    <input type="text" id="editCity" name="city">
                </div>
                <div class="form-group-ins">
                    <label for="province">Province:</label>
                    <input type="text" id="editProvince" name="province">
                </div>
                <div class="form-group-ins">
                    <label for="postalcode">Postal Code:</label>
                    <input type="text" id="editPostalcode" name="postalcode">
                </div>
                <div class="form-group-ins">
                    <label for="birthdate">Birthdate:</label>
                    <input type="date" id="editBirthdate" name="birthdate">
                </div>
                <div class="form-group-ins">
                    <label for="gender">Gender:</label>
                    <select id="editGender" name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form-group-ins">
                    <label for="email">Email:</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                <div class="form-group-ins">
                    <label for="role">Role:</label>
                    <input type="text" id="editRole" name="role" required>
                </div>

                <button type="submit" name="edit" class="btn-ins">Save Changes</button>
            </form>

        </div>
    </div>

    <div class="Addmodal" id="addStudentModal">
        <div class="Addmodal-dialog">
            <div class="Addmodal-content">
                <div class="Addmodal-header" style="display: flex; justify-content: space-between;">
                    <h5 class="Addmodal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" id="closeAddModal" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="Addmodal-body">
                    <form method="POST" action="add_admin.php" id="addStudentForm">
                        <div class="form-group">
                            <label for="fname">First Name</label>
                            <input type="text" class="form-control" id="fname" name="fname" required>
                        </div>
                        <div class="form-group">
                            <label for="mname">Middle Name</label>
                            <input type="text" class="form-control" id="mname" name="mname">
                        </div>
                        <div class="form-group">
                            <label for="lname">Last Name</label>
                            <input type="text" class="form-control" id="lname" name="lname" required>
                        </div>
                        <div class="form-group">
                            <label for="suffixname">Suffix Name</label>
                            <input type="text" class="form-control" id="suffixname" name="suffixname">
                        </div>
                        <div class="form-group">
                            <label for="contact_no">Contact Number</label>
                            <input type="text" class="form-control" id="contact_no" name="contact_no" required>
                        </div>
                        <div class="form-group">
                            <label for="houseno">House Number</label>
                            <input type="text" class="form-control" id="houseno" name="houseno">
                        </div>
                        <div class="form-group">
                            <label for="street">Street</label>
                            <input type="text" class="form-control" id="street" name="street">
                        </div>
                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <input type="text" class="form-control" id="barangay" name="barangay">
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city">
                        </div>
                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" class="form-control" id="province" name="province">
                        </div>
                        <div class="form-group">
                            <label for="postalcode">Postal Code</label>
                            <input type="text" class="form-control" id="postalcode" name="postalcode">
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-blue" name="submit">Add Student</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- 
    <script src="main.js"></script> -->



    <script>
        const toggle = document.querySelector('.toggle');
        const navigation = document.querySelector('.navigation');

        toggle.addEventListener('click', () => {
            navigation.classList.toggle('active');
        });

        function toggleUser() {
            const userDropdown = document.querySelector('.dropdown-content');
            userDropdown.style.display = userDropdown.style.display === 'none' ? 'block' : 'none';
        }
        function showInstructorDropdown() {
            const instructorDropdown = document.querySelector('.instructorDropdown');
            instructorDropdown.style.display = instructorDropdown.style.display === 'none' ? 'block' : 'none';
        }

        function showStudentDropdown() {
            const studentDropdown = document.querySelector('.studentDropdown'); // Corrected variable name
            studentDropdown.style.display = studentDropdown.style.display === 'none' ? 'block' : 'none';
        }

        function showDepartmentDropdown() {
            const departmentDropdown = document.querySelector('.departmentDropdown'); // Corrected variable name
            departmentDropdown.style.display = departmentDropdown.style.display === 'none' ? 'block' : 'none';
        }

        function toggleSidebar() {
            const sidebar = document.querySelector('.navigation');
            sidebar.classList.toggle('collapsed');
        }

        function openEditModal(userId, fname, mname, lname, suffixname, contact, houseno, street, barangay, city, province, postalcode, birthdate, gender, email, role,) {
            document.getElementById('editUserId').value = userId;
            document.getElementById('editFname').value = fname;
            document.getElementById('editMname').value = mname;
            document.getElementById('editLname').value = lname;
            document.getElementById('editSuffixname').value = suffixname;
            document.getElementById('editContact').value = contact;
            // Populate address fields
            document.getElementById('editHouseno').value = houseno;
            document.getElementById('editStreet').value = street;
            document.getElementById('editBarangay').value = barangay;
            document.getElementById('editCity').value = city;
            document.getElementById('editProvince').value = province;
            document.getElementById('editPostalcode').value = postalcode;
            document.getElementById('editBirthdate').value = birthdate;
            document.getElementById('editGender').value = gender;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;

            // Show the modal
            document.getElementById('editModal').style.display = 'block';
        }

        function blockNumbers(event) {
            // Remove any digits (0-9) from the input value
            event.target.value = event.target.value.replace(/[0-9]/g, '');
        }


        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function (event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
        function openModal() {
            document.getElementById("addStudentModal").style.display = "block";
        }

        // Close the modal
        function closeModal() {
            document.getElementById("addStudentModal").style.display = "none";
        }

        // Event listener for closing the modal when the close button is clicked
        document.querySelector('#closeAddModal').addEventListener('click', function () {
            closeModal();
        });





    </script>

</body>

</html>