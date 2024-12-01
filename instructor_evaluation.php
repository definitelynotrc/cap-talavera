<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch random instructor IDs if they are not already stored in the session
if (!isset($_SESSION['random_instructor_ids'])) {
    $result = mysqli_query($conn, "SELECT u.user_id, u.fName, u.mName, u.lName, u.role, e.eval_id
                                   FROM users u 
                                   LEFT JOIN evaluation e ON u.user_id = e.class_teacher_id
                                   WHERE u.role = 'Instructor' 
                                   ORDER BY RAND() LIMIT 3");  // Randomize the instructors

    $randomInstructorIds = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $randomInstructorIds[] = $row['user_id']; // Store the user_id in the array
        }

        // Store the random instructor IDs in the session
        $_SESSION['random_instructor_ids'] = $randomInstructorIds;
    } else {
        echo 'No instructors found';
    }
} else {
    $randomInstructorIds = $_SESSION['random_instructor_ids'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Instructor Evaluation</title>
    <link rel="stylesheet" href="style.css">
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
            display: flex;
            flex-direction: row;
        }

        .evalFormContainer {
            border: solid 1px black;
            width: 80%;
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
                        <!-- Clickable Image -->
                        <button class="dropdown-btn">
                            <img src="/img/admin.jpg" alt="User Profile" class="profile-img">
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="dropdown-content">
                            <a href="#">Manage Account</a>
                            <a href="logout.php">Logout</a>
                            <!-- PHP to log out user -->
                        </div>
                    </div>
                </div>

            </div>
            <?php
            $instructorId = isset($_GET['class_teacher_id']) ? $_GET['class_teacher_id'] : null;
            $instructorName = '';

            if ($instructorId) {

                $stmt = mysqli_prepare($conn, "SELECT fName, lName FROM users WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $instructorId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($instructor = mysqli_fetch_assoc($result)) {
                    $instructorName = $instructor['fName'] . ' ' . $instructor['lName'];
                }
            }
            ?>
            <div class="evaluationContainer">
                <div class="evalFormContainer">
                    <h2>Professor/Instructor Evaluation Form</h2>
                    <p><strong>Directions:</strong> This questionnaire seeks your objective, honest, and fair
                        evaluation
                        of the Professor's/Instructor's performance. Please indicate your rating on the different
                        items
                        by
                        selecting the rating in the corresponding column provided.</p>
                    <?Php $query = "SELECT rate_name, rates FROM rate ORDER BY rates DESC";
                    $result = $conn->query($query);

                    // Check if there are ratings available
                    if ($result->num_rows > 0): ?>
                        <p><strong>Ratings:</strong></p>
                        <ul>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <li><?php echo htmlspecialchars($row['rates'] . ' - ' . $row['rate_name']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>No ratings available.</p>
                    <?php endif;
                    ?>
                    <h4>You are now Evaluating <span
                            class="activeInstructor"><?php echo htmlspecialchars($instructorName); ?></span></h4>

                    <form action="process_instructor_evaluation.php" method="POST">
                        <input type="hidden" name="class_teacher_id"
                            value="<?php echo htmlspecialchars($instructorId); ?>">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">



                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="th">Item</th>
                                    <th>5</th>
                                    <th>4</th>
                                    <th>3</th>
                                    <th>2</th>
                                    <th>1</th>
                                </tr>
                            </thead>
                            <tbody>

                                <table>
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
                                        <?php


                                        // Fetch active questions
                                        $query = "SELECT ques_id, questions FROM question WHERE status = 'active' ORDER BY ques_id ASC";
                                        $result = $conn->query($query);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($row['ques_id'] . '. ' . $row['questions']) . '</td>';

                                                // Generate radio buttons for each question
                                                for ($i = 5; $i >= 1; $i--) {
                                                    echo '<td><input type="radio" name="q' . $row['ques_id'] . '" value="' . $i . '" required></td>';
                                                }
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="6">No questions available.</td></tr>';
                                        }

                                        ?>
                                    </tbody>
                                </table>



                                <div class="remarksContainer">
                                    <label for=""><strong>Remarks </strong></label>
                                    <textarea name="remarks" id="remarks" cols="30" rows="10"
                                        placeholder="Enter your remarks"></textarea>
                                    <button type="submit">Submit</button>
                                </div>
                    </form>
                </div>

                <div class="instructorContainer">
                    <h1>Instructors</h1>
                    <?php
                    if (!empty($randomInstructorIds)) {
                        // Query to fetch the instructors based on the random IDs along with their subject
                        $placeholders = implode(',', array_fill(0, count($randomInstructorIds), '?'));
                        $stmt = mysqli_prepare($conn, "
        SELECT 
            u.user_id, 
            u.fName, 
            u.mName, 
            u.lName, 
            u.role, 
            e.eval_id, 
            s.subjects,
            ct.teacher_type
        FROM users u
        LEFT JOIN evaluation e ON u.user_id = e.class_teacher_id
        LEFT JOIN class_teacher ct ON u.user_id = ct.user_id
        LEFT JOIN subject s ON ct.sub_id = s.sub_id
        WHERE u.role = 'Instructor' AND u.user_id IN ($placeholders)
    ");

                        // Bind parameters dynamically
                        mysqli_stmt_bind_param($stmt, str_repeat('i', count($randomInstructorIds)), ...$randomInstructorIds);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $fullName = htmlspecialchars($row['fName']) . ' ' . htmlspecialchars($row['mName']) . ' ' . htmlspecialchars($row['lName']);
                                $evaluated = $row['eval_id'] ? 'true' : 'false';
                                $subjectName = htmlspecialchars($row['subjects']);
                                ?>
                                <a href="instructor_evaluation.php?class_teacher_id=<?php echo $row['user_id']; ?>"
                                    class="instructor <?php echo ($evaluated == 'true') ? 'disabled' : ''; ?>"
                                    data-evaluated="<?php echo $evaluated; ?>" data-instructor-id="<?php echo $row['user_id']; ?>">
                                    <div class="instructorDetails">
                                        <div>
                                            <h3><?php echo $fullName; ?></h3>
                                            <p><?php echo htmlspecialchars($row['teacher_type']); ?>
                                            </p>
                                            <span class=""> <?php echo $subjectName; ?></span>
                                        </div>
                                    </div>
                                </a>
                                <?php
                            }
                        }
                    } else {
                        echo 'No instructors found';
                    }
                    ?>

                </div>

            </div>
        </div>

        <script src="main.js"></script>
        <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>


        <script>document.addEventListener("DOMContentLoaded", function () {
                const instructors = document.querySelectorAll('.instructor');
                const activeInstructorId = localStorage.getItem('activeInstructorId');

                if (activeInstructorId) {
                    const activeInstructor = document.querySelector(`.instructor[data-instructor-id='${activeInstructorId}']`);
                    if (activeInstructor) {
                        activeInstructor.classList.add('active');
                    }
                }

                instructors.forEach(instructor => {
                    instructor.addEventListener('click', function () {
                        if (!instructor.classList.contains('disabled')) {
                            instructors.forEach(item => item.classList.remove('active'));
                            instructor.classList.add('active');
                            const instructorId = instructor.getAttribute('data-instructor-id');
                            localStorage.setItem('activeInstructorId', instructorId);
                        }
                    });
                });
            });

        </script>
</body>

</html>