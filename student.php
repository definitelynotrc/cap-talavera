<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap"; // Replace with your actual database name 'cap'

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle CSV file upload and database insertion
if (isset($_POST['submit']) && isset($_FILES['csvFile'])) {
    // Get the uploaded file details
    $file = $_FILES['csvFile'];

    // Check if the file was uploaded without errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Get file info
        $fileTmpName = $file['tmp_name'];
        $fileName = $file['name'];

        // Check if the file is a CSV file (optional)
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (strtolower($fileExtension) !== 'csv') {
            echo "Sorry, only CSV files are allowed.";
            exit;
        }

        if (($handle = fopen($fileTmpName, 'r')) !== FALSE) {
            // Skip the first row if it contains headers (optional)
            fgetcsv($handle);

            // Prepare the SQL insert query (16 values, including is_archived)
            $stmt = $conn->prepare("
                INSERT INTO users (
                    fname, mname, lname, suffixname, contact_no, houseno, street, barangay, city, 
                    province, postalcode, birthdate, gender, role, email, is_archived
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
        
            // Loop through each row in the CSV
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Skip if the role is not 'student'
                if ($data[13] !== 'student') {
                    continue;
                }

                // Ensure each row has exactly 16 columns (match SQL query columns)
                while (count($data) < 16) {
                    $data[] = "";  // Pad with empty values (or NULL if you prefer)
                }

                // Convert the birthdate to the correct format (YYYY-MM-DD)
                $birthdate = DateTime::createFromFormat('d/m/Y', $data[11]); // Assuming it's in d/m/Y format
                if ($birthdate) {
                    $data[11] = $birthdate->format('Y-m-d'); // Convert to YYYY-MM-DD format
                }

                // Bind the CSV data to the SQL query (16 parameters)
                $stmt->bind_param(
                    "ssssssssssssssss", 
                    $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], 
                    $data[6], $data[7], $data[8], $data[9], $data[10], 
                    $data[11], $data[12], $data[13], $data[14], $data[15]
                );

                // Execute the statement
                $stmt->execute();
            }

            // Close the file handle and prepared statement
            fclose($handle);
            $stmt->close();
        }
        
    } else {
        echo "Error uploading the file.";
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
        $firstname, $middlename, $lastname, $suffixname, $contact, $houseno, $street, $barangay, 
        $city, $province, $postalcode, $birthdate, $gender, $email, $role, $user_id
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
                <a href="dashboard.php"><span class="icon"><ion-icon name="home"></ion-icon></span><span class="title">Dashboard</span></a>
            </li>
            <li id="instructor">
                <a href="instructor.php"><span class="icon"><ion-icon name="person-add"></ion-icon></span><span class="title">Instructor</span></a>
            </li>
            <li id="student">
                <a href="student.php"><span class="icon"><ion-icon name="person-add"></ion-icon></span><span class="title">Student</span></a>
            </li>
            <li id="department">
                <a href="department.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Department</span></a>
            </li>
            <li id="subject">
                <a href="subject.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Subject</span></a>
            </li>
            <li id="class">
                <a href="class.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Class</span></a>
            </li>
            <li id="section">
                <a href="section.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Section</span></a>
            </li>
            <li id="semester">
                <a href="semester.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Semester</span></a>
            </li>
            <li id="academic">
                <a href="acad_year.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Academic Year</span></a>
            </li>
            <li id="question">
                <a href="question.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Question</span></a>
            </li>
            <li id="rate">
                <a href="rate.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Rate</span></a>
            </li>
            <li id="evaluation">
                <a href="evaluation.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Evaluation</span></a>
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
            <button type="submit" name="submit" class="btn btn-primary">Upload</button>
        </form>

        <!-- Dropdown to select Active/Archived users -->
        <form method="GET">
            <select name="status" onchange="this.form.submit()">
                <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Active Students</option>
                <option value="archived" <?php echo ($filter == 'archived') ? 'selected' : ''; ?>>Archived Students</option>
            </select>
        </form>

        <h2><?php echo ucfirst($filter); ?> Students</h2>
        <!-- Make the table responsive -->
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
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']. ' ' . $row['suffixname']; ?></td>
                                <td><?php echo $row['contact_no']; ?></td>
                                <td><?php echo $row['gender']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['role']; ?></td>
                                <td>
                                    <!-- Action buttons -->
                                    <?php if ($row['is_archived'] == 0): ?>
                                        <button class="btn btn-success edit-btn" onclick="openEditModal(<?php echo $row['user_id']; ?>, '<?php echo $row['fname']; ?>', '<?php echo $row['mname']; ?>', '<?php echo $row['lname']; ?>', '<?php echo $row['suffixname']; ?>',  '<?php echo $row['contact_no']; ?>', '<?php echo $row['houseno']; ?>', '<?php echo $row['street']; ?>', '<?php echo $row['barangay']; ?>', '<?php echo $row['city']; ?>', '<?php echo $row['province']; ?>', '<?php echo $row['postalcode']; ?>', '<?php echo $row['birthdate']; ?>', '<?php echo $row['gender']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['role']; ?>')">Edit</button>
                                    
                
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
                        <tr><td colspan="6">No students found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
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


<script src="main.js"></script>
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
    

<script>
// JavaScript to open the modal and populate the user data for editing
function openEditModal(userId, fname, mname, lname, suffixname, contact, houseno, street, barangay, city, province, postalcode, birthdate,  gender, email, role,) {
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
window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeEditModal();
    }
}

</script>

</body>
</html>
