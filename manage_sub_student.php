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

// Handle Assign Student to Subject request (form submission)
if (isset($_POST['assign_student'])) {
    $student_id = $_POST['student_id'];
    $sub_id = $_POST['sub_id'];
    // Assuming advisory_class_id is already provided or needs to be fetched
    $advisory_class_id = 1; // Replace with the actual logic if needed

    // Insert the assignment into the class_teacher table
    $stmt = $conn->prepare("INSERT INTO class_teacher (advisory_class_id, teacher_type, sub_id, user_id) VALUES (?, 'student', ?, ?)");
    $stmt->bind_param("iii", $advisory_class_id, $sub_id, $student_id); // Bind parameters

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Student assigned to subject successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all subjects
$subjectsQuery = "SELECT * FROM subject";
$subjectsResult = $conn->query($subjectsQuery);

// Fetch all users who are students (assuming 'role' column exists)
$studentsQuery = "SELECT * FROM users WHERE role = 'student'";  // Filter users by 'student' role
$studentsResult = $conn->query($studentsQuery);

// Fetch existing student-subject assignments
$assignmentsQuery = "
    SELECT st.user_id, su.sub_id, st.fname, st.lname, su.subjects
    FROM class_teacher ct
    JOIN users st ON ct.user_id = st.user_id
    JOIN subject su ON ct.sub_id = su.sub_id
    WHERE ct.teacher_type = 'student'
";
$assignmentsResult = $conn->query($assignmentsQuery);

// Store already assigned student-subject combinations
$assignedStudentsSubjects = [];
while ($assignment = $assignmentsResult->fetch_assoc()) {
    $assignedStudentsSubjects[] = [
        'student_id' => $assignment['user_id'],
        'sub_id' => $assignment['sub_id']
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Student to Subject</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Styling for buttons and form */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="containerr">
    <div class="navigation">
        <ul>
            <li><a href="index.php"><span class="icon"><ion-icon name="school"></ion-icon></span><span class="title">NEUST</span></a></li>
            <li id="dashboard"><a href="dashboard.php"><span class="icon"><ion-icon name="home"></ion-icon></span><span class="title">Dashboard</span></a></li>
            <li id="subject"><a href="subject.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Subject</span></a></li>
            <li id="class"><a href="class.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Class</span></a></li>
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

        <h2>Assign Student to Subject</h2>

        <!-- Button to Open Modal -->
        <button id="openModalBtn">Add Student</button>

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModalBtn">&times;</span>
                <!-- Form to Assign Student to Subject -->
                <form action="assign_student.php" method="POST">
                    <div class="form-group">
                        <label for="student_id">Select Student</label>
                        <select name="student_id" id="student_id" required>
                            <option value="">-- Select Student --</option>
                            <?php while ($student = $studentsResult->fetch_assoc()): ?>
                                <?php 
                                // Check if the student is already assigned a subject
                                $isAssigned = false;
                                foreach ($assignedStudentsSubjects as $assigned) {
                                    if ($assigned['student_id'] == $student['user_id']) {
                                        $isAssigned = true;
                                        break;
                                    }
                                }
                                if (!$isAssigned): ?>
                                    <option value="<?php echo $student['user_id']; ?>">
                                        <?php echo $student['fname'] . ' ' . $student['lname']; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sub_id">Select Subject</label>
                        <select name="sub_id" id="sub_id" required>
                            <option value="">-- Select Subject --</option>
                            <?php while ($subject = $subjectsResult->fetch_assoc()): ?>
                                <option value="<?php echo $subject['sub_id']; ?>">
                                    <?php echo $subject['subjects']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" name="assign_student">Assign Student</button>
                </form>
            </div>
        </div>

        <h3>Current Assignments</h3>
        <!-- Display Assigned Students -->
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignment = $assignmentsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $assignment['fname'] . ' ' . $assignment['lname']; ?></td>
                        <td><?php echo $assignment['subjects']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="main.js"></script>
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("openModalBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementById("closeModalBtn");

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>

