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
                        <button class="dropdown-btn">
                            <img src="/img/admin.jpg" alt="User Profile" class="profile-img">
                        </button>
                        <div class="dropdown-content">
                            <a href="#">Manage Account</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Modal Structure for Adding/Editing -->
            <div id="myModal" class="modal">
                <div class="modal-content">
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

                        <button type="submit"
                            class="submit-btn"><?php echo isset($departmentToEdit) ? 'Update' : 'Add'; ?>
                            Department</button>
                    </form>
                </div>
            </div>



            <!-- Dropdown to select Active/Archived departments -->
            <div class="filter-container">
                <form method="GET">
                    <select name="status" onchange="this.form.submit()" class="status-select">
                        <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Active Departments
                        </option>
                        <option value="archived" <?php echo ($filter == 'archived') ? 'selected' : ''; ?>>Archived
                            Departments</option>
                    </select>
                </form>
            </div>

            <!-- Plus icon to add department -->
            <button id="addDeptBtn" class="add-btn">
                <ion-icon name="add-circle-outline"></ion-icon> <!-- Plus icon -->
            </button>


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

    <script src="main.js"></script>
    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>

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