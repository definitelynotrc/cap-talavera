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

    $advisoryClassQuery = "
    SELECT 
        advisory_class.advisory_class_id, 
        department.department, 
        class.year_level, 
        section.sections, 
        acad_year.year_start, 
        acad_year.year_end, 
        semester.semesters 
    FROM advisory_class
    INNER JOIN class_dep ON advisory_class.class_dep_id = class_dep.class_dep_id
    INNER JOIN class ON class_dep.class_id = class.class_id
    INNER JOIN section ON class.section_id = section.section_id
    INNER JOIN acad_year ON advisory_class.ay_id = acad_year.ay_id
    INNER JOIN semester ON advisory_class.sem_id = semester.sem_id
    INNER JOIN department ON class_dep.dep_id = department.dep_id
    WHERE advisory_class.isActive = 1";

    $advisoryClassStmt = $conn->prepare($advisoryClassQuery);
    $advisoryClassStmt->execute();
    $advisoryClasses = $advisoryClassStmt->fetchAll(PDO::FETCH_ASSOC);
    ;



    $instructorQuery = "
    SELECT 
        user_dep.*,
        users.*,
        department.*
    FROM user_dep
    INNER JOIN users ON user_dep.user_id = users.user_id  
    INNER JOIN department ON user_dep.dep_id = department.dep_id  
    WHERE users.role = 'Instructor' 
";

    $instructorStmt = $conn->prepare($instructorQuery);
    $instructorStmt->execute();
    $users = $instructorStmt->fetchAll(PDO::FETCH_ASSOC);




    $subjectQuery = "SELECT sub_id, subjects FROM subject";
    $subjectStmt = $conn->prepare($subjectQuery);
    $subjectStmt->execute();
    $subjects = $subjectStmt->fetchAll(PDO::FETCH_ASSOC);

    $teacherTypes = ['Adviser', 'Subject Teacher'];

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Teacher</title>
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
            display: flex;
            flex-direction: column;
            gap: 20px;

        }

        .form1 {
            width: 100%;
            margin: auto;

            padding: 20px;
            border-radius: 5px;
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            grid-template-rows: repeat(1, 1fr);
            grid-column-gap: 0px;
            grid-row-gap: 0px;
            gap: 20px;

        }

        .form1-btn {
            width: 100%;
            margin: auto;
            display: flex;
            justify-content: flex-end;
            align-items: end;
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
            <h1>Assign Teacher to Class </h1>
            <div class="form-container">
                <form method="POST" action="assign_instructor.php">
                    <h1>Assign Teacher</h1>
                    <label for="advisory_class_id">Select Class</label>
                    <select name="advisory_class_id" id="advisory_class_id" required>
                        <option value="">-- Select Advisory Class --</option>
                        <?php foreach ($advisoryClasses as $class): ?>
                            <option value="<?= htmlspecialchars($class['advisory_class_id']) ?>">
                                <?= htmlspecialchars($class['department'] . " - " . $class['year_level'] . "" . $class['sections'] . " - " . $class['year_start'] . " - " . $class['year_end'] . " (" . $class['semesters'] . ")") ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="user_id">Select Instructor</label>
                    <select name="user_id" id="user_id" required>
                        <option value="">-- Select Instructor --</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['user_id']) ?>">
                                <?= htmlspecialchars($user['fname'] . ' ' . $user['lname'] . ' - ' . $user['department']) ?>
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
            <?php
            $classTeacherQuery = "
    SELECT 
        class_teacher.*, 
        advisory_class.advisory_class_id, 
        department.department, 
        class.year_level, 
        section.sections, 
        acad_year.year_start, 
        acad_year.year_end, 
        semester.semesters, 
        subject.subjects,
        users.fname AS instructor_first_name,
        users.lname AS instructor_last_name,
        users.role AS instructor_role
    FROM class_teacher
    INNER JOIN advisory_class ON class_teacher.advisory_class_id = advisory_class.advisory_class_id  -- Join class_teacher with advisory_class
    INNER JOIN class_dep ON advisory_class.class_dep_id = class_dep.class_dep_id  -- Join advisory_class with class_dep to link to department
    INNER JOIN class ON class_dep.class_id = class.class_id  -- Join class to get year_level and section
    INNER JOIN section ON class.section_id = section.section_id  -- Join section to get section name
    INNER JOIN acad_year ON advisory_class.ay_id = acad_year.ay_id  -- Join acad_year to get year start and end
    INNER JOIN semester ON advisory_class.sem_id = semester.sem_id  -- Join semester to get semester info
    INNER JOIN department ON class_dep.dep_id = department.dep_id  -- Join department to get department name
    INNER JOIN users ON class_teacher.user_id = users.user_id  -- Join user to get instructor details
    INNER JOIN subject ON class_teacher.sub_id = subject.sub_id  -- Join subject to get subject name
    WHERE acad_year.isActive = 1  -- Only include rows where acad_year is active
";

            $classTeacherStmt = $conn->prepare($classTeacherQuery);
            $classTeacherStmt->execute();
            $classTeachers = $classTeacherStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div>
                <h1>Class Teachers</h1>

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
                        // Assuming $users is the result of the query
                        foreach ($classTeachers as $classTeacher) {
                            // Assuming you have additional variables for section, department, etc.
                            $instructorName = $classTeacher['instructor_first_name'] . ' ' . $classTeacher['instructor_last_name']; // Example for instructor's name
                            $teacherType = $classTeacher['teacher_type']; // Example for teacher type (you may need to adjust based on your data)
                            $section = $classTeacher['sections']; // Assuming section is part of the user data, adjust if needed
                            $department = $classTeacher['department']; // Assuming department is part of the user data, adjust if needed
                            $yearLevel = $classTeacher['year_level']; // Assuming year level is part of the user data
                            $subject = $classTeacher['subjects']; // Assuming subject is part of the user data, adjust if needed
                            $semester = $classTeacher['semesters']; // Assuming semester is part of the user data
                            $academicYear = $classTeacher['year_start'] . ' - ' . $classTeacher['year_end']; // Assuming academic year is part of the user data
                            ?>
                            <tr>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($instructorName); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($teacherType); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($section); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($department); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($yearLevel); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($subject); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($semester); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($academicYear); ?></td>
                            </tr>
                            <?php
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