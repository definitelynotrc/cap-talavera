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
$dbname = "cap"; // Replace with your actual database name 'cap'

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Department request (form submission)
if (isset($_POST['add'])) {
    $department = $_POST['department'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Insert the department into the database
    $stmt = $conn->prepare("INSERT INTO department (department, description, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $department, $description, $status);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Department added successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle Edit request
if (isset($_POST['edit'])) {
    $dep_id = $_POST['dep_id'];
    $department = $_POST['department'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Update the department in the database
    $stmt = $conn->prepare("UPDATE department SET department=?, description=?, status=? WHERE dep_id=?");
    $stmt->bind_param("sssi", $department, $description, $status, $dep_id);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Department updated successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Initialize $departmentToEdit as null
$departmentToEdit = null;

// Check if there is a dep_id parameter for editing
if (isset($_GET['dep_id'])) {
    $dep_id = $_GET['dep_id'];

    // Fetch the department details for editing
    $stmt = $conn->prepare("SELECT * FROM department WHERE dep_id = ?");
    $stmt->bind_param("i", $dep_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the department exists, assign it to $departmentToEdit
    if ($result->num_rows > 0) {
        $departmentToEdit = $result->fetch_assoc();
    } else {
        echo "Department not found.";
    }

    $stmt->close();
}


// Handle Archive request
if (isset($_GET['archive'])) {
    $dep_id = $_GET['dep_id'];

    // Mark the department as archived (assuming an "is_archived" column)
    $stmt = $conn->prepare("UPDATE department SET status='archived' WHERE dep_id=?");
    $stmt->bind_param("i", $dep_id);
    $stmt->execute();
    $stmt->close();
    header("Location: department.php"); // Redirect to avoid resubmitting the form
    exit();
}

// Handle Restore request
if (isset($_GET['restore'])) {
    $dep_id = $_GET['dep_id'];

    // Restore the archived department by setting status back to 'active'
    $stmt = $conn->prepare("UPDATE department SET status='active' WHERE dep_id=?");
    $stmt->bind_param("i", $dep_id);
    $stmt->execute();
    $stmt->close();
    header("Location: department.php"); // Redirect to avoid resubmitting the form
    exit();
}

// Handle filtering based on active or archived status
$filter = isset($_GET['status']) ? $_GET['status'] : 'active';
if ($filter == 'archived') {
    // Retrieve archived departments
    $sql = "SELECT * FROM department WHERE status = 'archived'";
} else {
    // Retrieve active departments
    $sql = "SELECT * FROM department WHERE status = 'active'";
}
$result = $conn->query($sql);

// Check if there are any results
if ($result === FALSE) {
    echo "Error: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <link rel="stylesheet" href="../sidebar.css">
    <style>
        /* Modal styles */
        .myModal,
        .modal {
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
            max-height: 80vh;
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            max-height: 50vh;
        }

        .myModal h2 {
            margin-bottom: 20px;
        }

        .myModal input[type="text"],
        .myModal select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
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

        .form-group textarea {
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

        #addDeptBtn {
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
            <h1>Manage Departments</h1>
            <div style="display:flex ; flex-direction: row; justify-content: space-between; width: 100%;">
                <div class="filter-container" style="margin-bottom: 10px;">
                    <form method="GET">
                        <select name="status" onchange="this.form.submit()" class="status-select">
                            <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Active
                                Departments
                            </option>
                            <option value="archived" <?php echo ($filter == 'archived') ? 'selected' : ''; ?>>Archived
                                Departments</option>
                        </select>
                    </form>
                </div>

                <!-- Plus icon to add department -->
                <button id="addDeptBtn">
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

            <h2><?php echo ucfirst($filter); ?> Departments</h2>
            <!-- Make the table responsive -->
            <div class="table-wrapper">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['department']; ?></td>
                                    <td><?php echo $row['description']; ?></td>
                                    <td><?php echo ucfirst($row['status']); ?></td>
                                    <td>
                                        <!-- Action buttons -->
                                        <?php if ($row['status'] == 'active'): ?>
                                            <button class="btn btn-success edit-btn"
                                                onclick="openEditModal(<?php echo $row['dep_id']; ?>, '<?php echo $row['department']; ?>', '<?php echo $row['description']; ?>', '<?php echo $row['status']; ?>')">Edit</button>
                                            <a href="department.php?archive=true&dep_id=<?php echo $row['dep_id']; ?>">
                                                <button class="btn btn-danger archive-btn">Archive</button>
                                            </a>
                                        <?php elseif ($row['status'] == 'archived'): ?>
                                            <a href="department.php?restore=true&dep_id=<?php echo $row['dep_id']; ?>">
                                                <button class="btn btn-success edit-btn">Restore</button>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No departments found</td>
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
            <h2 class="form-title"><?php echo isset($departmentToEdit) ? 'Edit' : 'Add'; ?> Department</h2>



            <form action="department.php" method="POST">
                <!-- Hidden ID field for editing -->
                <?php if ($departmentToEdit): ?>
                    <input type="hidden" name="id" value="<?php echo $departmentToEdit['dep_id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="department" class="form-label">Department Name</label>
                    <input type="text" name="department" id="department" class="form-input"
                        value="<?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['department']) : ''; ?>"
                        placeholder="Enter department name" required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-input" rows="4"
                        placeholder="Enter department description"><?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['description']) : ''; ?></textarea>
                </div>

                <button type="submit" class="submit-btn"><?php echo isset($departmentToEdit) ? 'Update' : 'Add'; ?>
                    Department</button>
            </form>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Department</h2>
            <form action="department.php" method="POST" class="edit-form">
                <input type="hidden" id="editDepId" name="dep_id" class="form-input">
                <div class="form-group">
                    <label for="department">Department Name:</label>
                    <input type="text" id="editDepartment" name="department" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="editDescription" name="description" class="form-input" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="editStatus" name="status" required class="form-control">
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <button type="submit" name="edit" class="submit-btn">Update Department</button>
            </form>
        </div>
    </div>

    <script src="../js/sidebar.js"></script>

    <script>

        // JavaScript to handle opening and closing the modal
        function openEditModal(dep_id, department, description, status) {
            document.getElementById('editDepId').value = dep_id;
            document.getElementById('editDepartment').value = department;
            document.getElementById('editDescription').value = description;
            document.getElementById('editStatus').value = status;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        var modal = document.getElementById("myModal");
        var btn = document.getElementById("addDeptBtn");
        var span = document.getElementsByClassName("close")[0];

        // Open modal for adding new department
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