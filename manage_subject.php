<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
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



// Fetch all subjects, instructors, and classes
$subjectsQuery = "
    SELECT subject.*, dep_sub.dep_id, dep_sub.sem_id, semester.semesters, department.department
    FROM subject
    JOIN dep_sub ON subject.sub_id = dep_sub.sub_id
    JOIN semester ON dep_sub.sem_id = semester.sem_id
    JOIN department ON dep_sub.dep_id = department.dep_id

";
$subjectsResult = $conn->query($subjectsQuery);


$instructorsQuery = "
    SELECT u.*, d.department
    FROM users u
    JOIN user_dep ud ON u.user_id = ud.user_id
    JOIN department d ON ud.dep_id = d.dep_id
    WHERE u.role = 'Instructor'
";
$instructorsResult = $conn->query($instructorsQuery);


$classesQuery = "SELECT * FROM class";
$classesResult = $conn->query($classesQuery);

// Fetch existing assignments (teacher-subject-class assignments)
$assignmentsQuery = "
    SELECT 
        u.user_id, 
        u.fname, 
        u.lname, 
        s.sub_id, 
        s.subjects, 
        c.year_level, 
        ct.teacher_type,
        ct.advisory_class_id,
        ac.advisory_class_id AS advisory_class_table_id
    FROM class_teacher ct
    LEFT JOIN users u ON ct.user_id = u.user_id
    LEFT JOIN subject s ON ct.sub_id = s.sub_id
    LEFT JOIN advisory_class ac ON ct.advisory_class_id = ac.advisory_class_id
    LEFT JOIN class c ON ac.class_id = c.class_id
    ORDER BY u.fname, u.lname;
";



$assignmentsResult = $conn->query($assignmentsQuery);



$assignments = [];
while ($assignment = $assignmentsResult->fetch_assoc()) {
    if (is_null($assignment['year_level'])) {
        echo "Missing year level for advisory_class_id: " . $assignment['advisory_class_id'] . "<br>";
    }
    $assignments[] = $assignment;
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
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            /* Black background with opacity */
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

            <div class="">
                <h2>Assign Teacher to Subject</h2>

                <!-- Button to Open Modal -->
                <button id="openModalBtn">Add Teacher</button>

                <!-- Modal -->
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeModalBtn">&times;</span>

                        <form action="assign_teacher.php" method="POST">
                            <div class="form-group">
                                <label for="user_id">Select Instructor</label>
                                <select name="user_id" id="user_id" required>
                                    <option value="">-- Select Instructor --</option>
                                    <?php while ($instructor = $instructorsResult->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($instructor['user_id']); ?>">
                                            <?php echo htmlspecialchars($instructor['fname'] . ' ' . $instructor['lname']) . ' (' . htmlspecialchars($instructor['department']) . ')'; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sub_id">Select Subject</label>
                                <select name="sub_id" id="sub_id" required>
                                    <option value="">-- Select Subject --</option>
                                    <?php while ($subject = $subjectsResult->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($subject['sub_id']); ?>">
                                            <?php echo htmlspecialchars($subject['subjects']) . ' (' . htmlspecialchars($subject['semesters']) . ') (' . htmlspecialchars($subject['department']) . ')'; ?>

                                        </option>
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
                            <th>Year Level</th>
                            <th>Teacher Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['fname'] . ' ' . $assignment['lname']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['subjects']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['year_level']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['teacher_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
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
            btn.onclick = function () {
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function () {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>

</body>

</html>