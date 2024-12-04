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

// Handle Assign Teacher to Class request (form submission)
if (isset($_POST['assign'])) {
    $teacher_type = $_POST['teacher_type'];
    $advisory_class_id = $_POST['advisory_class_id'];
    $sub_id = $_POST['sub_id'];
    $user_id = $_POST['user_id'];

    // Insert the assignment into the class_teacher table
    $stmt = $conn->prepare("INSERT INTO class_teacher (advisory_class_id, teacher_type, sub_id, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $advisory_class_id, $teacher_type, $sub_id, $user_id);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Teacher assigned successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all subjects, instructors, and classes
$subjectsQuery = "SELECT * FROM subject";
$subjectsResult = $conn->query($subjectsQuery);

$instructorsQuery = "SELECT * FROM users WHERE role = 'instructor'";
$instructorsResult = $conn->query($instructorsQuery);

$classesQuery = "SELECT * FROM class";
$classesResult = $conn->query($classesQuery);

// Fetch existing assignments (teacher-subject-class assignments)
$assignmentsQuery = "
    SELECT u.user_id, s.sub_id 
    FROM class_teacher ct
    JOIN users u ON ct.user_id = u.user_id
    JOIN subject s ON ct.sub_id = s.sub_id
    JOIN class c ON ct.advisory_class_id = c.class_id
";
$assignmentsResult = $conn->query($assignmentsQuery);

// Store already assigned teacher-subject combinations
$assignedTeachersSubjects = [];
while ($assignment = $assignmentsResult->fetch_assoc()) {
    $assignedTeachersSubjects[] = [
        'user_id' => $assignment['user_id'],
        'sub_id' => $assignment['sub_id']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teacher to Subject</title>
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

    <div class="">
        <h2>Assign Teacher to Subject</h2>

        <!-- Button to Open Modal -->
        <button id="openModalBtn">Add Teacher</button>

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModalBtn">&times;</span>
                <!-- Form to Assign Teacher to Class -->
                <form action="assign_teacher.php" method="POST">
                    <div class="form-group">
                        <label for="user_id">Select Instructor</label>
                        <select name="user_id" id="user_id" required>
                            <option value="">-- Select Instructor --</option>
                            <?php while ($instructor = $instructorsResult->fetch_assoc()): ?>
                                <?php 
                                // Check if the instructor is already assigned a subject
                                $isAssigned = false;
                                foreach ($assignedTeachersSubjects as $assigned) {
                                    if ($assigned['user_id'] == $instructor['user_id']) {
                                        $isAssigned = true;
                                        break;
                                    }
                                }
                                if (!$isAssigned): ?>
                                    <option value="<?php echo $instructor['user_id']; ?>">
                                        <?php echo $instructor['fname'] . ' ' . $instructor['lname']; ?>
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
                                <?php 
                                // Check if the subject is already assigned to any instructor
                                $isAssigned = false;
                                foreach ($assignedTeachersSubjects as $assigned) {
                                    if ($assigned['sub_id'] == $subject['sub_id']) {
                                        $isAssigned = true;
                                        break;
                                    }
                                }
                                if (!$isAssigned): ?>
                                    <option value="<?php echo $subject['sub_id']; ?>">
                                        <?php echo $subject['subjects']; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="advisory_class_id">Select Class</label>
                        <select name="advisory_class_id" id="advisory_class_id" required>
                            <option value="">-- Select Class --</option>
                            <?php while ($class = $classesResult->fetch_assoc()): ?>
                                <option value="<?php echo $class['class_id']; ?>">
                                    <?php echo $class['year_level']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="teacher_type">Teacher Type</label>
                        <select name="teacher_type" id="teacher_type" required>
                            <option value="Main">Main</option>
                            <option value="Assistant">Assistant</option>
                        </select>
                    </div>

                    <button type="submit" name="assign">Assign Teacher</button>
                </form>
            </div>
        </div>

        <h3>Current Assignments</h3>
        <!-- Display Assigned Teachers -->
        <table class="table">
            <thead>
                <tr>
                    <th>Instructor</th>
                    <th>Subject</th>
                    <th>Class</th>
                    <th>Teacher Type</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignment = $assignmentsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $assignment['fname'] . ' ' . $assignment['lname']; ?></td>
                        <td><?php echo $assignment['subjects']; ?></td>
                        <td><?php echo $assignment['year_level']; ?></td>
                        <td><?php echo $assignment['teacher_type']; ?></td>
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
