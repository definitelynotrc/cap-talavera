<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include('db.php'); // Query to count the number of students
$students_query = "SELECT COUNT(*) AS total_students FROM users WHERE role = 'student'";
$students_stmt = $pdo->
    prepare($students_query);
$students_stmt->execute();
$total_students_row = $students_stmt->fetch(PDO::FETCH_ASSOC);
$total_students = $total_students_row['total_students'];

// Query to count the number of instructors
$instructors_query = "SELECT COUNT(*) AS total_instructors FROM users WHERE role = 'instructor'";
$instructors_stmt = $pdo->prepare($instructors_query);
$instructors_stmt->execute();
$total_instructors_row = $instructors_stmt->fetch(PDO::FETCH_ASSOC);
$total_instructors = $total_instructors_row['total_instructors'];

$allUsersQuery = $instructors_query = "SELECT COUNT(*) AS total_users FROM users";
$allUsersStmt = $pdo->prepare($allUsersQuery);
$allUsersStmt->execute();
$allUsers = $allUsersStmt->fetch(PDO::FETCH_ASSOC);
$allUsers = $allUsers['total_users'];

$studentsPerDepartmentQuery = "
    SELECT
    d.department,
    COUNT(u.user_id) AS total_students
    FROM
    user_dep ud
    JOIN
    department d ON ud.dep_id = d.dep_id
    JOIN
    users u ON ud.user_id = u.user_id
    WHERE
    u.role = 'Student'
    AND d.department = 'BSIT'
    GROUP BY
    d.department
    ";

$stmt = $pdo->prepare($studentsPerDepartmentQuery);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$totalStudentsBSIT = !empty($result) ? $result['total_students'] : 0;

$studentsPerDepartmentQuery = "
    SELECT
    d.department,
    COUNT(u.user_id) AS total_students
    FROM
    user_dep ud
    JOIN
    department d ON ud.dep_id = d.dep_id
    JOIN
    users u ON ud.user_id = u.user_id
    WHERE
    u.role = 'Student'
    AND d.department = 'BEED'
    GROUP BY
    d.department
    ";

$stmt = $pdo->prepare($studentsPerDepartmentQuery);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$totalStudentsBEED = !empty($result) ? $result['total_students'] : 0;

$studentsPerDepartmentQuery = "
    SELECT
    d.department,
    COUNT(u.user_id) AS total_students
    FROM
    user_dep ud
    JOIN
    department d ON ud.dep_id = d.dep_id
    JOIN
    users u ON ud.user_id = u.user_id
    WHERE
    u.role = 'Student'
    AND d.department = 'BSBA'
    GROUP BY
    d.department
    ";

