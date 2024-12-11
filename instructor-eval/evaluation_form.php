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
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$classTeacherId = isset($_GET['class_teacher_id']) ? $_GET['class_teacher_id'] : null;
$instructorName = isset($_GET['instructor_name']) ? htmlspecialchars($_GET['instructor_name']) : 'Unknown';

$query = "SELECT rate_name, rates FROM rate ORDER BY rates DESC";
$result = $conn->query($query);

$queryQuestions = "SELECT ques_id, questions FROM question WHERE status = 'active' ORDER BY ques_id ASC";
$resultQuestions = $conn->query($queryQuestions);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Instructor Evaluation</title>
    <link rel="stylesheet" href="../sidebar.css">
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .th,
        .td {
            border: 1px solid #ddd;

            padding: 8px;
        }

        .th {
            background-color: #f4f4f4;
        }

        label {
            margin-right: 10px;
        }

        button {
            background-color: #2a2185;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .evaluationContainer {
            margin: 20px;


        }

        .evalFormContainer {
            border: solid 1px black;
            width: 100%;
            margin-right: 20px;
            padding: 20px;
            border-radius: 8px;
        }

        .evalFormContainer ul {
            list-style-type: none;
            margin-bottom: 10px;
        }

        .evalFormContainer h2 {
            margin-bottom: 20px;
        }

        .evalFormContainer p {
            margin-bottom: 10px;
        }

        .evalFormContainer h4 {
            margin-bottom: 10px;
        }

        .instructorContainer {
            width: 20%;
            background-color: #f4f4f4;
        }

        .instructor {
            display: flex;
            cursor: pointer;
        }

        .instructor.active {
            background-color: #2a2185;
            color: white;
        }

        /* Disabled instructor */
        .instructor.disabled {
            background-color: #ddd;
            /* Disabled style */
            color: #999;
            pointer-events: none;
            /* Disables further interaction */
            cursor: not-allowed;
            /* Change cursor to not-allowed */
        }

        .instructorContainer a {
            text-decoration: none;
            color: black;
        }

        .instructorContainer h1 {
            margin: 20px;
        }

        .instructorDetails {
            padding: 10px;
            display: flex;
            flex: row;
            gap: 10px;
            align-items: center;
        }

        .remarksContainer {
            margin-top: 20px;
            width: 100%;
        }

        .remarksContainer textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 20px;
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
        <aside class="navigation">
            <ul>
                <li class="logo">

                    <a href="">
                        <span class="custom-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.05 2.53004L4.03002 6.46004C2.10002 7.72004 2.10002 10.54 4.03002 11.8L10.05 15.73C11.13 16.44 12.91 16.44 13.99 15.73L19.98 11.8C21.9 10.54 21.9 7.73004 19.98 6.47004L13.99 2.54004C12.91 1.82004 11.13 1.82004 10.05 2.53004Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M5.63 13.08L5.62 17.77C5.62 19.04 6.6 20.4 7.8 20.8L10.99 21.86C11.54 22.04 12.45 22.04 13.01 21.86L16.2 20.8C17.4 20.4 18.38 19.04 18.38 17.77V13.13"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M21.4 15V9" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>


                        </span>
                        <span class="title">NEUST</span>
                    </a>
                </li>
                <li id="dashboard">
                    <a href="instructor_evaluation.php"><span class="icon"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.37 8.87988H17.62" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke="white" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12.37 15.8799H17.62" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke="white" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span><span class="title">Evaluation</span></a>
                </li>

            </ul>

        </aside>

        <div class="main">
            <div id="evaluationFormContainer" class="evalFormContainer" style=" margin-top: 20px;">
                <h2>Professor/Instructor Evaluation Form</h2>
                <p><strong>Directions:</strong> This questionnaire seeks your objective, honest, and fair evaluation
                    of the Professor's/Instructor's performance. Please indicate your rating on the different items
                    by selecting the rating in the corresponding column provided.</p>

                <?php if ($result->num_rows > 0): ?>
                    <p><strong>Ratings:</strong></p>
                    <ul>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li><?= htmlspecialchars($row['rates'] . ' - ' . $row['rate_name']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No ratings available.</p>
                <?php endif; ?>

                <h4>You are now Evaluating <span><?= $instructorName; ?></span></h4>

                <form action="process_instructor_evaluation.php" method="POST">
                    <input type="hidden" name="class_teacher_id" value="<?= htmlspecialchars($classTeacherId); ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>5</th>
                                <th>4</th>
                                <th>3</th>
                                <th>2</th>
                                <th>1</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultQuestions->num_rows > 0): ?>
                                <?php while ($row = $resultQuestions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['ques_id'] . '. ' . $row['questions']); ?></td>
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <td><input type="radio" name="q<?= $row['ques_id']; ?>" value="<?= $i; ?>" required>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No questions available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="remarksContainer" style="display: flex; flex-direction: column; gap: 5px;">
                        <label><strong>Remarks:</strong></label>
                        <textarea name="remarks" placeholder="Enter your remarks"></textarea>
                    </div>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>