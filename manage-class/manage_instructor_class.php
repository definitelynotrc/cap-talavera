<?php
session_start();
$userid = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$host = 'localhost'; // Change to your host
$dbname = 'cap'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch classes
    $classesQuery = "
    SELECT 
        class.class_id, 
        class.year_level, 
        section.sections, 
        section.status, 
        department.dep_id,
        department.department


    FROM 
        class
    LEFT JOIN 
        section 
       
    ON 
        class.section_id = section.section_id
         LEFT JOIN 
        department ON section.dep_id = department.dep_id
        "; // Adjust column names as needed

    $classesStmt = $conn->prepare($classesQuery);
    $classesStmt->execute();
    $classes = $classesStmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch academic years
    $academicYearsQuery = "SELECT ay_id, year_start FROM acad_year"; // Adjust table/column names as per your database
    $academicYearsStmt = $conn->prepare($academicYearsQuery);
    $academicYearsStmt->execute();
    $academicYears = $academicYearsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch semesters
    $semestersQuery = "SELECT sem_id, semesters FROM semester"; // Adjust table/column names as per your database
    $semestersStmt = $conn->prepare($semestersQuery);
    $semestersStmt->execute();
    $semesters = $semestersStmt->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

try {


    // Fetch advisory classes
    $advisoryClassesQuery = "
        SELECT ac.advisory_class_id, c.year_level, ay.year_start, s.semesters, sec.sections, d.department
        FROM advisory_class ac
        INNER JOIN class c ON ac.class_id = c.class_id
        INNER JOIN section sec ON c.section_id = sec.section_id
        INNER JOIN department d ON sec.dep_id = d.dep_id
        INNER JOIN acad_year ay ON ac.ay_id = ay.ay_id
        INNER JOIN semester s ON ac.sem_id = s.sem_id
        WHERE ac.isActive = 1
    ";
    $advisoryClassesStmt = $conn->prepare($advisoryClassesQuery);
    $advisoryClassesStmt->execute();
    $advisoryClasses = $advisoryClassesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch users
    $usersQuery = "
    SELECT 
        u.user_id, 
        CONCAT(u.fname, ' ', u.lname) AS fullname, 
        d.department AS department_name
    FROM 
        users u
        JOIN user_dep ud ON u.user_id = ud.user_id
    JOIN 
        department d ON ud.dep_id = d.dep_id
    WHERE 
        u.role = 'Instructor'
";
    $usersStmt = $conn->prepare($usersQuery);
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch subjects
    $subjectsQuery = "SELECT sub_id, subjects FROM subject"; // Replace with your subjects table and columns
    $subjectsStmt = $conn->prepare($subjectsQuery);
    $subjectsStmt->execute();
    $subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Teacher types (e.g., primary, secondary)
    $teacherTypes = ['Advisory Teacher', 'Subject Teacher']; // Adjust as needed



} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="../sidebar.css">
    <style>
        .main {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .form-container {
            display: flex;
            justify-content: space-between;
            flex-direction: column;
            gap: 20px;
        }

        form {
            width: 100%;
            margin: auto;
            border: 1px solid #2A2185;
            padding: 20px;
            border-radius: 5px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        select,
        button {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
        }

        button {
            background-color: #2A2185;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
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

        <?php include '../components/sidebar.php'; ?>
        <div class="main">
            <h1>Instructor Classes</h1>
            <div class="form-container">
                <form method="POST" action="set_advisory.php">
                    <h1>Assign Class</h1>
                    <div>
                        <label for="class_id">Select Class</label>
                        <select name="class_id" id="class_id" required>
                            <option value="">-- Select Class --</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= htmlspecialchars($class['class_id']) ?>">
                                    <?= htmlspecialchars($class['department'] . ' - ' . $class['year_level'] . '' . $class['sections']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                    <div>
                        <label for="ay_id">Select Academic Year</label>
                        <select name="ay_id" id="ay_id" required>
                            <option value="">-- Select Academic Year --</option>
                            <?php foreach ($academicYears as $ay): ?>
                                <option value="<?= htmlspecialchars($ay['ay_id']) ?>">
                                    <?= htmlspecialchars($ay['year_start']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="sem_id">Select Semester</label>
                        <select name="sem_id" id="sem_id" required>
                            <option value="">-- Select Semester --</option>
                            <?php foreach ($semesters as $semester): ?>
                                <option value="<?= htmlspecialchars($semester['sem_id']) ?>">
                                    <?= htmlspecialchars($semester['semesters']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">Assign Class</button>
                </form>
                <form method="POST" action="assign_instructor.php">
                    <h1>Assign Teacher</h1>
                    <label for="advisory_class_id">Select Class</label>
                    <select name="advisory_class_id" id="advisory_class_id" required>
                        <option value="">-- Select Advisory Class --</option>
                        <?php foreach ($advisoryClasses as $class): ?>
                            <option value="<?= htmlspecialchars($class['advisory_class_id']) ?>">
                                <?= htmlspecialchars($class['department'] . " - " . $class['year_level'] . "" . $class['sections'] . " - " . $class['year_start'] . " (" . $class['semesters'] . ")") ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="user_id">Select Instructor</label>
                    <select name="user_id" id="user_id" required>
                        <option value="">-- Select Instructor --</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['user_id']) ?>">
                                <?= htmlspecialchars($user['fullname'] . ' - ' . $user['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>


                    <label for="sub_id">Select Subject</label>
                    <select name="sub_id" id="sub_id" required>
                        <option value="">-- Select Subject --</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= htmlspecialchars($subject['sub_id']) ?>">
                                <?= htmlspecialchars($subject['subjects']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="teacher_type">Select Teacher Type</label>
                    <select name="teacher_type" id="teacher_type" required>
                        <option value="">-- Select Teacher Type --</option>
                        <?php foreach ($teacherTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Assign Teacher</button>
                </form>
            </div>

            <div>
                <?php
                $classesQuery = "SELECT * FROM class_teacher
                JOIN advisory_class ON class_teacher.advisory_class_id = advisory_class.advisory_class_id
                JOIN class ON advisory_class.class_id = class.class_id
                JOIN section ON class.section_id = section.section_id
                JOIN department ON section.dep_id = department.dep_id
                JOIN acad_year ON advisory_class.ay_id = acad_year.ay_id
                JOIN semester ON advisory_class.sem_id = semester.sem_id
                JOIN users ON class_teacher.user_id = users.user_id
                JOIN subject ON class_teacher.sub_id = subject.sub_id";

                // Prepare and execute the query
                $stmt = $conn->prepare($classesQuery);
                $stmt->execute();
                ?>
                <h1>Classes</h1>

                <table style="width: 100%; border-collapse: collapse; border: 1px solid #2A2185;">
                    <thead style="background-color: #2A2185; color: white;">
                        <tr>
                            <th style="padding: 8px; text-align: left;">Instructor</th>
                            <th style="padding: 8px; text-align: left;">Teacher Type</th>
                            <th style="padding: 8px; text-align: left;">Section</th>
                            <th style="padding: 8px; text-align: left;">Department</th>
                            <th style="padding: 8px; text-align: left;">Year Level</th>
                            <th style="padding: 8px; text-align: left;">Subject</th>
                            <th style="padding: 8px; text-align: left;">Semester</th>
                            <th style="padding: 8px; text-align: left;">Academic Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are results and loop through them
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr style='border-bottom: 1px solid #ddd;'>";

                                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['fname'] . " " . $row['lname']) . "</td>";
                                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['teacher_type']) . "</td>";
                                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['sections']) . "</td>";
                                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['department']) . "</td>";
                                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['year_level']) . "</td>";
                                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['subjects']) . "</td>";
                                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['semesters']) . "</td>";
                                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['year_start']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='padding: 8px; text-align: center;'>No classes found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>






    <script src="../js/sidebar.js"></script>



    <script>






    </script>

</body>

</html>