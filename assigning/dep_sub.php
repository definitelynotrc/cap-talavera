<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
$host = 'localhost';
$dbname = 'cap';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch departments
    $departmentsQuery = "SELECT dep_id, department FROM department";
    $departmentsStmt = $conn->prepare($departmentsQuery);
    $departmentsStmt->execute();
    $departments = $departmentsStmt->fetchAll(PDO::FETCH_ASSOC);

    $subjectsQuery = "
    SELECT sub_id, subjects 
    FROM subject 
    WHERE sub_id NOT IN (
        SELECT sub_id 
        FROM dep_sub
    )";


    // Handle form submission to assign a subject to a department and semester
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dep_id = $_POST['dep_id'];
        $sub_id = $_POST['sub_id'];


        // Check if the assignment already exists
        $checkAssignmentQuery = "SELECT * FROM dep_sub WHERE dep_id = :dep_id AND sub_id = :sub_id ";
        $checkAssignmentStmt = $conn->prepare($checkAssignmentQuery);
        $checkAssignmentStmt->execute([
            ':dep_id' => $dep_id,
            ':sub_id' => $sub_id,
            // Include semester_id in the check
        ]);

        if ($checkAssignmentStmt->rowCount() > 0) {
            echo "<script>alert('This subject is already assigned to the selected department .'); window.location.href = '';</script>";
        } else {
            // Insert the assignment
            $insertDepSubQuery = "
                INSERT INTO dep_sub (dep_id, sub_id, sem_id) 
                VALUES (:dep_id, :sub_id)
            ";
            $insertDepSubStmt = $conn->prepare($insertDepSubQuery);
            $insertDepSubStmt->execute([
                ':dep_id' => $dep_id,
                ':sub_id' => $sub_id,

            ]);

            echo "<script>alert('Subject successfully assigned to department and semester!'); window.location.href = '';</script>";
        }
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../sidebar.css">
    <title>Assign Subject to Department</title>
    <style>
        form {
            max-width: 500px;
            margin: auto;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        select,
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #007BFF;
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
                        <a href="../logout.php">Logout</a>
                    </div>
                    <!-- PHP to log out user -->
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <?php include('../components/sidebar.php') ?>
        <div class="main">

            <h1>Assign Subject to Department</h1>

            <div>
                <form method="POST">
                    <label for="dep_id">Select Department</label>
                    <select name="dep_id" id="dep_id" required>
                        <option value="">-- Select Department --</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= htmlspecialchars($department['dep_id']) ?>">
                                <?= htmlspecialchars($department['department']) ?>
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



                    <button type="submit">Assign Subject to Department</button>
                </form>
            </div>
            <div>
                <?php
                $assignedSubjectsQuery = "SELECT * FROM dep_sub
                JOIN department ON dep_sub.dep_id = department.dep_id
                JOIN subject ON dep_sub.sub_id = subject.sub_id
                ";
                $assignedSubjectsStmt = $conn->prepare($assignedSubjectsQuery);
                $assignedSubjectsStmt->execute();
                $assignedSubjects = $assignedSubjectsStmt->fetchAll(PDO::FETCH_ASSOC);


                ?>
                <h2>Assigned Subjects</h2>

                <table style="width: 100%; border-collapse: collapse; border: 1px solid #2A2185;">
                    <thead style="background-color: #2A2185; color: white;">
                        <tr>

                            <th style="padding: 8px; text-align: left;">Subject</th>
                            <th style="padding: 8px; text-align: left;">Department</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignedSubjects as $assignedSubject): ?>
                            <tr>

                                <td style="padding: 8px; border-bottom: 1px solid #2A2185;">
                                    <?= htmlspecialchars($assignedSubject['subjects']) ?>
                                </td>
                                <td style="padding: 8px; border-bottom: 1px solid #2A2185;">
                                    <?= htmlspecialchars($assignedSubject['department']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="../js/sidebar.js"></script>
</body>

</html>