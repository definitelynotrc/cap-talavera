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
$dbname = "cap"; // Replace with your actual database name 'cap'

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add subject request (form submission)
if (isset($_POST['add'])) {
    $year_level = $_POST['year_level'];

    // Insert the class into the database
    $stmt = $conn->prepare("INSERT INTO class (year_level) VALUES (?)");
    $stmt->bind_param("i", $year_level);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Class added successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle Edit request
if (isset($_POST['edit'])) {
    $class_id = $_POST['class_id'];
    $year_level = $_POST['year_level'];

    // Update the subject in the database
    $stmt = $conn->prepare("UPDATE class SET year_level=? WHERE class_id=?");
    $stmt->bind_param("ii", $year_level, $class_id);  // Bind both parameters: year_level and class_id

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Class updated successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Initialize $subjectToEdit as null
$classToEdit = null;
$result = null; // Initialize $result

// Check if there is a dep_id parameter for editing
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];

    // Fetch the subject details for editing
    $stmt = $conn->prepare("SELECT * FROM class WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the subject exists, assign it to $subjectToEdit
    if ($result->num_rows > 0) {
        $classToEdit = $result->fetch_assoc();
    } else {
        echo "Subject not found.";
    }

    $stmt->close();
} else {
    // Fetch all subjects when no sub_id is provided
    $stmt = $conn->prepare("SELECT * FROM class");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class</title>
    <link rel="stylesheet" href="../sidebar.css">
    <style>
        /* Modal styles */
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

        .mymodal-content {
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

        #addSubBtn {
            background-color: transparent !important;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
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
            <div style="display: flex; flex-direction: row; justify-content: space-between;">
                <h1> Class</h1>
                <button id="addSubBtn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 12H16" stroke="#292D32" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 16V8" stroke="#292D32" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z"
                            stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </button>
            </div>

            <div class="table-wrapper">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Year Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($result) && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                    <td>
                                        <button class="btn btn-success edit-btn" onclick="openEditModal(
                                    <?php echo $row['class_id']; ?>,
                                    '<?php echo htmlspecialchars($row['year_level'], ENT_QUOTES); ?>'
                                )">Edit</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">No records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div id="myModal" class="myModal">
        <div class="mymodal-content">
            <span class="close">&times;</span>
            <h2 class="form-title"><?php echo isset($classsToEdit) ? 'Edit' : 'Add'; ?> Class</h2>



            <form action="class.php" method="POST">
                <!-- Hidden ID field for editing -->
                <?php if ($classToEdit): ?>
                    <input type="hidden" name="class_id" value="<?php echo $classToEdit['class_id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="year_level" class="form-label">Year Level </label>
                    <input type="text" name="year_level" id="year_level" class="form-input"
                        value="<?php echo $classToEdit ? htmlspecialchars($classToEdit['year_level']) : ''; ?>"
                        placeholder="Enter year level" required>
                </div>
                <button type="submit" name="add"
                    class="submit-btn"><?php echo isset($subjectToEdit) ? 'Update' : 'Add'; ?>
                    Class</button>
            </form>
        </div>
    </div>
    <!-- Edit subject Modal -->
    <!-- Edit Subject Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Class</h2>
            <form action="class.php" method="POST" class="edit-form">
                <input type="hidden" id="editClassId" name="class_id" class="form-input">

                <div class="form-group">
                    <label for="year_level">Year Level:</label>
                    <input type="text" id="editYear_level" name="year_level" class="form-input" required>
                </div>



                <button type="submit" name="edit" class="submit-btn">Update Subject</button>
            </form>
        </div>
    </div>


    <script src="../js/sidebar.js"></script>
    <script>

        // JavaScript to handle opening and closing the modal

        function openEditModal(class_id, year_level) {
            // Ensure all the inputs are updated with the existing data
            document.getElementById('editClassId').value = class_id;
            document.getElementById('editYear_level').value = year_level;

            // Display the modal
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Open modal for adding new class
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("addSubBtn");
        var span = document.getElementsByClassName("close")[0];

        // Open modal for adding subject
        btn.onclick = function () {
            modal.style.display = "block";
        }

        // Close modal when clicking the close button
        span.onclick = function () {
            modal.style.display = "none";
        }


    </script>

</body>

</html>