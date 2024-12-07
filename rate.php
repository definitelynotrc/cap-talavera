<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
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

// Handle rate creation or update (for add or edit)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rate_name']) && isset($_POST['rates'])) {
    $rate_name = $_POST['rate_name'];
    $rates = $_POST['rates'];  // Changed variable name to match 'rates' column in the database
    $date_created = date('Y-m-d H:i:s'); // Set the current date and time

    try {
        // Check if editing or adding a rate
        if (isset($_POST['rate_id'])) {
            // Updating an existing rate
            $stmt = $pdo->prepare("UPDATE rate SET rate_name = :rate_name, rates = :rates, date_created = :date_created WHERE rate_id = :rate_id");
            $stmt->bindParam(':rate_id', $_POST['rate_id'], PDO::PARAM_INT);
        } else {
            // Adding a new rate
            $stmt = $pdo->prepare("INSERT INTO rate (rate_name, rates, date_created) VALUES (:rate_name, :rates, :date_created)");
        }

        // Bind parameters to the query
        $stmt->bindParam(':rate_name', $rate_name, PDO::PARAM_STR);
        $stmt->bindParam(':rates', $rates, PDO::PARAM_INT);  // Changed to match 'rates' column
        $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        header("Location: rate.php"); // Redirect to rate page after successful update/add
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all rates (for listing)
try {
    $stmt = $pdo->prepare("SELECT * FROM rate");
    $stmt->execute();
    $rates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch the rate data for editing if needed
$rateToEdit = null;
if (isset($_GET['rate_id'])) {
    $id = $_GET['rate_id'];
    $stmt = $pdo->prepare("SELECT * FROM rate WHERE rate_id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $rateToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Rate</title>
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
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <button id="addRateBtn" class="add-btn">Add Rate</button>

            <!-- Modal Structure for Adding/Editing -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2 class="form-title"><?php echo isset($rateToEdit) ? 'Edit' : 'Add'; ?> Rate</h2>

                    <form action="rate.php" method="POST">
                        <!-- Hidden ID field for editing -->
                        <?php if ($rateToEdit): ?>
                            <input type="hidden" name="rate_id" value="<?php echo $rateToEdit['rate_id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="rate_name" class="form-label">Rate Name</label>
                            <input type="text" name="rate_name" id="rate_name" class="form-input"
                                value="<?php echo $rateToEdit ? htmlspecialchars($rateToEdit['rate_name']) : ''; ?>"
                                placeholder="Enter rate name" required>
                        </div>

                        <div class="form-group">
                            <label for="rate" class="form-label">Rate</label>
                            <input type="number" name="rates" id="rates" class="form-input"
                                value="<?php echo $rateToEdit ? htmlspecialchars($rateToEdit['rate']) : ''; ?>"
                                placeholder="Enter rate" required>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-input" rows="4"
                                placeholder="Enter rate description"><?php echo $rateToEdit ? htmlspecialchars($rateToEdit['description']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="submit-btn"><?php echo isset($rateToEdit) ? 'Update' : 'Add'; ?>
                            Rate</button>
                    </form>
                </div>
            </div>

            <!-- Rate List -->
            <h2>Rate List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Rate Name</th>
                        <th>Rate</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rates)): ?>
                        <?php foreach ($rates as $rate): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rate['rate_name']); ?></td>
                                <td><?php echo htmlspecialchars($rate['rates']); ?></td>
                                <td><?php echo htmlspecialchars($rate['date_created']); ?></td>
                                <td>
                                    <a href="?rate_id=<?php echo $rate['rate_id']; ?>" class="btn edit-btn">Edit</a><br><br>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No rates found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <script src="main.js"></script>
        <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>

        <script>
            var modal = document.getElementById("myModal");
            var btn = document.getElementById("addRateBtn");
            var span = document.getElementsByClassName("close")[0];

            // Open modal for adding new rate
            btn.onclick = function () {
                modal.style.display = "block";
            }

            // Close modal when clicking the close button
            span.onclick = function () {
                modal.style.display = "none";
            }

            // Close modal when clicking outside of it
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
</body>

</html>