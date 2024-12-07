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


// Fetch all students and filter by department
$sectionsQuery = "SELECT * FROM section";
$sectionsResult = $conn->query($sectionsQuery);

// Fetch all students and their department IDs
// Database query to get students and their department ID
$studentsQuery = "
    SELECT u.*, ud.dep_id, s.class_id
    FROM users u
    LEFT JOIN user_dep ud ON u.user_id = ud.user_id
    LEFT JOIN section s ON ud.dep_id = s.dep_id
    WHERE u.role = 'Student' and u.user_id NOT IN (SELECT user_id FROM class_student)
";

// Execute the query and check for errors
$studentsResult = $conn->query($studentsQuery);
if (!$studentsResult) {
    die("Query failed: " . $conn->error);
}

// Store student data in a JavaScript-friendly format
$studentsData = [];
while ($student = $studentsResult->fetch_assoc()) {

    $studentsData[] = [
        'id' => $student['user_id'],
        'name' => $student['fname'] . ' ' . $student['lname'],
        'year_level' => $student['class_id'],  // This can be NULL if no class exists
        'dep_id' => $student['dep_id']  // This can be NULL if no department exists
    ];
}

// Check if any students are fetched

$assignmentsQuery = "
    SELECT u.fname, u.lname, s.sections, s.class_id
    FROM class_student a
    JOIN users u ON a.user_id = u.user_id
    JOIN section s ON a.section_id = s.section_id
    
    
";
$assignmentsResult = $conn->query($assignmentsQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Student to Section</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Modal styles */
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
    </style>
</head>

<body>

    <div class="containerr">
        <div class="navigation">
            <ul>
                <li><a href="index.php"><span class="icon"><ion-icon name="school"></ion-icon></span><span
                            class="title">NEUST</span></a></li>
                <li id="dashboard"><a href="dashboard.php"><span class="icon"><ion-icon
                                name="home"></ion-icon></span><span class="title">Dashboard</span></a></li>
                <li id="subject"><a href="subject.php"><span class="icon"><ion-icon
                                name="desktop"></ion-icon></span><span class="title">Subject</span></a></li>
                <li id="class"><a href="class.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
                            class="title">Class</span></a></li>
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

            <h2>Assign Student to a Section</h2>

            <!-- Button to Open Modal -->
            <button id="openModalBtn">Add Student</button>

            <!-- Modal -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="closeModalBtn">&times;</span>
                    <!-- Form to Assign Student to Section -->
                    <form action="assign_student.php" method="POST">
                        <div class="form-group">
                            <label for="student_id">Select Student</label>
                            <select name="student_id" id="student_id" required>
                                <option value="">-- Select Student --</option>
                                <?php
                                foreach ($studentsData as $student) {
                                    echo '<option value="' . $student['id'] . '" data-dep_id="' . $student['dep_id'] . '">' . htmlspecialchars($student['name']) . '</option>';
                                }
                                ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="section_id">Select Section</label>
                            <select name="section_id" id="section_id" required>
                                <option value="">-- Select Section --</option>
                            </select>
                        </div>

                        <button type="submit" name="assign_student">Assign Student</button>
                    </form>
                </div>
            </div>

            <h3>Current Assignments</h3>
            <!-- Display Assigned Students -->
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Section</th>
                        <th>Year Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Assuming you have a query to get the assigned students with their section and year level
                    while ($assignment = $assignmentsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $assignment['fname'] . ' ' . $assignment['lname']; ?></td>
                            <td><?php echo $assignment['sections']; ?></td>
                            <td><?php echo $assignment['class_id']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="main.js"></script>
    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("openModalBtn");

        // Get the <span> element that closes the modal
        var span = document.getElementById("closeModalBtn");

        // When the user clicks the button, open the modal
        btn.onclick = function () {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function () {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        var studentsData = <?php echo json_encode($studentsData); ?>;

        // When a student is selected, filter the sections by department
        $('#student_id').change(function () {
            var selectedStudentId = $(this).val();
            var selectedStudentDepid = $(this).find(':selected').data('dep_id');  // Correctly get dep_id

            console.log(selectedStudentDepid);  // For debugging to see if the dep_id is being retrieved

            // Only continue if a student is selected
            if (selectedStudentId && selectedStudentDepid) {
                $.ajax({
                    url: 'get_sections.php',  // A file to fetch sections based on dep_id
                    method: 'GET',
                    data: { depid: selectedStudentDepid },  // Pass dep_id to the PHP script
                    success: function (data) {
                        var sections = data; // No need for JSON.parse since it's already parsed
                        var sectionDropdown = $('#section_id');
                        sectionDropdown.empty();  // Clear previous sections
                        sectionDropdown.append('<option value="">-- Select Section --</option>'); // Default option

                        // Populate sections based on the department of the selected student
                        sections.forEach(function (section) {
                            sectionDropdown.append('<option value="' + section.section_id + '">' + section.class_id + section.sections + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error: ' + error); // Log error if any
                    }
                });
            }
        });


    </script>
</body>

</html>