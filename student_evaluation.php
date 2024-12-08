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
$dbname = "cap";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
                    <a href="">
                        <span class="icon"><ion-icon name="school"></ion-icon></span>
                        <span class="title">NEUST</span>
                    </a>
                </li>

                <li id="dashboard">
                    <a href="eval_result.php"><span class="icon"><ion-icon name="desktop"></ion-icon></span><span
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

                $stmt = mysqli_prepare($conn, "SELECT u.fName, u.lName 
FROM users u
JOIN class_teacher ct ON u.user_id = ct.user_id
WHERE ct.class_teacher_id = ?
");
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

                    <form action="process_instructor_evaluation_student.php" method="POST">
                        <input type="hidden" name="class_teacher_id"
                            value="<?php echo htmlspecialchars($instructorId); ?>">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">



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

                <div class="instructorContainer" style="padding: 10px;">
                    <h1>Instructors</h1>
                    <?php
                    $sectionOfUserQuery = "
    SELECT 
        cs.section_id, 
        s.sections, 
        s.class_id,
        c.year_level                 
    FROM class_student cs
    JOIN section s ON cs.section_id = s.section_id
    JOIN class c ON s.class_id = c.class_id
    WHERE cs.user_id = ?
";

                    $stmt = $conn->prepare($sectionOfUserQuery);
                    $stmt->bind_param("i", $user_id); // Assuming $user_id contains the logged-in user's ID
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $sectionId = $row['section_id'];
                        $sectionName = $row['sections'];
                        $yearLevel = $row['year_level'];
                    } else {
                        $sectionName = "N/A";
                        $yearLevel = "N/A";
                    }
                    ?>

                    <p>Section: <?php echo htmlspecialchars($yearLevel . " " . $sectionName); ?></p>


                    <?php
                    $studentId = $user_id;  // Assuming $user_id is the logged-in student's ID
                    $instructorsQuery = "
    SELECT 
        ct.class_teacher_id,
        s.sub_id, 
        s.subjects,
        u.fname AS instructor_fname,
        u.lname AS instructor_lname,
        ct.teacher_type
    FROM class_student cs
    JOIN section_subjec ss ON cs.section_id = ss.section_id
    JOIN subject s ON ss.sub_id = s.sub_id
    LEFT JOIN class_teacher ct ON s.sub_id = ct.sub_id
    LEFT JOIN users u ON ct.user_id = u.user_id
    WHERE cs.user_id = ? 
    AND ct.class_teacher_id IN (SELECT MIN(class_teacher_id) 
                                 FROM class_teacher 
                                 WHERE sub_id = s.sub_id)
    ORDER BY s.subjects;
";




                    $stmt = $conn->prepare($instructorsQuery);
                    $stmt->bind_param('i', $studentId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $fullName = htmlspecialchars($row['instructor_fname']) . ' ' . htmlspecialchars($row['instructor_lname']);
                            $teacherType = htmlspecialchars($row['teacher_type']);
                            $subjectName = htmlspecialchars($row['subjects']);

                            // Check if the student has already evaluated the instructor
                            $evaluationQuery = "
                SELECT eval_id 
                FROM evaluation 
                WHERE class_teacher_id = ? AND user_id = ?
            ";
                            $evalStmt = $conn->prepare($evaluationQuery);
                            $evalStmt->bind_param('ii', $row['class_teacher_id'], $user_id);
                            $evalStmt->execute();
                            $evalResult = $evalStmt->get_result();

                            $evaluated = $evalResult->num_rows > 0 ? 'true' : 'false';
                            $disabledClass = ($evaluated === 'true') ? 'disabled' : '';

                            ?>
                                    <a href="student_evaluation.php?class_teacher_id=<?php echo $row['class_teacher_id']; ?>"
                                        class="instructor <?php echo $disabledClass; ?>" data-evaluated="<?php echo $evaluated; ?>"
                                        data-instructor-id="<?php echo $row['class_teacher_id']; ?>">
                                        <div class="instructorDetails">
                                            <div>
                                                <h3><?php echo $fullName; ?></h3>
                                                <p><?php echo $teacherType; ?></p>
                                                <span><?php echo $subjectName; ?></span>
                                            </div>
                                        </div>
                                    </a>
                                    <?php
                        }
                    } else {
                        echo '<p>No instructors available for evaluation.</p>';
                    }
                    ?>
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