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
    $semesters = $_POST['semesters'];

    // Insert the class into the database
    $stmt = $conn->prepare("INSERT INTO semester (semesters) VALUES (?)");
    $stmt->bind_param("s", $semesters);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Semester added successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle Edit request
if (isset($_POST['edit'])) {
    $sem_id = $_POST['sem_id'];
    $semesters = $_POST['semesters'];

    // Update the subject in the database
    $stmt = $conn->prepare("UPDATE semester SET semesters=? WHERE sem_id=?");
    $stmt->bind_param("si", $semesters, $sem_id);  // Bind both parameters: semesters and sem_id

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Semester updated successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Initialize $subjectToEdit as null
$semesterToEdit = null;
$result = null; // Initialize $result

// Check if there is a sem_id parameter for editing
if (isset($_GET['sem_id'])) {
    $sem_id = $_GET['sem_id'];

    // Fetch the subject details for editing
    $stmt = $conn->prepare("SELECT * FROM semester WHERE sem_id = ?");
    $stmt->bind_param("i", $sem_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the subject exists, assign it to $subjectToEdit
    if ($result->num_rows > 0) {
        $classToEdit = $result->fetch_assoc();
    } else {
        echo "Semester not found.";
    }

    $stmt->close();
} else {
    // Fetch all subjects when no sub_id is provided
    $stmt = $conn->prepare("SELECT * FROM semester");
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
    <title>Manage Semester</title>
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

        .mymodal-content,
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            height: 40vh;
            max-width: 400px;
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


            <h2> Semester</h2>

            <div class="table-wrapper">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Semester</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($result) && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['semesters']); ?></td>
                                    <td>
                                        <button class="btn btn-success edit-btn" onclick="openEditModal(
                                    <?php echo $row['sem_id']; ?>,
                                    '<?php echo htmlspecialchars($row['semesters'], ENT_QUOTES); ?>'
                                )">Edit</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">No semester found</td>
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



            <form action="semester.php" method="POST">
                <!-- Hidden ID field for editing -->
                <?php if ($semesterToEdit): ?>
                    <input type="hidden" name="sem_id" value="<?php echo $semesterToEdit['sem_id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="semesters" class="form-label">Year Level </label>
                    <input type="text" name="semesters" id="semesters" class="form-input"
                        value="<?php echo $semesterToEdit ? htmlspecialchars($semesterToEdit['semesters']) : ''; ?>"
                        placeholder="Enter semester" required>
                </div>
                <button type="submit" name="add"
                    class="submit-btn"><?php echo isset($semesterToEdit) ? 'Update' : 'Add'; ?>
                    Semester</button>
            </form>
        </div>
    </div>
    <!-- Edit subject Modal -->
    <!-- Edit Subject Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit semester</h2>
            <form action="semester.php" method="POST" class="edit-form">
                <input type="hidden" id="editSemId" name="sem_id" class="form-input">

                <div class="form-group">
                    <label for="semesters">Semester:</label>
                    <input type="text" id="editSemesters" name="semesters" class="form-input" required>
                </div>



                <button type="submit" name="edit" class="submit-btn">Update Subject</button>
            </form>
        </div>
    </div>

    <script src="../js/sidebar.js"></script>

    <script>


        function openEditModal(sem_id, semesters) {
            // Ensure all the inputs are updated with the existing data
            document.getElementById('editSemId').value = sem_id;
            document.getElementById('editSemesters').value = semesters;

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