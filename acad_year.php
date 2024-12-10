<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap"; // Replace with your actual database name

// Establishing a PDO connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Validate year_start format
function isValidYearStart($yearStart)
{
    return preg_match('/^\d{4}-\d{4}$/', $yearStart);
}



// Handle archiving an academic year (set isActive to 0)
if (isset($_GET['archive']) && isset($_GET['ay_id'])) {
    $ay_id = $_GET['ay_id'];
    try {
        $stmt = $pdo->prepare("UPDATE acad_year SET isActive = 0 WHERE ay_id = :ay_id");
        $stmt->bindParam(':ay_id', $ay_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: acad_year.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle restoring an archived academic year (set isActive to 1)
if (isset($_GET['restore']) && isset($_GET['ay_id'])) {
    $ay_id = $_GET['ay_id'];
    try {
        $stmt = $pdo->prepare("UPDATE acad_year SET isActive = 1 WHERE ay_id = :ay_id");
        $stmt->bindParam(':ay_id', $ay_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: acad_year.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all academic years
try {
    $stmt = $pdo->prepare("SELECT * FROM acad_year ORDER BY year_start DESC");
    $stmt->execute();
    $academicYears = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


if (isset($_GET['ay_id'])) {
    $ay_id = $_GET['ay_id']; // Get the ay_id from the URL
    try {
        $stmt = $pdo->prepare("SELECT * FROM acad_year WHERE ay_id = :ay_id");
        $stmt->bindParam(':ay_id', $ay_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo "<script>alert('Academic Year not found');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}


// Include the PDO connection


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['year_start'])) {
    $yearStart = $_POST['year_start'];
    $isActive = isset($_POST['isActive']) ? 1 : 0; // Check if 'isActive' is checked

    // Prepare the SQL statement using PDO
    $sql = "INSERT INTO acad_year (year_start, isActive) VALUES (:year_start, :isActive)";
    $stmt = $pdo->prepare($sql);

    // Bind the parameters to the prepared statement
    $stmt->bindParam(':year_start', $yearStart, PDO::PARAM_STR);
    $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        echo 'Success';  // Optionally, send a success response
    } else {
        echo 'Failed';  // Optionally, handle failure
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ay_id'])) {
    $ayId = $_POST['ay_id'];
    $yearStart = $_POST['year_start'];
    $isActive = isset($_POST['isActive']) ? 1 : 0; // Check if 'isActive' is checked

    // Prepare the SQL statement using PDO
    $sql = "UPDATE acad_year SET year_start = :year_start, isActive = :isActive WHERE ay_id = :ay_id";
    $stmt = $pdo->prepare($sql);

    // Bind the parameters to the prepared statement
    $stmt->bindParam(':year_start', $yearStart, PDO::PARAM_STR);
    $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);
    $stmt->bindParam(':ay_id', $ayId, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        echo 'Success';  // Optionally, send a success response
        header("Location: acad_year.php");
    } else {
        echo 'Failed';  // Optionally, handle failure
    }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="sidebar.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
        .archive-btn {
            background-color: #ff6347;
            color: white;
        }

        .btn {
            text-decoration: none;
            padding: 6px 12px;
            margin: 0 5px;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background-color: #4caf50;

            color: white;
        }

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


        .newmodal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            display: flex;
            flex-direction: column;

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

        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
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

        #addAcadYearBtn {
            background-color: transparent !important;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .view-archived-btn {
            background-color: #2A2185;
            color: white;
            padding: 10px 10px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .view-archived-btn:hover {
            background-color: #1d175d;
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
                        <li><a href="class_teacher.php">Instructor Subjects</a></li>
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
                        <li><a href="add_subject_student.php">Student Sections</a></li>
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
                        <li>
                            <a href="advisory_class.php">Manage Advisory Class</a>
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
            <div style="display:flex ; flex-direction: row; width: 100%; justify-content: space-between;">
                <a href="acad_year.php<?php echo isset($_GET['archived']) && $_GET['archived'] == 'true' ? '' : '?archived=true'; ?>"
                    class="btn view-archived-btn">
                    <?php echo isset($_GET['archived']) && $_GET['archived'] == 'true' ? 'View Actives' : 'View Archived '; ?>
                </a>
                <button id="addBtn" style=" background-color: none;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 12H16" stroke="#292D32" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 16V8" stroke="#292D32" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z"
                            stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

            </div>

            <!-- Academic Year List -->
            <h2>Academic Year List</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Year Start</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $acadYear): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($acadYear['year_start']); ?></td>
                                <td><?php echo $acadYear['isActive'] == 1 ? 'Active' : 'Archived'; ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn edit-btn" data-ay-id="<?php echo $acadYear['ay_id']; ?>"
                                        data-year-start="<?php echo $acadYear['year_start']; ?>"
                                        data-is-active="<?php echo $acadYear['isActive']; ?>">Edit</button>


                                    <a href="?archive=true&ay_id=<?php echo $acadYear['ay_id']; ?>"
                                        class="btn archive-btn">Archive</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No active academic years found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Archived Academic Years Section -->
            <?php if (isset($_GET['archived']) && $_GET['archived'] == 'true'): ?>
                <h2>Archived Academic Years</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Year Start</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($archivedAcademicYears)): ?>
                            <?php foreach ($archivedAcademicYears as $acadYear): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($acadYear['year_start']); ?></td>
                                    <td><?php echo $acadYear['isActive'] == 1 ? 'Active' : 'Archived'; ?></td>
                                    <td>
                                        <a href="?restore=true&ay_id=<?php echo $acadYear['ay_id']; ?>"
                                            class="btn restore-btn">Restore</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No archived academic years found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <div id="addAcademicYearModal" class="modal">
        <div class="newmodal-content" style="display: flex; flex-direction: column; gap: 20px;">
            <span class="close">&times;</span>
            <form id="addAcademicYearForm" method="POST" action="acad_year.php">
                <div class="form-group">
                    <label for="year_start">Academic Year Start:</label>
                    <input type="text" name="year_start" id="year_start" required>
                </div>
                <div class="form-group">
                    <label for="isActive">Is Active:</label>
                    <input type="checkbox" name="isActive" id="isActive">
                </div>
                <button type="submit">Add</button>
            </form>
        </div>
    </div>


    <div id="editAcademicYearModal" class="modal" style="display: none;">
        <div class="newmodal-content">
            <span class="close">&times;</span>
            <form id="editAcademicYearForm" method="POST" action="acad_year.php">
                <div class="form-group"> <label for="year_start">Academic Year Start:</label>
                    <input type="text" name="year_start" id="edit_year_start" required>
                </div>
                <input type="hidden" name="ay_id" id="edit_ay_id">
                <div class="form-group">
                    <label for="isActive">Is Active:</label>
                    <input type="checkbox" name="isActive" id="edit_isActive">
                </div>
                <button type="submit">Save</button>
            </form>
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
        // Get Add Modal and Button
        const addModal = document.querySelector('#addAcademicYearModal');
        const addBtn = document.querySelector('#addBtn');
        const addClose = addModal.querySelector('.close');

        // Open Add Modal
        addBtn.addEventListener('click', () => {
            const form = addModal.querySelector('form');
            form.reset(); // Reset form fields
            addModal.style.display = 'block'; // Show the modal
        });

        // Close Add Modal
        addClose.addEventListener('click', () => {
            addModal.style.display = 'none';
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === addModal) {
                addModal.style.display = 'none';
            }
        });

        $(document).ready(function () {
            // When the Edit button is clicked
            $('.edit-btn').click(function () {
                // Get the data attributes from the button
                var ayId = $(this).data('ay-id');
                var yearStart = $(this).data('year-start');
                var isActive = $(this).data('is-active') == 1;  // Convert to boolean

                // Fill the modal form with the current values
                $('#edit_ay_id').val(ayId);  // Fill the hidden input with ay_id
                $('#edit_year_start').val(yearStart);  // Fill the year_start input
                $('#edit_isActive').prop('checked', isActive);  // Set checkbox based on isActive

                // Show the modal
                $('#editAcademicYearModal').show();
            });

            // When the close button (X) is clicked, hide the modal
            $('.close').click(function () {
                $('#editAcademicYearModal').hide();
            });

            // Handle the form submission
            $('#editAcademicYearForm').submit(function (e) {
                e.preventDefault(); // Prevent the form from submitting normally

                // Serialize the form data
                var formData = $(this).serialize();

                // Use AJAX to submit the form without reloading the page
                $.ajax({
                    url: 'acad_year.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        // Handle success (you can show a success message or update the table dynamically)
                        alert('Academic year updated successfully!');
                        $('#editAcademicYearModal').hide();  // Hide the modal after success
                    },
                    error: function () {
                        alert('Error updating academic year!');
                    }
                });
            });
        });
        $(document).ready(function () {
            // Show the modal when 'Add Academic Year' button is clicked (add button needs to be implemented on your page)
            $('#addAcademicYearBtn').click(function () {
                $('#addAcademicYearModal').show();
            });

            // Close the modal when the 'X' button is clicked
            $('.close').click(function () {
                $('#addAcademicYearModal').hide();
            });

            // Handle form submission with AJAX (optional)
            $('#addAcademicYearForm').submit(function (e) {
                e.preventDefault(); // Prevent the form from submitting normally

                var formData = $(this).serialize(); // Serialize form data

                // Use AJAX to submit the form without reloading the page
                $.ajax({
                    url: 'acad_year.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        // Handle success (you can show a success message or update the table dynamically)
                        alert('Academic year added successfully!');
                        $('#addAcademicYearModal').hide();  // Hide the modal after success
                    },
                    error: function () {
                        alert('Error adding academic year!');
                    }
                });
            });
        });





    </script>
</body>

</html>