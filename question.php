<?php
// Database configuration
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

// Handle question creation or update (for add or edit)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['questions'])) {
    $questions = $_POST['questions'];
    $date_created = date('Y-m-d H:i:s'); // Current timestamp for `date_created`

    try {
        // Check if editing or adding a question
        if (isset($_POST['id'])) {
            // Updating an existing question
            $stmt = $pdo->prepare("UPDATE question SET questions = :questions WHERE ques_id = :id");
            $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        } else {
            // Adding a new question
            $stmt = $pdo->prepare("INSERT INTO question (questions, date_created) VALUES (:questions, :date_created)");
            $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR);  // Bind the created date
        }

        // Bind parameters to the query
        $stmt->bindParam(':questions', $questions, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        header("Location: question.php"); // Redirect to question page after successful update/add
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all questions (for listing)
try {
    $stmt = $pdo->prepare("SELECT * FROM question");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch the question data for editing if needed
$questionToEdit = null;
if (isset($_GET['ques_id'])) {
    $id = $_GET['ques_id'];
    $stmt = $pdo->prepare("SELECT * FROM question WHERE ques_id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $questionToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all active questions (for listing)
try {
    $stmt = $pdo->prepare("SELECT * FROM question WHERE status = 'active'");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch archived questions
$archivedQuestions = [];
if (isset($_GET['archived']) && $_GET['archived'] == 'true') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM question WHERE status = 'archived'");
        $stmt->execute();
        $archivedQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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
            <br>

            <button id="addQuestionBtn" class="add-btn">Add Question</button>

            <!-- Modal Structure for Adding/Editing -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2 class="form-title"><?php echo isset($questionToEdit) ? 'Edit' : 'Add'; ?> Question</h2>

                    <form action="question.php" method="POST">
                        <!-- Hidden ID field for editing -->
                        <?php if ($questionToEdit): ?>
                            <input type="hidden" name="id" value="<?php echo $questionToEdit['ques_id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="questions" class="form-label">Question</label>
                            <input type="text" name="questions" id="questions" class="form-input" 
                                   value="<?php echo $questionToEdit ? htmlspecialchars($questionToEdit['questions']) : ''; ?>" 
                                   placeholder="Enter question" required>
                        </div>

                        <button type="submit" class="submit-btn"><?php echo isset($questionToEdit) ? 'Update' : 'Add'; ?> Question</button>
                    </form>
                </div>
            </div> 

            <!-- Button to View Archived Questions -->
            <a href="question.php<?php echo isset($_GET['archived']) && $_GET['archived'] == 'true' ? '' : '?archived=true'; ?>" class="btn view-archived-btn">
                <?php echo isset($_GET['archived']) && $_GET['archived'] == 'true' ? 'View Active Questions' : 'View Archived Questions'; ?>
            </a>

            <!-- Question List -->
            <h2>Question List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $question): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($question['questions']); ?></td>
                                <td><?php echo htmlspecialchars($question['date_created']); ?></td>
                                <td>
                                    <a href="?ques_id=<?php echo $question['ques_id']; ?>" class="btn edit-btn">Edit</a> <br> <br>
                                    <a href="archive_question.php?id=<?php echo $question['ques_id']; ?>" class="btn archive-btn">Archive</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No active questions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Archived Questions Section -->
            <?php if (isset($_GET['archived']) && $_GET['archived'] == 'true'): ?>
                <h2>Archived Questions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($archivedQuestions)): ?>
                            <?php foreach ($archivedQuestions as $question): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($question['questions']); ?></td>
                                    <td><?php echo htmlspecialchars($question['date_created']); ?></td>
                                    <td>
                                        <a href="restore_question.php?id=<?php echo $question['ques_id']; ?>" class="btn restore-btn">Restore</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No archived questions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="main.js"></script>
    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
    
    <script>
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("addQuestionBtn");
        var span = document.getElementsByClassName("close")[0];

        // Open modal for adding new department
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Close modal when clicking the close button
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close modal if clicked outside of modal content
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Open modal automatically if editing
        <?php if ($questionToEdit): ?>
            modal.style.display = "block";
        <?php endif; ?>
    </script>
</body>
</html>
