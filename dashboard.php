<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include('db.php');

// Query to count the number of students
$students_query = "SELECT COUNT(*) AS total_students FROM users WHERE role = 'student'";
$students_stmt = $pdo->prepare($students_query);
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
                        <!-- Clickable Image -->
                        <button class="dropdown-btn">
                            <img src="/img/admin.jpg" alt="User Profile" class="profile-img">
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="dropdown-content">
                            <a href="#">Manage Account</a>
                            <a href="logout.php">Logout</a>
                            <!-- PHP to log out user -->
                        </div>
                    </div>
                </div>
            </div>


            <div class="cardBox" style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: space-between;">
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
                    style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px;">
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
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <caption style="font-size: 1.2rem; font-weight: bold; margin-bottom: 10px;">Recent Evaluations
                    </caption>
                    <thead>
                        <tr style="background-color: #2A2185; color: black;">
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






            <script src="main.js"></script>

            <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
</body>

</html>