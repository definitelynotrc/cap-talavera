<?php
// Database connection details
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

// Handle Add Section request (form submission)
if (isset($_POST['add'])) {
    $sections = $_POST['sections'];
    $status = $_POST['status'];

    // Insert the sections into the database
    $stmt = $conn->prepare("INSERT INTO section (sections, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $sections, $status);
    
    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Section added successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Handle Edit request
if (isset($_POST['edit'])) {
    $section_id = $_POST['section_id'];
    $sections = $_POST['sections'];
    $status = $_POST['status'];

    // Update the section in the database
    $stmt = $conn->prepare("UPDATE section SET sections=?, status=? WHERE section_id=?");
    $stmt->bind_param("ssi", $sections, $status, $section_id);
    
    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Section updated successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Initialize $sectionToEdit as null
$sectionToEdit = null;

// Check if there is a section_id parameter for editing
if (isset($_GET['section_id'])) {
    $section_id = $_GET['section_id'];

    // Fetch the section details for editing
    $stmt = $conn->prepare("SELECT * FROM section WHERE section_id = ?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the section exists, assign it to $sectionToEdit
    if ($result->num_rows > 0) {
        $sectionToEdit = $result->fetch_assoc();
    } else {
        echo "Section not found.";
    }

    $stmt->close();
}

// Handle Archive request
if (isset($_GET['archive'])) {
    $section_id = $_GET['section_id'];

    // Mark the section as archived (assuming an "is_archived" column)
    $stmt = $conn->prepare("UPDATE section SET status='archived' WHERE section_id=?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $stmt->close();
    header("Location: section.php"); // Redirect to avoid resubmitting the form
    exit();
}

// Handle Restore request
if (isset($_GET['restore'])) {
    $section_id = $_GET['section_id'];

    // Restore the archived section by setting status back to 'active'
    $stmt = $conn->prepare("UPDATE section SET status='active' WHERE section_id=?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $stmt->close();
    header("Location: section.php"); // Redirect to avoid resubmitting the form
    exit();
}

// Handle filtering based on active or archived status
$filter = isset($_GET['status']) ? $_GET['status'] : 'active';
if ($filter == 'archived') {
    // Retrieve archived sections
    $sql = "SELECT * FROM section WHERE status = 'archived'";
} else {
    // Retrieve active sections
    $sql = "SELECT * FROM section WHERE status = 'active'";
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
    <title>Manage Sections</title>
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
                    <a href="dashboard.php"><span class="icon"><ion-icon name="home"></ion-icon></span><span class="title">Dashboard</span></a>
                </li>
                <li id="instructor">
                    <a href="instructor.php"><span class="icon"><ion-icon name="person-add"></ion-icon></span><span class="title">Instructor</span></a>
                </li>
                <li id="student">
                    <a href="student.php"><span class="icon"><ion-icon name="person-add"></ion-icon></span><span class="title">Student</span></a>
                </li>
                <li id="department">
                    <a href="department.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Department</span></a>
                </li>
                <li id="subject">
                <a href="subject.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Subject</span></a>
                </li>
                <li id="class">
                    <a href="class.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Class</span></a>
                </li>
                <li id="section">
                    <a href="section.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Section</span></a>
                </li>
                <li id="semester">
                    <a href="semester.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Semester</span></a>
                </li>
                <li id="academic">
                    <a href="acad_year.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Academic Year</span></a>
                </li>
                <li id="question">
                    <a href="question.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Question</span></a>
                </li>
                <li id="rate">
                    <a href="rate.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Rate</span></a>
                </li>
                <li id="evaluation">
                    <a href="evaluation.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span class="title">Evaluation</span></a>
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
        <h2 class="form-title"><?php echo isset($sectionToEdit) ? 'Edit' : 'Add'; ?> Section</h2>

         

        <form action="section.php" method="POST">
            <!-- Hidden ID field for editing -->
            <?php if ($sectionToEdit): ?>
                <input type="hidden" name="id" value="<?php echo $sectionToEdit['section_id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="sections" class="form-label">Section Name</label>
                <input type="text" name="sections" id="sections" class="form-input" 
                       value="<?php echo $sectionToEdit ? htmlspecialchars($sectionToEdit['sections']) : ''; ?>" 
                       placeholder="Enter section name" required>
            </div>

            <button type="submit" class="submit-btn"><?php echo isset($sectionToEdit) ? 'Update' : 'Add'; ?> Section</button>
        </form>
    </div>
</div>


        <!-- Dropdown to select Active/Archived sections -->
        <div class="filter-container">
    <form method="GET">
        <select name="status" onchange="this.form.submit()" class="status-select">
            <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Active Sections</option>
            <option value="archived" <?php echo ($filter == 'archived') ? 'selected' : ''; ?>>Archived Sections</option>
        </select>
    </form>
   </div>

   <!-- Plus icon to add section -->
   <button id="addSecBtn" class="add-btn">
            <ion-icon name="add-circle-outline"></ion-icon> <!-- Plus icon -->
        </button>

        <h2><?php echo ucfirst($filter); ?> Sections</h2>
        <!-- Make the table responsive -->
        <div class="table-wrapper">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Section </th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['sections']; ?></td>
                                <td><?php echo ucfirst($row['status']); ?></td>
                                <td>
                                    <!-- Action buttons -->
                                    <?php if ($row['status'] == 'active'): ?>
                                        <button class="btn btn-success edit-btn" onclick="openEditModal(<?php echo $row['section_id']; ?>, '<?php echo $row['sections']; ?>', '<?php echo $row['status']; ?>')">Edit</button>
                                        <a href="section.php?archive=true&section_id=<?php echo $row['section_id']; ?>">
                                            <button class="btn btn-danger archive-btn">Archive</button>
                                        </a>
                                    <?php elseif ($row['status'] == 'archived'): ?>
                                        <a href="section.php?restore=true&section_id=<?php echo $row['section_id']; ?>">
                                            <button class="btn btn-success edit-btn">Restore</button>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No sections found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit section Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Section</h2>
        <form action="section.php" method="POST" class="edit-form">
            <input type="hidden" id="editSectionId" name="section_id" class="form-input">
            <div class="form-group">
                <label for="sections">Section :</label>
                <input type="text" id="editSections" name="sections" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="editStatus" name="status"  required class="form-control">
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <button type="submit" name="edit" class="submit-btn">Update Section</button>
        </form>
    </div>
</div>

<script src="main.js"></script>
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
 
<script>
// JavaScript to handle opening and closing the modal
function openEditModal(section_id, sections, status) {
    document.getElementById('editSectionId').value = section_id;
    document.getElementById('editSections').value = sections;
    document.getElementById('editStatus').value = status;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

var modal = document.getElementById("myModal");
        var btn = document.getElementById("addSecBtn");
        var span = document.getElementsByClassName("close")[0];

        // Open modal for adding new section
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Close modal when clicking the close button
        span.onclick = function() {
            modal.style.display = "none";
        }

        
</script>

</body>
</html>



