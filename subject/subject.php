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
    $code = $_POST['code'];
    $subjects = $_POST['subjects'];
    $lec = $_POST['lec'];
    $lab = $_POST['lab'];
    $credit = $_POST['credit'];
    $description = $_POST['description'];

    // Insert the subject into the database
    $stmt = $conn->prepare("INSERT INTO subject (code, subjects, lec, lab, credit, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiis", $code, $subjects, $lec, $lab, $credit, $description);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Subject added successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle Edit request
if (isset($_POST['edit'])) {
    $sub_id = $_POST['sub_id'];
    $code = $_POST['code'];
    $subjects = $_POST['subjects'];
    $lec = $_POST['lec'];
    $lab = $_POST['lab'];
    $credit = $_POST['credit'];
    $description = $_POST['description'];

    // Update the subject in the database
    $stmt = $conn->prepare("UPDATE subject SET code=?, subjects=?, lec=?, lab=?, credit=?, description=? WHERE sub_id=?");
    $stmt->bind_param("ssiiisi", $code, $subjects, $lec, $lab, $credit, $description, $sub_id);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Subject updated successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Initialize $subjectToEdit as null
$subjectToEdit = null;
$result = null; // Initialize $result

// Check if there is a dep_id parameter for editing
if (isset($_GET['sub_id'])) {
    $dep_id = $_GET['sub_id'];

    // Fetch the subject details for editing
    $stmt = $conn->prepare("SELECT * FROM subject WHERE sub_id = ?");
    $stmt->bind_param("i", $sub_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the subject exists, assign it to $subjectToEdit
    if ($result->num_rows > 0) {
        $subjectToEdit = $result->fetch_assoc();
    } else {
        echo "Subject not found.";
    }

    $stmt->close();
} else {
    // Fetch all subjects when no sub_id is provided
    $stmt = $conn->prepare("SELECT * FROM subject");
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
    <title>Manage Subject</title>
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

            <h2> Subjects</h2>

            <div class="table-wrapper">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject</th>
                            <th>Lec</th>
                            <th>Lab</th>
                            <th>Credit</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($result) && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subjects']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lec']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lab']); ?></td>
                                    <td><?php echo htmlspecialchars($row['credit']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td>
                                        <button class="btn btn-success edit-btn" onclick="openEditModal(
                                    <?php echo $row['sub_id']; ?>,
                                    '<?php echo htmlspecialchars($row['code'], ENT_QUOTES); ?>',
                                    '<?php echo htmlspecialchars($row['subjects'], ENT_QUOTES); ?>',
                                    '<?php echo htmlspecialchars($row['lec'], ENT_QUOTES); ?>',
                                    '<?php echo htmlspecialchars($row['lab'], ENT_QUOTES); ?>',
                                    '<?php echo htmlspecialchars($row['credit'], ENT_QUOTES); ?>',
                                    '<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>'
                                )">Edit</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No subjects found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div id="myModal" class="myModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="form-title"><?php echo isset($subjectToEdit) ? 'Edit' : 'Add'; ?> Subject</h2>



            <form action="subject.php" method="POST">
                <!-- Hidden ID field for editing -->
                <?php if ($subjectToEdit): ?>
                    <input type="hidden" name="id" value="<?php echo $subjectToEdit['sub_id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="code" class="form-label">Code </label>
                    <input type="text" name="code" id="code" class="form-input"
                        value="<?php echo $subjectToEdit ? htmlspecialchars($subjectToEdit['code']) : ''; ?>"
                        placeholder="Enter subject name" required>
                </div>

                <div class="form-group">
                    <label for="subjects" class="form-label">Subject Name</label>
                    <input type="text" name="subjects" id="subjects" class="form-input"
                        value="<?php echo $subjectToEdit ? htmlspecialchars($subjectToEdit['subjects']) : ''; ?>"
                        placeholder="Enter subject name" required>
                </div>

                <div class="form-group">
                    <label for="lec" class="form-label">Lecture</label>
                    <input type="text" name="lec" id="lec" class="form-input"
                        value="<?php echo $subjectToEdit ? htmlspecialchars($subjectToEdit['lec']) : ''; ?>"
                        placeholder="Enter subject name" required>
                </div>

                <div class="form-group">
                    <label for="lab" class="form-label">Laboratory</label>
                    <input type="text" name="lab" id="lab" class="form-input"
                        value="<?php echo $subjectToEdit ? htmlspecialchars($subjectToEdit['lab']) : ''; ?>"
                        placeholder="Enter subject name" required>
                </div>

                <div class="form-group">
                    <label for="credit" class="form-label">Credit</label>
                    <input type="text" name="credit" id="credit" class="form-input"
                        value="<?php echo $subjectToEdit ? htmlspecialchars($subjectToEdit['credit']) : ''; ?>"
                        placeholder="Enter subject name" required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-input" rows="4"
                        placeholder="Enter subject description"><?php echo $subjectToEdit ? htmlspecialchars($subjectToEdit['description']) : ''; ?></textarea>
                </div>

                <button type="submit" name="add"
                    class="submit-btn"><?php echo isset($subjectToEdit) ? 'Update' : 'Add'; ?>
                    Subject</button>
            </form>
        </div>
    </div>

    <!-- Edit Subject Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Subject</h2>
            <form action="subject.php" method="POST" class="edit-form">
                <input type="hidden" id="editSubId" name="sub_id" class="form-input">

                <div class="form-group">
                    <label for="code">Code:</label>
                    <input type="text" id="editCode" name="code" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="subjects">Subject:</label>
                    <input type="text" id="editSubjects" name="subjects" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="lec">Lecture:</label>
                    <input type="text" id="editLec" name="lec" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="lab">Laboratory:</label>
                    <input type="text" id="editLab" name="lab" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="credit">Credit:</label>
                    <input type="text" id="editCredit" name="credit" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="editDescription" name="description" class="form-input" required></textarea>
                </div>

                <button type="submit" name="edit" class="submit-btn">Update Subject</button>
            </form>
        </div>
    </div>

    <script src="../js/sidebar.js"></script>
    <script>


        function openEditModal(sub_id, code, subjects, lec, lab, credit, description) {
            // Ensure all the inputs are updated with the existing data
            document.getElementById('editSubId').value = sub_id;
            document.getElementById('editCode').value = code;
            document.getElementById('editSubjects').value = subjects;
            document.getElementById('editLec').value = lec;
            document.getElementById('editLab').value = lab;
            document.getElementById('editCredit').value = credit;
            document.getElementById('editDescription').value = description;

            // Display the modal
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Open modal for adding new department
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