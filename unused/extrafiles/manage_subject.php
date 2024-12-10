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
    <link rel="stylesheet" href="sidebar.css">
    <style>
        /* Modal styles */
        .myModal {
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

        .mymodal-content {
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

        #openModalBtn {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
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

                    <a href="">
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
                    <a href="eval_result.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24"
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
            <div class="">
                <h2>Assign Teacher to Subject</h2>

                <!-- Button to Open Modal -->
                <button id="openModalBtn">Assign Subject</button>

                <!-- Modal -->
                <div id="myModal" class="myModal">
                    <div class="mymodal-content">
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