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


$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch departments from the department table
$yearLevels = [];
$classQuery = "SELECT class_id, year_level FROM class ORDER BY year_level ASC";
$classResult = $conn->query($classQuery);
if ($classResult && $classResult->num_rows > 0) {
    while ($row = $classResult->fetch_assoc()) {
        $yearLevels[] = $row;
    }
}

// Fetch departments dynamically
$departments = [];
$departmentQuery = "SELECT dep_id, department FROM department ORDER BY department ASC";
$departmentResult = $conn->query($departmentQuery);
if ($departmentResult && $departmentResult->num_rows > 0) {
    while ($row = $departmentResult->fetch_assoc()) {
        $departments[] = $row;
    }
}



$sectionToEdit = null;
// Fetch section data for editing (make sure to fetch the class_id as well)
if (isset($_GET['section_id'])) {
    $section_id = $_GET['section_id'];

    $stmt = $conn->prepare("
    SELECT * 
    FROM section s
    INNER JOIN class c ON c.section_id = s.section_id
    WHERE s.section_id = ?
");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sectionToEdit = $result->fetch_assoc();
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

$query = "SELECT 
    class.class_id,
    class.section_id,
    section.sections AS section_name,
    section.status AS section_status,
    department.department AS department_name,
    class.year_level
FROM 
    class
INNER JOIN 
    section ON class.section_id = section.section_id
    INNER JOIN department ON section.dep_id = department.dep_id"
;



$result = $conn->query($query);

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
    <link rel="stylesheet" href="../sidebar.css">
    <style>
        .edit-btn,
        .add-subject-btn {
            background-color: #4CAF50;
            color: white;
        }

        .edit-btn,
        .add-subject-btn:hover {
            background-color: #45a049;
        }

        .archive-btn {
            background-color: #f44336;
            color: white;
        }

        .archive-btn:hover {
            background-color: #e53935;
        }

        .restore-btn {
            background-color: #2196F3;
            color: white;
        }

        .restore-btn:hover {
            background-color: #1e88e5;
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
            height: 50vh;
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

        #addSubjectModal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            /* Semi-transparent black */
        }

        /* Modal Content */
        #modalContent {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Close Button */
        #closeBtn {
            color: #aaa;
            float: right;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        #closeBtn:hover,
        #closeBtn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Heading */
        #modalHeader {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form Elements */
        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: inline-block;
        }

        #subjectSelect {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            height: 200px;
        }

        #addSubjectBtn {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #addSubjectBtn:hover {
            background-color: #45a049;
        }

        /* Optional: Add responsiveness */
        @media (max-width: 768px) {
            #modalContent {
                width: 90%;
                margin: 30% auto;
            }
        }

        #addSecBtn {
            background-color: transparent !important;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
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
            /* Green */
            color: white;
        }

        .archive-btn {
            background-color: #f44336;
            /* Red */
            color: white;
        }

        /* Hover effect */
        .btn:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }
    </style>
</head>


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
        <div style="width: 100%; display:flex ; flex-direction: row; justify-content: space-between;">
            <div class="filter-container">
                <form method="GET">
                    <select name="status" onchange="this.form.submit()" class="status-select">
                        <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Active Sections
                        </option>
                        <option value="archived" <?php echo ($filter == 'archived') ? 'selected' : ''; ?>>Archived
                            Sections</option>
                    </select>
                </form>
            </div>

            <!-- Plus icon to add section -->
            <button id="addSecBtn">
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
        <h2><?php echo ucfirst($filter); ?> Sections</h2>
        <!-- Make the table responsive -->

        <div class="table-wrapper">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Section</th>
                        <th>Department</th>
                        <th>Year Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['section_name']; ?></td>
                                <td><?php echo $row['department_name']; ?></td>
                                <td><?php echo $row['year_level']; ?></td>
                                <td><?php echo ucfirst($row['section_status']); ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button class="btn edit-btn"
                                        onclick="openEditModal(<?php echo $row['section_id']; ?>, '<?php echo $row['section_name']; ?>', '<?php echo $row['section_status']; ?>')">Edit</button>
                                    <!-- Archive Button -->
                                    <a href="section.php?archive=true&section_id=<?php echo $row['section_id']; ?>"
                                        class="btn archive-btn">Archive</a>
                                    <!-- Restore Button -->
                                    <a href="section.php?restore=true&section_id=<?php echo $row['section_id']; ?>"
                                        class="btn restore-btn">Restore</a>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No sections found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</div>
