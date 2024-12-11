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

    $query = "
    SELECT 
        class_dep.class_dep_id,
    class.class_id,
        class.year_level, 
        section.sections AS section_name, 
        department.department
    FROM class_dep
    INNER JOIN class ON class_dep.class_id = class.class_id
    INNER JOIN section ON class.section_id = section.section_id
    INNER JOIN department ON class_dep.dep_id = department.dep_id
    
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Fetch all results
    $sections_departments = $stmt->fetchAll(PDO::FETCH_ASSOC);



    $acad_yearQuery = "SELECT * FROM acad_year WHERE isActive = 1";
    $acad_yearStmt = $conn->prepare($acad_yearQuery);
    $acad_yearStmt->execute();
    $acadyears = $acad_yearStmt->fetchAll(PDO::FETCH_ASSOC);

    $semesterQuery = "SELECT * FROM semester";
    $semesterStmt = $conn->prepare($semesterQuery);
    $semesterStmt->execute();
    $semesters = $semesterStmt->fetchAll(PDO::FETCH_ASSOC);



} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisory Class</title>
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
            <h1>Assign Advisory Class </h1>
            <div class="form-container">
                <form method="POST" action="assign_advisory_class.php">
                    <div class="form1">
                        <div>
                            <label for="class_id">Select Class</label>
                            <select name="class" id="class_id" required>
                                <option value="">-- Select Class --</option>
                                <?php foreach ($sections_departments as $row): ?>
                                    <option value="<?= htmlspecialchars($row['class_dep_id']) ?>">
                                        <?= htmlspecialchars($row['department']) ?>
                                        <?= htmlspecialchars($row['year_level']) ?> -
                                        <?= htmlspecialchars($row['section_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="sem_id">Select Semester</label>
                            <select name="semester" id="sem_id" required>
                                <option value="">-- Select Semester --</option>
                                <?php foreach ($semesters as $semester): ?>
                                    <option value="<?= htmlspecialchars($semester['sem_id']) ?>">
                                        <?= htmlspecialchars($semester['semesters']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="ay_id">Select Academic Year</label>
                            <select name="acadyear" id="ay_id" required>
                                <option value="">-- Select Academic Year --</option>
                                <?php foreach ($acadyears as $acadyear): ?>
                                    <option value="<?= htmlspecialchars($acadyear['ay_id']) ?>">
                                        <?= htmlspecialchars($acadyear['year_start']) ?>-<?= htmlspecialchars($acadyear['year_end']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form1-btn">
                        <button type="submit" name="submit">Assign Class</button>
                    </div>
                </form>



            </div>
            <?php
            $query = "
    SELECT 
        class.class_id,
        class.year_level, 
        section.section_id,
        section.sections AS section_name, 
        department.department, 
        semester.semesters AS semester, 
        acad_year.year_start, 
        acad_year.year_end,
        GROUP_CONCAT(DISTINCT class_dep.class_dep_id) AS class_dep_ids   -- Concatenate distinct class_dep_ids
    FROM advisory_class
    INNER JOIN class_dep ON advisory_class.class_dep_id = class_dep.class_dep_id
    INNER JOIN class ON class_dep.class_id = class.class_id
    INNER JOIN section ON class.section_id = section.section_id
    INNER JOIN department ON class_dep.dep_id = department.dep_id
    INNER JOIN semester ON advisory_class.sem_id = semester.sem_id
    INNER JOIN acad_year ON advisory_class.ay_id = acad_year.ay_id
    WHERE acad_year.isActive = 1  -- Only select active academic years
    GROUP BY class.class_id, class.year_level, section.section_id, section.sections, 
        department.department, semester.semesters, acad_year.year_start, 
        acad_year.year_end
";


            $stmt = $conn->prepare($query);
            $stmt->execute();

            // Fetch all results
            $sections_departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>






            <div>
                <h1>Advisory Class</h1>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #2A2185;">
                    <thead style="background-color: #2A2185; color: white;">
                        <tr>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Year Level</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Section</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Department</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Semester</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Academic Year</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections_departments as $row): ?>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #2A2185;">
                                    <?= htmlspecialchars($row['year_level']) ?>
                                </td>
                                <td style="padding: 8px; border: 1px solid #2A2185;">
                                    <?= htmlspecialchars($row['section_name']) ?>
                                </td>
                                <td style="padding: 8px; border: 1px solid #2A2185;">
                                    <?= htmlspecialchars($row['department']) ?>
                                </td>
                                <td style="padding: 8px; border: 1px solid #2A2185;">
                                    <?= htmlspecialchars($row['semester']) ?>
                                </td>
                                <td style="padding: 8px; border: 1px solid #2A2185;">
                                    <?= htmlspecialchars($row['year_start']) ?>-<?= htmlspecialchars($row['year_end']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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