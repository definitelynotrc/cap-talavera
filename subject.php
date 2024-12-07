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

            <button type="submit" name="add" class="submit-btn"><?php echo isset($subjectToEdit) ? 'Update' : 'Add'; ?> Subject</button>
        </form>
    </div>
</div>



   <!-- Plus icon to add department -->
   <button id="addSubBtn" class="add-btn">
            <ion-icon name="add-circle-outline"></ion-icon> <!-- Plus icon -->
        </button>


        <h2> Subjects</h2>
        <!-- Make the table responsive -->
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
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['code']; ?></td>
                                <td><?php echo $row['subjects']; ?></td>
                                <td><?php echo $row['lec']; ?></td>
                                <td><?php echo $row['lab']; ?></td>
                                <td><?php echo $row['credit']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td>
                                    <button class="btn btn-success edit-btn" onclick="openEditModal(<?php echo $row['sub_id']; ?>, '<?php echo $row['code']; ?>', '<?php echo $row['subjects']; ?>', '<?php echo $row['lec']; ?>', '<?php echo $row['lab']; ?>', '<?php echo $row['credit']; ?>', '<?php echo $row['description']; ?>')">Edit</button>  
                                    
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No subjects found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit subject Modal -->
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


<script src="main.js"></script>
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
 
<script>
// JavaScript to handle opening and closing the modal
// JavaScript to handle opening and closing the modal
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