$stmt = $pdo->prepare($studentsPerDepartmentQuery);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$totalStudentsBSBA = !empty($result) ? $result['total_students'] : 0;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="">
    <style>
        @import url("https://font.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");

        * {
            font-family: "Ubuntu", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
        }



        nav.topbar {
            background-color: #2A2185;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            height: 60px;
            width: 100%;

            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }



        .toggle {
            margin-left: 200px;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .logo a {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            color: white;
        }

        aside.navigation {
            background-color: #2A2185;
            color: white;
            width: 200px;

            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 20px 20px;
            position: fixed;
            transition: width 0.3s;
            top: 0;



        }


        .navigation.collapsed {
            width: 70px;

        }


        .navigation li .icon {
            margin-right: 10px;
        }

        .navigation li .title {
            display: inline-block;
            transition: opacity 0.3s ease;
        }

        .navigation.collapsed .title {
            display: none;
        }



        .navigation.collapsed~.main {
            margin-left: 70px;
        }

        .navigation ul {
            list-style-type: none;
            padding: 0;

        }

        .navigation ul li {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            font-size: 1.1rem;
            cursor: pointer;
        }

        .navigation ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;

        }

        .instructorDropdown,
        .studentDropdown,
        .departmentDropdown {
            display: none;
            list-style-type: none;
            padding: 0;
            margin-left: 20px;
        }

        .instructorDropdown ul li,
        .studentDropdown ul li,
        .departmentDropdown ul li {
            margin-bottom: 5px;
        }


        .instructorDropdown ul li a,
        .studentDropdown ul li a,
        .departmentDropdown ul li a {
            color: white;
            text-decoration: none;
            font-size: 0.5rem;
            display: flex;
            align-items: center;
        }

        .instructorDropdown ul li a span,
        .studentDropdown ul li a span,
        .departmentDropdown ul li a span {
            margin-left: 10px;


        }

        #instructor,
        #student,
        #department {
            display: flex;
            flex-direction: column;
        }

        .main {
            padding: 20px;
            margin-left: 200px;
            transition: margin-left 0.3s;

        }

        .container {
            display: flex;
            width: 100%;
            flex-direction: row;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-btn {
            background-color: transparent;

            border: none;
            cursor: pointer;
        }

        .dropdown-btn img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .dropdown-content {

            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
            z-index: 1;
            right: 10px;
            display: flex;
            flex-direction: column;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
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



            <div class="cardBox" style="display: flex; gap: 10px; flex-wrap: wrap; ">
                <!-- Static Cards -->
                <div class="card"
                    style="background-color: #2A2185; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center; flex: 1; min-width: 250px; max-width: 300px;">
                    <div>
                        <div class="numbers" style="font-size: 2rem; font-weight: bold; color: white;">
                            <?php echo $total_students; ?>
                        </div>
                        <div class="cardName" style="font-size: 1.2rem; margin-top: 10px;">Total Students</div>
                    </div>
                </div>

                <div class="card"
                    style="background-color: #2A2185; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center; flex: 1; min-width: 250px; max-width: 300px;">
                    <div>
                        <div class="numbers" style="font-size: 2rem; font-weight: bold; color: white;">
                            <?php echo $total_instructors; ?>
                        </div>
                        <div class="cardName" style="font-size: 1.2rem; margin-top: 10px;">Total Instructors</div>
                    </div>
                </div>

                <div class="card"
                    style="background-color: #2A2185; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center; flex: 1; min-width: 250px; max-width: 300px;">
                    <div>
                        <div class="numbers" style="font-size: 2rem; font-weight: bold; color: white;">
                            <?php echo $allUsers; ?>
                        </div>
                        <div class="cardName" style="font-size: 1.2rem; margin-top: 10px;">Total Users</div>
                    </div>
                </div>

                <div class="card"
                    style="background-color: #2A2185; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center; flex: 1; min-width: 250px; max-width: 300px;">
                    <div>
                        <div class="numbers" style="font-size: 2rem; font-weight: bold; color: white;">
                            <?php echo $totalStudentsBSIT; ?>
                        </div>
                        <div class="cardName" style="font-size: 1.2rem; margin-top: 10px;">BSIT Students</div>
                    </div>
                </div>

                <div class="card"
                    style="background-color: #2A2185; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center; flex: 1; min-width: 250px; max-width: 300px;">
                    <div>
                        <div class="numbers" style="font-size: 2rem; font-weight: bold; color: white;">
                            <?php echo $totalStudentsBEED; ?>
                        </div>
                        <div class="cardName" style="font-size: 1.2rem; margin-top: 10px;">BEED Students</div>
                    </div>
                </div>

                <div class="card"
                    style="background-color: #2A2185; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center; flex: 1; min-width: 250px; max-width: 300px;">
                    <div>
                        <div class="numbers" style="font-size: 2rem; font-weight: bold; color: white;">
                            <?php echo $totalStudentsBSBA; ?>
                        </div>
                        <div class="cardName" style="font-size: 1.2rem; margin-top: 10px;">BSBA Students</div>
                    </div>
                </div>

                <!-- Dynamic Cards for Year Levels -->
                <?php
                $yearLevels = [1, 2, 3, 4]; // Year levels to display
                
                foreach ($yearLevels as $yearLevel) {
                    try {
                        // Prepare the query to count unique subjects per year level
                        $query = "
                SELECT 
                    c.year_level,
                    COUNT(DISTINCT sub.sub_id) AS unique_subject_count
                FROM 
                    class c
                JOIN 
                    section s ON c.class_id = s.class_id
                JOIN 
                    section_subjec ss ON s.section_id = ss.section_id
                JOIN 
                    subject sub ON ss.sub_id = sub.sub_id
                WHERE 
                    c.year_level = ?
                GROUP BY 
                    c.year_level
            ";

                        // Prepare the statement
                        $stmt = $pdo->prepare($query);

                        // Bind the parameter
                        $stmt->bindParam(1, $yearLevel, PDO::PARAM_INT);

                        // Execute the query
                        $stmt->execute();

                        // Fetch the result
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Get the count of unique subjects
                        $uniqueSubjectCount = $result['unique_subject_count'] ?? 0;
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                        die();
                    }
                    ?>

                    <!-- Card for each Year Level -->
                    <div class="card"
                        style="background-color: #2A2185; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center; flex: 1; min-width: 250px; max-width: 300px;">

                        <div>
                            <div class="numbers" style="font-size: 1.9rem; font-weight: bold; color: white;">
                                Year level <?php echo htmlspecialchars($yearLevel); ?>

                            </div>
                            <div class="cardName" style="font-size: 1.2rem; margin-top: 10px;">
                                <?php echo $uniqueSubjectCount; ?> Subjects
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>

            <?php
            // Query to fetch the highest evaluation rating
            $highestEvaluationQuery = "
    SELECT 
        e.eval_id,
        e.remarks,
        e.rate_result,
        e.date_created,
        u.fname AS instructor_fname,
        u.lname AS instructor_lname,
        ct.teacher_type
    FROM evaluation e
    JOIN class_teacher ct ON e.class_teacher_id = ct.class_teacher_id
    JOIN users u ON ct.user_id = u.user_id
    ORDER BY e.rate_result DESC
    LIMIT 1
";

            $stmt = $pdo->prepare($highestEvaluationQuery);
            $stmt->execute(); // No need to bind parameters here since the query doesn't have placeholders.
            $highestEvaluation = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch recent evaluations for the table
            $recentEvaluationsQuery = "
    SELECT 
        e.eval_id,
        e.remarks,
        e.rate_result,
        e.date_created,
        u.fname AS instructor_fname,
        u.lname AS instructor_lname,
        ct.teacher_type
    FROM evaluation e
    JOIN class_teacher ct ON e.class_teacher_id = ct.class_teacher_id
    JOIN users u ON ct.user_id = u.user_id
    ORDER BY e.date_created DESC
    LIMIT 10
";

            $stmt = $pdo->prepare($recentEvaluationsQuery);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Display highest evaluation
            if ($highestEvaluation): ?>
                <div
                    style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-top: 20px; margin-bottom: 30px;">
                    <h3 style="font-size: 1.6rem; color: #2A2185; font-weight: bold; margin-bottom: 15px;">Highest
                        Evaluation</h3>
                    <p style="font-size: 1.1rem; color: #333; line-height: 1.6;">
                        <strong style="color: #2A2185;">Instructor:</strong>
                        <?php echo $highestEvaluation['instructor_fname'] . ' ' . $highestEvaluation['instructor_lname']; ?><br>
                        <strong style="color: #2A2185;">Teacher Type:</strong>
                        <?php echo $highestEvaluation['teacher_type']; ?><br>
                        <strong style="color: #2A2185;">Overall Rating:</strong>
                        <?php echo $highestEvaluation['rate_result']; ?><br>

                    </p>
                </div>
            <?php endif; ?>


            <?php
            // Query to fetch recently evaluated instructors
            $recentEvaluationsQuery = "
    SELECT 
        e.eval_id,
        e.transaction_code,
        e.remarks,
        e.rate_result,
        e.date_created,
        u.fname AS instructor_fname,
        u.lname AS instructor_lname,
        ct.teacher_type
    FROM evaluation e
    JOIN class_teacher ct ON e.class_teacher_id = ct.class_teacher_id
    JOIN users u ON ct.user_id = u.user_id
    ORDER BY e.date_created DESC
    LIMIT 10
";

            $stmt = $pdo->prepare($recentEvaluationsQuery);
            $stmt->execute(); // No need to bind parameters here since the query doesn't have placeholders.
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) > 0): ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px; ">
                    <caption style="font-size: 1.2rem; font-weight: bold; margin-bottom: 10px;">Recent Evaluations
                    </caption>
                    <thead>
                        <tr style="background-color: #2A2185; color: white;">
                            <th style="padding: 10px; border: 1px solid #ddd;">Transaction Code</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Instructor</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Teacher Type</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Remarks</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Rating</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Date Evaluated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $row): ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                                    <?php echo $row['transaction_code']; ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo $row['instructor_fname'] . ' ' . $row['instructor_lname']; ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $row['teacher_type']; ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $row['remarks']; ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                                    <?php echo $row['rate_result']; ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                                    <?php echo date("F j, Y, g:i a", strtotime($row['date_created'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="margin-top: 20px;">No recent evaluations found.</p>
            <?php endif; ?>

        </div>

    </div>


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
    </script>
    </script>
    <!-- <script src="main.js"></script> -->


</body>

</html>