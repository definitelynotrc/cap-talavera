<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
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
    <link rel="stylesheet" href="../sidebar.css">
    <style>
        .archive-btn {
            background-color: #ff6347;
            color: white;
        }

        .btn {
            text-decoration: none;
            padding: 6px 12px;
            margin: 0 5px;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background-color: #4caf50;

            color: white;
        }

        .myModal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            /* Black background with opacity */
        }

        .mymodal-content,
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;

        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Styling for buttons and form */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }



        #addRateBtn {
            background-color: #2A2185;
            color: white;
            padding: 10px 10px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        #addRateBtn:hover {
            background-color: #1d175d;
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
        <?php include '../components/sidebar.php'; ?>
        <div class="main">
            <div style="display:flex ; flex-direction: row; width: 100%; justify-content: space-between;">
                <h2>Rate List</h2>
                <button id="addRateBtn" class="add-btn">Add Rate</button>
            </div>
            <table class="table table-bordered">
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
                            value="<?php echo (isset($rateToEdit['rate']) ? htmlspecialchars($rateToEdit['rate']) : ''); ?>"
                            placeholder="Enter rate" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-input" rows="4"
                            placeholder="Enter rate description"><?php echo (isset($rateToEdit['description']) ? htmlspecialchars($rateToEdit['description']) : ''); ?></textarea>
                    </div>


                    <button type="submit" class="submit-btn"><?php echo isset($rateToEdit) ? 'Update' : 'Add'; ?>
                        Rate</button>
                </form>
            </div>
        </div>
        <script src="../js/sidebar.js"></script>
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
            const toggle = document.querySelector('.toggle');
            const navigation = document.querySelector('.navigation');

            toggle.addEventListener('click', () => {
                navigation.classList.toggle('active');
            });


        </script>
</body>

</html>