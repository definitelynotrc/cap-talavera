<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
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
    <link rel="stylesheet" href="sidebar.css">
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
        #department,
        #classes,
        #subject,
        #users {
            display: flex;
            flex-direction: column;
        }

        .main {
            padding: 20px;
            margin-left: 200px;
            transition: margin-left 0.3s;
            width: 100%;

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
        <?php include('components/sidebar.php'); ?>
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
            </div>
            <?php


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
            ?>

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
            <?php
            // Query to fetch the 10 highest evaluated teachers
            $highestEvaluatedQuery = "
    SELECT 
     e.eval_id,
        e.transaction_code,
        e.remarks,
        e.rate_result,
        e.date_created,
        u.fname AS instructor_fname,
        u.lname AS instructor_lname,
        ct.teacher_type,
        AVG(e.rate_result) AS avg_rating
    FROM evaluation e
    JOIN class_teacher ct ON e.class_teacher_id = ct.class_teacher_id
    JOIN users u ON ct.user_id = u.user_id
    GROUP BY ct.class_teacher_id
    ORDER BY avg_rating DESC
    LIMIT 10
";

            $stmtHigh = $pdo->prepare($highestEvaluatedQuery);
            $stmtHigh->execute();
            $highestEvaluated = $stmtHigh->fetchAll(PDO::FETCH_ASSOC);

            // Query to fetch the 10 lowest evaluated teachers
            $lowestEvaluatedQuery = "
    SELECT 
      e.eval_id,
        e.transaction_code,
        e.remarks,
        e.rate_result,
        e.date_created,
        u.fname AS instructor_fname,
        u.lname AS instructor_lname,
        ct.teacher_type,
        AVG(e.rate_result) AS avg_rating
    FROM evaluation e
    JOIN class_teacher ct ON e.class_teacher_id = ct.class_teacher_id
    JOIN users u ON ct.user_id = u.user_id
    GROUP BY ct.class_teacher_id
    ORDER BY avg_rating ASC
    LIMIT 10
";

            $stmtLow = $pdo->prepare($lowestEvaluatedQuery);
            $stmtLow->execute();
            $lowestEvaluated = $stmtLow->fetchAll(PDO::FETCH_ASSOC);
            ?>



            <?php if (count($highestEvaluated) > 0): ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <caption style="font-size: 1.2rem; font-weight: bold; margin-bottom: 10px;">Highest Evaluated Teachers
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
                        <?php foreach ($highestEvaluated as $row): ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo htmlspecialchars($row['transaction_code']); ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo htmlspecialchars($row['instructor_fname'] . ' ' . $row['instructor_lname']); ?>

                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                                    <?php echo htmlspecialchars($row['teacher_type']); ?>

                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo htmlspecialchars($row['remarks']); ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo number_format($row['avg_rating'], 2); ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">

                                    <?php echo date("F j, Y, g:i a", strtotime($row['date_created'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No high-rated teachers found.</p>
            <?php endif; ?>

            <!-- Table for Top 10 Lowest Evaluated Teachers -->

            <?php if (count($lowestEvaluated) > 0): ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <caption style="font-size: 1.2rem; font-weight: bold; margin-bottom: 10px;">Lowest Evaluated Teachers
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
                        <?php foreach ($lowestEvaluated as $row): ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo htmlspecialchars($row['transaction_code']); ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo htmlspecialchars($row['instructor_fname'] . ' ' . $row['instructor_lname']); ?>

                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                                    <?php echo htmlspecialchars($row['teacher_type']); ?>

                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo htmlspecialchars($row['remarks']); ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?php echo number_format($row['avg_rating'], 2); ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">

                                    <?php echo date("F j, Y, g:i a", strtotime($row['date_created'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No low-rated teachers found.</p>
            <?php endif; ?>

        </div>

    </div>

    <script src="/js/sidebar.js"></script>
    <script>

    </script>



</body>

</html>