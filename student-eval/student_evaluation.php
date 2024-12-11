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

            padding: 10px;
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


        .custom-evaluation-button {
            background-color: #2a2185;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
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
            width: 100%;

            margin-top: 20px;
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

        .evaluatedModal {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            margin: 20px auto;
            width: 50%;
            border-radius: 5px;
        }

        .evaluatedModal h2 {
            color: #4caf50;
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
                    <a href="student_evaluation.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
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
                <li id="dashboard">
                    <a href="evaluation_history.php"><span class="icon"> <svg width="24" height="24" viewBox="0 0 24 24"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 2V5" stroke="white" stroke-width="1.5" stroke-miterlimit="10"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M16 2V5" stroke="white" stroke-width="1.5" stroke-miterlimit="10"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M3.5 9.08997H20.5" stroke="white" stroke-width="1.5" stroke-miterlimit="10"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z"
                                    stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M11.9955 13.7H12.0045" stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M8.29431 13.7H8.30329" stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M8.29431 16.7H8.30329" stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span><span class="title">Evaluation History</span></a>
                </li>


            </ul>

        </aside>

        <div class="main">
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
            <div class="evaluationContainer" style="display: flex; flex-direction: column; width: 100%;">
                <div class="custom-instructor-container">

                    <?php
                    $studentId = $user_id; // Assuming $user_id is the logged-in student's ID
                    $allEvaluated = true; // Flag to check if all instructors are evaluated
                    
                    $instructorsQuery = "
    SELECT 
        ct.class_teacher_id,
        s.sub_id, 
        s.subjects,
        u.fname AS instructor_fname,
        u.lname AS instructor_lname,
        ct.teacher_type
    FROM user_class uc
    JOIN advisory_class ac ON uc.advisory_class_id = ac.advisory_class_id
    JOIN class_teacher ct ON ac.advisory_class_id = ct.advisory_class_id
    JOIN subject s ON ct.sub_id = s.sub_id
    JOIN users u ON ct.user_id = u.user_id
    JOIN class c ON ac.class_id = c.class_id
    WHERE uc.user_id = ? 
    GROUP BY s.sub_id, ct.class_teacher_id
    ORDER BY s.subjects;
";

                    $stmt = $conn->prepare($instructorsQuery);
                    $stmt->bind_param('i', $studentId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $rows = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows for reusability
                        foreach ($rows as $row) {
                            $evaluationQuery = "
            SELECT eval_id 
            FROM evaluation 
            WHERE class_teacher_id = ? AND user_id = ?
        ";
                            $evalStmt = $conn->prepare($evaluationQuery);
                            $evalStmt->bind_param('ii', $row['class_teacher_id'], $studentId);
                            $evalStmt->execute();
                            $evalResult = $evalStmt->get_result();

                            $evaluated = $evalResult->num_rows > 0 ? 'true' : 'false';

                            // If at least one instructor is not evaluated, set the flag to false
                            if ($evaluated === 'false') {
                                $allEvaluated = false;
                            }

                            $evalStmt->close();
                        }
                        $stmt->close();
                    } else {
                        $allEvaluated = false; // No instructors available
                    }

                    ?>
                    <?php if ($allEvaluated): ?>
                        <!-- Display Modal -->
                        <div class="evaluatedModal">
                            <h2>Thank you!</h2>
                            <p>You have completed all instructor evaluations for this term.</p>
                        </div>
                    <?php else: ?>
                        <h1>Instructors</h1>
                        <table class="instructorContainer" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Instructor Name</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Teacher Type</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Subject</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)): ?>
                                    <p>Wait for the admin to add your subjects.</p>
                                <?php else: ?>
                                    <?php foreach ($rows as $row):
                                        $fullName = htmlspecialchars($row['instructor_fname']) . ' ' . htmlspecialchars($row['instructor_lname']);
                                        $teacherType = htmlspecialchars($row['teacher_type']);
                                        $subjectName = htmlspecialchars($row['subjects']); ?>

                                        <?php
                                        $evaluationQuery = "
                        SELECT eval_id 
                        FROM evaluation 
                        WHERE class_teacher_id = ? AND user_id = ?
                    ";
                                        $evalStmt = $conn->prepare($evaluationQuery);
                                        $evalStmt->bind_param('ii', $row['class_teacher_id'], $studentId);
                                        $evalStmt->execute();
                                        $evalResult = $evalStmt->get_result();

                                        $evaluated = $evalResult->num_rows > 0 ? 'true' : 'false';
                                        $disabledClass = ($evaluated === 'true') ? 'disabled' : '';
                                        $evalStmt->close();
                                        ?>


                                        <tr>
                                            <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $fullName; ?></td>
                                            <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $teacherType; ?></td>
                                            <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $subjectName; ?></td>
                                            <td style="border: 1px solid #ddd; padding: 20px;">
                                                <?php if ($evaluated === 'false'): ?>
                                                    <a href="eval_form.php?class_teacher_id=<?php echo $row['class_teacher_id']; ?>&user_id=<?php echo $studentId; ?>"
                                                        class="custom-evaluation-button <?php echo $disabledClass; ?>">
                                                        Evaluate
                                                    </a>
                                                <?php else: ?>
                                                    <span>Evaluated</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>


                    </div>
                <?php endif; ?>
            </div>

        </div>

        <script>
            const toggle = document.querySelector('.toggle');
            const navigation = document.querySelector('.navigation');

            toggle.addEventListener('click', () => {
                navigation.classList.toggle('active');
            });

            function toggleUser() {
                const userDropdown = document.querySelector('.dropdown-content');
                userDropdown.style.display = userDropdown.style.display === 'none' ? 'block' : 'none';
            }


            function toggleSidebar() {
                const sidebar = document.querySelector('.navigation');
                sidebar.classList.toggle('collapsed');
            }

            document.addEventListener('DOMContentLoaded', function () {
                function showEvaluationForm(button) {
                    const classTeacherId = button.getAttribute('data-class-teacher-id');
                    const instructorName = button.getAttribute('data-instructor-name');

                    // Ensure elements exist before trying to set their properties
                    const classTeacherInput = document.getElementById('formClassTeacherId');
                    const instructorNameSpan = document.getElementById('activeInstructorName');
                    const formContainer = document.querySelector('.evalFormContainer');

                    if (classTeacherInput && instructorNameSpan && formContainer) {
                        classTeacherInput.value = classTeacherId;
                        instructorNameSpan.textContent = instructorName;

                        formContainer.style.display = 'block';
                        formContainer.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        console.error('Form elements are missing. Please check the HTML structure.');
                    }
                }

                // Attach the function to buttons with the class 'custom-evaluation-button'
                const evaluationButtons = document.querySelectorAll('.custom-evaluation-button');
                evaluationButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        showEvaluationForm(this);
                    });
                });
            });

        </script>
</body>

</html>