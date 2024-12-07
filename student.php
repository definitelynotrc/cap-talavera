<?php
// Database connection details
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
                if ($data[13] !== 'Student') {
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
        echo "<script>alert('User updated successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
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
    header("Location: student.php"); // Redirect to avoid resubmitting the form
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
    header("Location: student.php"); // Redirect to avoid resubmitting the form
    exit();
}

// Handle filtering based on active or archived status
$filter = isset($_GET['status']) ? $_GET['status'] : 'active';
if ($filter == 'archived') {
    // Retrieve archived students
    $sql = "SELECT * FROM users WHERE is_archived = 1 AND role = 'student'";
} else {
    // Retrieve active students
    $sql = "SELECT * FROM users WHERE is_archived = 0 AND role = 'student'";
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
    <link rel="stylesheet" href="style.css">
    <style>
        .success-message {
            color: green;
            font-weight: bold;
            padding: 10px;
            border: 1px solid green;
            background-color: #eafaf1;
        }

        .error-message {
            color: red;
            font-weight: bold;
            padding: 10px;
            border: 1px solid red;
            background-color: #fce4e4;
        }

        .Addmodal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            /* Black with opacity */
        }

        .Addmodal-dialog {
            position: relative;
            margin: 15% auto;
            max-width: 500px;
        }

        .Addmodal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .Addmodal-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .Addmodal-title {
            font-size: 1.25rem;
            margin: 0;
        }

        .close {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
        }

        .Addmodal-body {
            padding-top: 20px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-blue {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-blue:hover {
            background-color: #0056b3;

        }

        .btn1 {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn1:hover {
            background-color: #0056b3;

        }
    </style>
</head>

<body>

    <div class="containerr">
        <div class="navigation">
            <ul>
                <li>
                    <a href="index.php">
                        <span class="icon"><ion-icon name="school"></ion-icon></span>
                        <span class="title">NEUST</span>
                    </a>
                </li>

                <li id="dashboard">
                    <a href="dashboard.php"><span class="icon"><ion-icon name="home"></ion-icon></span><span
                            class="title">Dashboard</span></a>
                </li>
                <li id="instructor">
                    <a href="instructor.php"><span class="icon"><ion-icon name="person-add"></ion-icon></span><span
                            class="title">Instructor</span></a>
                </li>
                <li id="student">
                    <a href="student.php"><span class="icon"><ion-icon name="person-add"></ion-icon></span><span
                            class="title">Student</span></a>
                </li>
                <li id="department">
                    <a href="department.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Department</span></a>
                </li>
                <li id="subject">
                    <a href="subject.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Subject</span></a>
                </li>
                <li id="class">
                    <a href="class.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Class</span></a>
                </li>
                <li id="section">
                    <a href="section.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Section</span></a>
                </li>
                <li id="semester">
                    <a href="semester.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Semester</span></a>
                </li>
                <li id="academic">
                    <a href="acad_year.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Academic Year</span></a>
                </li>
                <li id="question">
                    <a href="question.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Question</span></a>
                </li>
                <li id="rate">
                    <a href="rate.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Rate</span></a>
                </li>
                <li id="evaluation">
                    <a href="evaluation.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Evaluation</span></a>
                </li>
            </ul>
        </div>

        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu"></ion-icon>
                </div>

                <div class="user">
                    <div class="dropdown">
                        <button class="dropdown-btn">
                            <img src="/img/admin.jpg" alt="User Profile" class="profile-img">
                        </button>
                        <div class="dropdown-content">
                            <a href="#">Manage Account</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Upload CSV File to Users Table</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="csvFile">Select CSV File:</label>
                <input type="file" name="csvFile" id="csvFile" required>
                <button type="submit" name="submit" class="btn1 btn-primary">Upload</button>
            </form>

            <!-- Dropdown to select Active/Archived users -->
            <form method="GET">
                <select name="status" onchange="this.form.submit()">
                    <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Active Students</option>
                    <option value="archived" <?php echo ($filter == 'archived') ? 'selected' : ''; ?>>Archived Students
                    </option>
                </select>
            </form>

            <h2><?php echo ucfirst($filter); ?> Students</h2>
            <!-- Button to open the modal -->
            <button type="button" class="btn1 btn-primary" style="" onclick="openModal()">Add Student Manually</button>

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
                    <form method="POST" action="add_student.php" id="addStudentForm">
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

    <script src="main.js"></script>
    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>


    <script>
        // JavaScript to open the modal and populate the user data for editing
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

        // Optional: Close the modal if the user clicks outside the modal content


    </script>

</body>

</html>