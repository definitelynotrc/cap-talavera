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

    $classSectionQuery = "SELECT class.class_id, class.year_level, section.sections FROM class
INNER JOIN section ON class.section_id = section.section_id";
    $classSectionStmt = $conn->prepare($classSectionQuery);
    $classSectionStmt->execute();
    $classes = $classSectionStmt->fetchAll(PDO::FETCH_ASSOC);


    $departmentQuery = "SELECT * FROM department";
    $departmentStmt = $conn->prepare($departmentQuery);
    $departmentStmt->execute();
    $departments = $departmentStmt->fetchAll(PDO::FETCH_ASSOC);



} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Department</title>
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
            <h1>Assign Section to a Department </h1>
            <div class="form-container">
                <form method="POST" action="assign_class_dep.php">
                    <div class="form1">
                        <div>
                            <label for="section_id">Select Section</label>
                            <select name="section" id="section_id" required>
                                <option value="">-- Select Section --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= htmlspecialchars($class['class_id']) ?>">
                                        <?= htmlspecialchars($class['year_level']) ?> -
                                        <?= htmlspecialchars($class['sections']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <div>
                            <label for="dep_id">Select Department</label>
                            <select name="department" id="dep_id" required>
                                <option value="">-- Select Department --</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?= htmlspecialchars($department['dep_id']) ?>">
                                        <?= htmlspecialchars($department['department']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Department Dropdown -->

                    </div>

                    <!-- Submit Button -->
                    <div class="form1-btn">
                        <button type="submit" name="submit">Assign Class</button>
                    </div>
                </form>



            </div>


            <?php
            $query = "
    SELECT 
        class.year_level, 
        section.sections AS section_name, 
        department.department
    FROM class_dep
    INNER JOIN class ON class_dep.class_id = class.class_id
    INNER JOIN section ON class.section_id = section.section_id
    INNER JOIN department ON class_dep.dep_id = department.dep_id";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            // Fetch all results
            $sections_departments = $stmt->fetchAll(PDO::FETCH_ASSOC);



            ?>
            <div>
                <h1>Sections</h1>

                <table style="width: 100%; border-collapse: collapse; border: 1px solid #2A2185;">
                    <thead style="background-color: #2A2185; color: white;">
                        <tr>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Year Level</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Section</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Department</th>
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