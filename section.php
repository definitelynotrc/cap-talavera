<?php
// Database connection details
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
$departments = [];
$deptQuery = "SELECT * FROM department WHERE status = 'active'"; // You can adjust this query to filter based on active status if needed
$deptResult = $conn->query($deptQuery);
if ($deptResult->num_rows > 0) {
    while ($deptRow = $deptResult->fetch_assoc()) {
        $departments[] = $deptRow;
    }
}




$sectionToEdit = null;
// Fetch section data for editing (make sure to fetch the class_id as well)
if (isset($_GET['section_id'])) {
    $section_id = $_GET['section_id'];
    $stmt = $conn->prepare("SELECT * FROM section s
                            INNER JOIN class c ON s.class_id = c.class_id
                            WHERE s.section_id = ?");
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
                s.section_id, 
                s.sections, 
                d.department,
                s.status,
                c.year_level
            FROM 
                section s
            INNER JOIN 
                department d ON s.dep_id = d.dep_id
            INNER JOIN 
                class c ON s.class_id = c.class_id
            WHERE 
                s.status IN ('active', 'archived')
            ORDER BY 
                c.year_level asc ";

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
    <link rel="stylesheet" href="style.css">
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
    </style>
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




            <!-- Dropdown to select Active/Archived sections -->
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
            <button id="addSecBtn" class="add-btn">
                <ion-icon name="add-circle-outline"></ion-icon> <!-- Plus icon -->
            </button>

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
                                    <td><?php echo $row['sections']; ?></td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td><?php echo $row['year_level']; ?></td>
                                    <td><?php echo ucfirst($row['status']); ?></td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button class="btn edit-btn"
                                            onclick="openEditModal(<?php echo $row['section_id']; ?>, '<?php echo $row['sections']; ?>', '<?php echo $row['status']; ?>')">Edit</button>
                                        <!-- Archive Button -->
                                        <a href="section.php?archive=true&section_id=<?php echo $row['section_id']; ?>"
                                            class="btn archive-btn">Archive</a>
                                        <!-- Restore Button -->
                                        <a href="section.php?restore=true&section_id=<?php echo $row['section_id']; ?>"
                                            class="btn restore-btn">Restore</a>
                                        <button class="btn add-subject-btn"
                                            onclick="openAddSubjectModal(<?php echo $row['section_id']; ?>, '<?php echo $row['sections']; ?>', '<?php echo $row['status']; ?>')">Add
                                            Subject</button>

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
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="form-title">Add Section</h2>

            <form action="add_section.php" method="POST">
                <div class="form-group">
                    <label for="sections" class="form-label">Section Name</label>
                    <input type="text" name="sections" id="sections" class="form-input" placeholder="Enter section name"
                        required>
                </div>
                <div class="form-group">
                    <label for="year_level" class="form-label">Year Level</label>
                    <select name="year_level" id="year_level" class="form-input" required>
                        <option value="">Select Year Level</option>
                        <option value="1">First Year</option>
                        <option value="2">Second Year</option>
                        <option value="3">Third Year</option>
                        <option value="4">Fourth Year</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="department" class="form-label">Department</label>
                    <select name="department" id="department" class="form-input" required>
                        <option value="">Select Department</option>
                        <?php
                        // Fetch departments dynamically
                        $departments_query = $conn->query("SELECT dep_id, department FROM department");
                        while ($department = $departments_query->fetch_assoc()):
                            ?>
                            <option value="<?php echo $department['dep_id']; ?>">
                                <?php echo htmlspecialchars($department['department']); ?>
                            </option>
                        <?php endwhile; ?>
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

    <script src="main.js"></script>
    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>

    <script>
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
        function loadSubjectsForSection(sectionId) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_subject_for_sections.php?section_id=' + sectionId + '&_=' + new Date().getTime(), true);
            xhr.onload = function () {
                if (xhr.status == 200) {
                    var subjects = JSON.parse(xhr.responseText);
                    console.log(subjects); // Check if subjects are logged correctly

                    var select = document.getElementById('subjectSelect');
                    select.innerHTML = ''; // Clear existing options

                    // Add an initial placeholder option
                    var placeholderOption = document.createElement('option');
                    placeholderOption.value = '';
                    placeholderOption.textContent = 'Select Subjects';
                    select.appendChild(placeholderOption);

                    // Loop through the subjects and append them as options
                    subjects.forEach(function (subject) {
                        var option = document.createElement('option');
                        option.value = subject.sub_id;

                        // Check if the instructor's name exists
                        if (subject.instructor_fname && subject.instructor_lname) {
                            option.textContent = subject.subjects +
                                ' (Instructor: ' + subject.instructor_fname + ' ' + subject.instructor_lname + ')';
                        } else {
                            option.textContent = subject.subjects + ' (No Instructor Assigned)';
                        }

                        select.appendChild(option);
                    });
                } else {
                    console.error("Failed to load subjects. Status: " + xhr.status);
                }
            };
            xhr.send();
        }



        // Handle form submission to add subjects
        document.getElementById('addSubjectForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var sectionId = document.getElementById('section_id').value;
            var selectedSubjects = Array.from(document.getElementById('subjectSelect').selectedOptions)
                .map(option => option.value);

            // Send selected subjects to the server
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'assign_subject_forsection.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status == 200) {
                    alert('Subjects added successfully!');
                    closeAddSubjectModal(); // Close modal after success
                } else {
                    alert('Error adding subjects.');
                }
            };
            xhr.send('section_id=' + sectionId + '&subjects=' + JSON.stringify(selectedSubjects));
        });

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