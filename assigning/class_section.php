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

    $sectionQuery = "SELECT section_id, sections FROM section";
    $sectionStmt = $conn->prepare($sectionQuery);
    $sectionStmt->execute();
    $sections = $sectionStmt->fetchAll(PDO::FETCH_ASSOC);





} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section and Year Level</title>
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
            <h1>Assign Section to a Year Level </h1>
            <div class="form-container">
                <form method="POST" action="assign_class_sec.php">
                    <div class="form1">

                        <!-- Year Dropdown -->
                        <div>
                            <label for="class_id">Select Year</label>
                            <select name="class" id="class_id" required>
                                <option value="">-- Select Year --</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>

                        <!-- Section Dropdown -->
                        <div>
                            <label for="section_id">Select Section</label>
                            <select name="section" id="section_id" required>
                                <option value="">-- Select Section --</option>
                                <?php foreach ($sections as $section): ?>
                                    <option value="<?= htmlspecialchars($section['section_id']) ?>">
                                        <?= htmlspecialchars($section['sections']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>


                    <!-- Submit Button -->
                    <div class="form1-btn">
                        <button type="submit" name="submit">Assign Section</button>
                    </div>
                </form>



            </div>


            <?php
            $query = "
       SELECT * FROM class
       JOIN section ON class.section_id = section.section_id ORDER BY year_level ASC
       ";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            // Fetch all results
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div>
                <h1>Sections</h1>

                <table style="width: 100%; border-collapse: collapse; border: 1px solid #2A2185;">
                    <thead style="background-color: #2A2185; color: white;">
                        <tr>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Year Level</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #2A2185;">Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections as $row): ?>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #2A2185;">
                                    <?= htmlspecialchars($row['year_level']) ?>
                                </td>
                                <td style="padding: 8px; border: 1px solid #2A2185;">
                                    <?= htmlspecialchars($row['sections']) ?>
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