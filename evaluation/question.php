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
    <link rel="stylesheet" href="../sidebar.css">
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

        .add-btn {
            background-color: #2A2185;
            color: white;
            padding: 10px 10px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .add-btn:hover {
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
                <a href="question.php<?php echo isset($_GET['archived']) && $_GET['archived'] == 'true' ? '' : '?archived=true'; ?>"
                    class="btn view-archived-btn">
                    <?php echo isset($_GET['archived']) && $_GET['archived'] == 'true' ? 'View Active Questions' : 'View Archived Questions'; ?>
                </a>
                <button id="addQuestionBtn" class="add-btn">Add Question</button>



            </div>
            <!-- Question List -->
            <h2>Question List</h2>
            <table class="table table-bordered">
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
                                    <a href="?ques_id=<?php echo $question['ques_id']; ?>" class="btn edit-btn">Edit</a> <br>
                                    <br>
                                    <a href="archive_question.php?id=<?php echo $question['ques_id']; ?>"
                                        class="btn archive-btn">Archive</a>
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
                <table class="table table-bordered">
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
                                        <a href="restore_question.php?id=<?php echo $question['ques_id']; ?>"
                                            class="btn restore-btn">Restore</a>
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
    <div id="myModal" class="myModal">
        <div class="mymodal-content">
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

                <button type="submit" class="submit-btn"><?php echo isset($questionToEdit) ? 'Update' : 'Add'; ?>
                    Question</button>
            </form>
        </div>
    </div>
    <script src="../js/sidebar.js"></script>
    <script>

        var modal = document.getElementById("myModal");
        var btn = document.getElementById("addQuestionBtn");
        var span = document.getElementsByClassName("close")[0];

        // Open modal for adding new department
        btn.onclick = function () {
            modal.style.display = "block";
        }

        // Close modal when clicking the close button
        span.onclick = function () {
            modal.style.display = "none";
        }

        // Close modal if clicked outside of modal content
        window.onclick = function (event) {
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