<div id="myModal" class="myModal">
    <div class="mymodal-content">
        <span class="close">&times;</span>
        <h2 class="form-title">Add Section</h2>

        <form action="add_section.php" method="POST">
            <div class="form-group">
                <label for="sections" class="form-label">Section Name</label>
                <input type="text" name="sections" id="sections" class="form-input" placeholder="Enter section name"
                    required>
            </div>
            <div class="form-group">
                <label for="department" class="form-label">Department</label>
                <select name="dep_id" id=" department" class="form-input" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= htmlspecialchars($department['dep_id']); ?>">
                            <?= htmlspecialchars($department['department']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="year_level" class="form-label">Year Level</label>
                <select name="year_level" id="year_level" class="form-input" required>
                    <option value="">Select Year Level</option>
                    <option value="1">
                        1
                    </option>
                    <option value="2">
                        2
                    </option>
                    <option value="3">
                        3
                    </option>
                    <option value="4">
                        4
                    </option>
                </select>
            </div>
            <input type="hidden" name="status" value="active"> <!-- Default Status -->
            <button type="submit" name="add" class="submit-btn">Add Section</button>
        </form>
    </div>
</div>
<!-- Edit section Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Section</h2>
        <form action="edit_section.php" method="POST" class="edit-form">
            <input type="hidden" id="editSectionId" name="section_id" class="form-input">
            <div class="form-group">
                <label for="sections">Section :</label>
                <input type="text" id="editSections" name="sections" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="editStatus" name="status" required class="form-control">
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <div class="form-group">
                <label for="department" class="form-label">Department</label>
                <select name="department" id="department" class="form-input" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?php echo $department['dep_id']; ?>" <?php echo isset($sectionToEdit) && $sectionToEdit['dep_id'] == $department['dep_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($department['department']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <div class="form-group">
                    <select name="year_level" id="year_level" class="form-input" required>
                        <option value="1" <?php echo (!empty($sectionToEdit['class_id']) && $sectionToEdit['class_id'] == 1) ? 'selected' : ''; ?>>First Year</option>
                        <option value="2" <?php echo (!empty($sectionToEdit['class_id']) && $sectionToEdit['class_id'] == 2) ? 'selected' : ''; ?>>Second Year</option>
                        <option value="3" <?php echo (!empty($sectionToEdit['class_id']) && $sectionToEdit['class_id'] == 3) ? 'selected' : ''; ?>>Third Year</option>
                        <option value="4" <?php echo (!empty($sectionToEdit['class_id']) && $sectionToEdit['class_id'] == 4) ? 'selected' : ''; ?>>Fourth Year</option>
                    </select>
                </div>



            </div>
            <button type="submit" name="edit" class="submit-btn">Update Section</button>
        </form>
    </div>
</div>


<div id="addSubjectModal">
    <div id="modalContent">
        <span id="closeBtn" onclick="closeAddSubjectModal()">&times;</span>
        <h3 id="modalHeader">Add Subjects to Section</h3>
        <form id="addSubjectForm">
            <label for="subjectSelect">Select Subjects:</label>
            <select id="subjectSelect" name="subjects[]" multiple>
                <!-- Dynamically populated options will go here -->
            </select>
            <input type="hidden" id="section_id" name="section_id">
            <button type="submit" id="addSubjectBtn">Add Subjects</button>
        </form>
    </div>
</div>
<script src="../js/sidebar.js"></script>

<script>

    // JavaScript to handle opening and closing the modal

    // Open the modal with section data
    function openAddSubjectModal(sectionId, sectionName, sectionStatus) {
        // Set the section ID to the hidden input field
        document.getElementById('section_id').value = sectionId;

        // Populate subjects in the select dropdown (via AJAX)
        loadSubjectsForSection(sectionId);

        // Show the modal
        document.getElementById('addSubjectModal').style.display = 'block';
    }

    // Close the modal
    function closeAddSubjectModal() {
        document.getElementById('addSubjectModal').style.display = 'none';
    }

    // Load available subjects via AJAX



    // Handle form submission to add subjects


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