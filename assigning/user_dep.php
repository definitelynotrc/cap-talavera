<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Database connection details
$host = 'localhost'; // Change to your host
$dbname = 'cap'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch instructors (users with role "Instructor")
    $instructorsQuery = "SELECT user_id, CONCAT(fname, ' ', lname) AS fullname FROM users WHERE user_id NOT IN (SELECT user_id FROM user_dep WHERE isActive = 1) ";
    $instructorsStmt = $conn->prepare($instructorsQuery);
    $instructorsStmt->execute();
    $instructors = $instructorsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch departments
    $departmentsQuery = "SELECT dep_id, department FROM department";
    $departmentsStmt = $conn->prepare($departmentsQuery);
    $departmentsStmt->execute();
    $departments = $departmentsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_ids = $_POST['user_ids'] ?? []; // Array of selected user IDs
        $dep_id = $_POST['dep_id'];
        $date_assigned = date('Y-m-d');
        $isActive = 1;

        if (!empty($user_ids)) {
            // Prepare the insert query
            $assignQuery = "INSERT INTO user_dep (user_id, dep_id, isActive, date_assigned) VALUES (:user_id, :dep_id, :isActive, :date_assigned)";
            $assignStmt = $conn->prepare($assignQuery);

            // Insert each user into the user_dep table
            foreach ($user_ids as $user_id) {
                $assignStmt->execute([
                    ':user_id' => $user_id,
                    ':dep_id' => $dep_id,
                    ':isActive' => $isActive,
                    ':date_assigned' => $date_assigned,
                ]);
            }

            echo "<script>alert('Departments assigned successfully!'); window.location.href = '';</script>";
        } else {
            echo "<script>alert('Please select at least one user.'); window.location.href = '';</script>";
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
    <title>Assign Department to Users</title>
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
            <h1>Assign Department to Users</h1>
            <div>
                <form method="POST">
                    <div class="form-group">
                        <label for="dep_id">Select Department</label>
                        <select name="dep_id" id="dep_id" required>
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= htmlspecialchars($department['dep_id']) ?>">
                                    <?= htmlspecialchars($department['department']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user_ids">Select Users</label>
                        <div id="user_ids">
                            <?php foreach ($instructors as $instructor): ?>
                                <label>
                                    <input type="checkbox" name="user_ids[]"
                                        value="<?= htmlspecialchars($instructor['user_id']) ?>">
                                    <?= htmlspecialchars($instructor['fullname']) ?>
                                </label>
                                <br>
                            <?php endforeach; ?>
                        </div>
                    </div>



                    <button type="submit">Assign Department</button>
                </form>
            </div>
            <div>
                <h2>User Departments</h2>
                <?php
                // Fetch user departments
                $userDepQuery = "SELECT user_dep_id, CONCAT(fname, ' ', lname) AS fullname, department, users.role FROM user_dep
JOIN users ON user_dep.user_id = users.user_id
JOIN department ON user_dep.dep_id = department.dep_id
WHERE user_dep.isActive = 1";
                $userDepStmt = $conn->prepare($userDepQuery);
                $userDepStmt->execute();
                $userDeps = $userDepStmt->fetchAll(PDO::FETCH_ASSOC);


                ?>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #2A2185;">
                    <thead style="background-color: #2A2185; color: white;">
                        <tr>

                            <th style="padding: 8px; text-align: left;">Name</th>
                            <th style="padding: 8px; text-align: left;">Role</th>

                            <th style="padding: 8px; text-align: left;">Department</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userDeps as $userDep): ?>
                            <tr>

                                <td style="padding: 8px; border-bottom: 1px solid #2A2185;">
                                    <?= htmlspecialchars($userDep['fullname']) ?>
                                </td>
                                <td style="padding: 8px; border-bottom: 1px solid #2A2185;">
                                    <?= htmlspecialchars($userDep['role']) ?>
                                </td>
                                <td style="padding: 8px; border-bottom: 1px solid #2A2185;">
                                    <?= htmlspecialchars($userDep['department']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

            </div>
        </div>
    </div>


    <script src="../js/sidebar.js"></script>
</body>


</html>