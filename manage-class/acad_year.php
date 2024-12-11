<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap"; // Replace with your actual database name

// Establishing a PDO connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Validate year_start format
function isValidYearStart($yearStart)
{
    return preg_match('/^\d{4}-\d{4}$/', $yearStart);
}



// Handle archiving an academic year (set isActive to 0)
if (isset($_GET['archive']) && isset($_GET['ay_id'])) {
    $ay_id = $_GET['ay_id'];
    try {
        $stmt = $pdo->prepare("UPDATE acad_year SET isActive = 0 WHERE ay_id = :ay_id");
        $stmt->bindParam(':ay_id', $ay_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: acad_year.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle restoring an archived academic year (set isActive to 1)
if (isset($_GET['restore']) && isset($_GET['ay_id'])) {
    $ay_id = $_GET['ay_id'];
    try {
        $stmt = $pdo->prepare("UPDATE acad_year SET isActive = 1 WHERE ay_id = :ay_id");
        $stmt->bindParam(':ay_id', $ay_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: acad_year.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all academic years
try {
    $stmt = $pdo->prepare("SELECT * FROM acad_year ORDER BY year_start DESC");
    $stmt->execute();
    $academicYears = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


if (isset($_GET['ay_id'])) {
    $ay_id = $_GET['ay_id']; // Get the ay_id from the URL
    try {
        $stmt = $pdo->prepare("SELECT * FROM acad_year WHERE ay_id = :ay_id");
        $stmt->bindParam(':ay_id', $ay_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo "<script>alert('Academic Year not found');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['year_start']) && isset($_POST['year_end'])) {
        $yearStart = $_POST['year_start'];
        $yearEnd = $_POST['year_end'];
        $isActive = isset($_POST['isActive']) ? 1 : 0; // Check if 'isActive' is checked

        // Get the current year
        $currentYear = date('Y');

        // Validate year range for both year_start and year_end
        if ($yearStart < ($currentYear - 1) || $yearStart > ($currentYear + 1)) {
            echo 'Year start is out of range. It should be between ' . ($currentYear - 1) . ' and ' . ($currentYear + 1);
            exit;
        }

        if ($yearEnd < ($currentYear - 1) || $yearEnd > ($currentYear + 1)) {
            echo 'Year end is out of range. It should be between ' . ($currentYear - 1) . ' and ' . ($currentYear + 1);
            exit;
        }

        // Check if ay_id is set for an update
        if (isset($_POST['ay_id'])) {
            $ayId = $_POST['ay_id'];

            $sql = "UPDATE acad_year SET year_start = :year_start, year_end = :year_end, isActive = :isActive WHERE ay_id = :ay_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':ay_id', $ayId, PDO::PARAM_INT);
        } else {
            // If ay_id is not set, insert as a new record
            $sql = "INSERT INTO acad_year (year_start, year_end, isActive) VALUES (:year_start, :year_end, :isActive)";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindParam(':year_start', $yearStart, PDO::PARAM_INT);
        $stmt->bindParam(':year_end', $yearEnd, PDO::PARAM_INT);
        $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: acad_year.php");
        } else {
            echo 'Failed';
        }
    }
}
$currentYear = date('Y');

$sql = "UPDATE acad_year SET isActive = 0 WHERE year_end < :currentYear";
$stmt = $pdo->prepare($sql);

$stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);

if ($stmt->execute()) {
    $updateAdvisoryClassSql = "
        UPDATE advisory_class 
        SET isActive = 0 
        WHERE ay_id IN (SELECT ay_id FROM acad_year WHERE isActive = 0)
    ";
    $updateStmt = $pdo->prepare($updateAdvisoryClassSql);
    $updateStmt->execute();
}




?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../sidebar.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
        .archive-btn {
            background-color: #ff6347;
            color: white;
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

            color: white;
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


        .newmodal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            display: flex;
            flex-direction: column;

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

        #addAcadYearBtn {
            background-color: transparent !important;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .view-archived-btn {
            background-color: #2A2185;
            color: white;
            padding: 10px 10px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .view-archived-btn:hover {
            background-color: #1d175d;
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
            <div style="display:flex ; flex-direction: row; width: 100%; justify-content: space-between;">
                <a href="acad_year.php<?php echo isset($_GET['archived']) && $_GET['archived'] == 'true' ? '' : '?archived=true'; ?>"
                    class="btn view-archived-btn">
                    <?php echo isset($_GET['archived']) && $_GET['archived'] == 'true' ? 'View Actives' : 'View Archived '; ?>
                </a>
                <button id="addAcademicYearBtn" style=" background-color: none;">
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


            <h2>Academic Year List</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Year Start</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $acadYear): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($acadYear['year_start'] . ' - ' . $acadYear['year_end']); ?>
                                </td>
                                <td><?php echo $acadYear['isActive'] == 1 ? 'Active' : 'Archived'; ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn edit-btn" data-ay-id="<?php echo $acadYear['ay_id']; ?>"
                                        data-year-start="<?php echo $acadYear['year_start']; ?>"
                                        data-year-end="<?php echo $acadYear['year_end']; ?>"
                                        data-is-active="<?php echo $acadYear['isActive']; ?>">Edit</button>



                                    <a href="?archive=true&ay_id=<?php echo $acadYear['ay_id']; ?>"
                                        class="btn archive-btn">Archive</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No active academic years found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>


            <?php if (isset($_GET['archived']) && $_GET['archived'] == 'true'): ?>
                <h2>Archived Academic Years</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Year Start</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($archivedAcademicYears)): ?>
                            <?php foreach ($archivedAcademicYears as $acadYear): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($acadYear['year_start'] . ' - ' . $acadYear['year_end']); ?>
                                    </td>
                                    <td><?php echo $acadYear['isActive'] == 1 ? 'Active' : 'Archived'; ?></td>
                                    <td>
                                        <a href="?restore=true&ay_id=<?php echo $acadYear['ay_id']; ?>"
                                            class="btn restore-btn">Restore</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No archived academic years found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <div id="addAcademicYearModal" class="modal">
        <div class="newmodal-content" style="display: flex; flex-direction: column; gap: 20px;">
            <span class="close">&times;</span>
            <form id="addAcademicYearForm" method="POST" action="acad_year.php">
                <div class="form-group">
                    <label for="year_start">Academic Year Start:</label>
                    <input type="number" name="year_start" id="year_start" required min="<?php echo date('Y') - 1; ?>"
                        max="<?php echo date('Y') + 1; ?>">
                </div>
                <div class="form-group">
                    <label for="year_end">Academic Year End:</label>
                    <input type="number" name="year_end" id="year_end" required min="<?php echo date('Y') - 1; ?>"
                        max="<?php echo date('Y') + 1; ?>">
                </div>
                <div class="form-group">
                    <label for="isActive">Is Active:</label>
                    <input type="checkbox" name="isActive" id="isActive">
                </div>
                <button type="submit">Add</button>
            </form>
        </div>
    </div>


    <div id="editAcademicYearModal" class="modal" style="display: none;">
        <div class="newmodal-content">
            <span class="close">&times;</span>
            <form id="editAcademicYearForm" method="POST" action="acad_year.php">
                <div class="form-group">
                    <label for="year_start">Academic Year Start:</label>
                    <input type="number" name="year_start" id="edit_year_start" required
                        min="<?php echo date('Y') - 1; ?>" max="<?php echo date('Y') + 1; ?>">
                </div>
                <div class="form-group">
                    <label for="year_end">Academic Year End:</label>
                    <input type="number" name="year_end" id="edit_year_end" required min="<?php echo date('Y') - 1; ?>"
                        max="<?php echo date('Y') + 1; ?>">
                </div>
                <input type="hidden" name="ay_id" id="edit_ay_id">
                <div class="form-group">
                    <label for="isActive">Is Active:</label>
                    <input type="checkbox" name="isActive" id="edit_isActive">
                </div>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>



    <script src="../js/sidebar.js"></script>

    <script>

        $(document).ready(function () {
            // When the Edit button is clicked
            $('.edit-btn').click(function () {
                // Get the data attributes from the button
                var ayId = $(this).data('ay-id');
                var yearStart = $(this).data('year-start');
                var yearEnd = $(this).data('year-end');
                var isActive = $(this).data('is-active') == 1;

                // Fill the modal form with the current values
                $('#edit_ay_id').val(ayId);  // Fill the hidden input with ay_id
                $('#edit_year_start').val(yearStart);  // Fill the year_start input
                $('#edit_year_end').val(yearEnd);  // Fill the year_end input
                $('#edit_isActive').prop('checked', isActive);  // Set checkbox based on isActive

                // Show the modal
                $('#editAcademicYearModal').show();
            });

            // When the close button (X) is clicked, hide the modal
            $('.close').click(function () {
                $('#editAcademicYearModal').hide();
            });

            // Handle the form submission for editing the academic year
            $('#editAcademicYearForm').submit(function (e) {
                e.preventDefault(); // Prevent the form from submitting normally

                // Serialize the form data
                var formData = $(this).serialize();

                // Use AJAX to submit the form without reloading the page
                $.ajax({
                    url: 'acad_year.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        alert('Academic year updated successfully!');
                        $('#editAcademicYearModal').hide();  // Hide the modal after success
                        location.reload();
                    },
                    error: function () {
                        alert('Error updating academic year!');
                    }
                });
            });

        });


        $(document).ready(function () {
            // Show the modal when 'Add Academic Year' button is clicked
            $('#addAcademicYearBtn').click(function () {
                $('#addAcademicYearModal').show(); // Show the modal
            });

            // Close the modal when the 'X' button is clicked
            $('.close').click(function () {
                $('#addAcademicYearModal').hide(); // Hide the modal
            });

            // Handle form submission with AJAX for adding academic year
            $('#addAcademicYearForm').submit(function (e) {
                e.preventDefault(); // Prevent the form from submitting normally

                var formData = $(this).serialize(); // Serialize form data

                // Use AJAX to submit the form without reloading the page
                $.ajax({
                    url: 'acad_year.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        // Handle success (you can show a success message or update the table dynamically)
                        alert('Academic year added successfully!');
                        $('#addAcademicYearModal').hide();
                        location.reload();
                        // Optionally, update the academic year list or table here
                    },
                    error: function () {
                        alert('Error adding academic year!');
                    }
                });
            });
        });



    </script>
</body>

</html>