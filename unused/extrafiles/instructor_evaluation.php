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



$getClassTeacherQuery = "SELECT class_teacher_id FROM class_teacher WHERE user_id = ?";
$stmt = $conn->prepare($getClassTeacherQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$classTeacher = $result->fetch_assoc();

$userDepQuery = "SELECT dep_id FROM user_dep WHERE user_id = ?";
$stmt = $conn->prepare($userDepQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $userDepId = $row['dep_id'];
} else {
    // Handle the case where no department ID is found
    echo ("No department found for the user.");
}










?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Instructor Evaluation</title>
    <link rel="stylesheet" href="sidebar.css">
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
                        <a href="logout.php">Logout</a>
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
            <div class="evaluationContainer" style="display: flex; flex-direction: column;">


                <div class="custom-instructor-container">
                    <h1>Instructor List</h1>
                    <?php
                    $instructorQuery = "
        SELECT 
            u.user_id, 
            u.fName, 
            u.lName, 
            ct.teacher_type,
            ct.class_teacher_id, 
            GROUP_CONCAT(DISTINCT s.subjects ORDER BY s.subjects SEPARATOR ', ') AS subjects 
        FROM users u
        JOIN class_teacher ct ON u.user_id = ct.user_id
        JOIN subject s ON ct.sub_id = s.sub_id
        JOIN user_dep ud ON u.user_id = ud.user_id
        WHERE ud.dep_id = ? AND u.role = 'Instructor'
        GROUP BY u.user_id, u.fName, u.lName, ct.teacher_type";

                    $stmt = $conn->prepare($instructorQuery);
                    $stmt->bind_param('i', $userDepId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $instructors = $result->fetch_all(MYSQLI_ASSOC);

                    if (!empty($instructors)): ?>
                        <table class="instructorTable" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Instructor Name</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Teacher Type</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Subjects</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($instructors as $row):
                                    $fullName = htmlspecialchars($row['fName']) . ' ' . htmlspecialchars($row['lName']);
                                    $teacherType = htmlspecialchars($row['teacher_type']);
                                    $subjectName = htmlspecialchars($row['subjects']);

                                    $evaluationQuery = "
                        SELECT eval_id 
                        FROM evaluation 
                        WHERE class_teacher_id = ? AND user_id = ?";

                                    $evalStmt = $conn->prepare($evaluationQuery);
                                    if (!$evalStmt) {
                                        die("Query preparation failed: " . $conn->error);
                                    }

                                    $evalStmt->bind_param('ii', $row['class_teacher_id'], $user_id);
                                    $evalStmt->execute();
                                    $evalResult = $evalStmt->get_result();
                                    $evaluated = $evalResult->num_rows > 0 ? 'true' : 'false';
                                    $disabledClass = ($evaluated === 'true') ? 'disabled' : '';
                                    ?>
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $fullName; ?></td>
                                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $teacherType; ?></td>
                                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $subjectName; ?></td>
                                        <td style="border: 1px solid #ddd; padding: 8px;">
                                            <?php if ($evaluated === 'false') { ?>

                                                <button class="custom-evaluate-btn"
                                                    data-instructor-id="<?php echo $row['class_teacher_id']; ?>"
                                                    data-instructor-name="<?php echo $fullName; ?>">
                                                    Evaluate
                                                </button>
                                            <?php } else { ?>
                                                <span>Evaluated</span>
                                            <?php } ?>
                                        </td>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="custom-no-instructors">No instructors available for evaluation.</p>
                    <?php endif; ?>
                </div>
                <div id="evaluationFormContainer" class="evalFormContainer" style="display:none; margin-top: 20px;">
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
                    <h4>You are now Evaluating <span class="activeInstructor "
                            id="instructorName"><?php echo htmlspecialchars($instructorName); ?></span></h4>

                    <form action="process_instructor_evaluation.php" method="POST">
                        <input type="hidden" id="classTeacherId" name="class_teacher_id"
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
            function showInstructorDropdown() {
                const instructorDropdown = document.querySelector('.instructorDropdown');
                instructorDropdown.style.display = instructorDropdown.style.display === 'none' ? 'block' : 'none';
            }

            function showStudentDropdown() {
                const studentDropdown = document.querySelector('.studentDropdown'); // Corrected variable name
                studentDropdown.style.display = studentDropdown.style.display === 'none' ? 'block' : 'none';
            }

            function showDepartmentDropdown() {
                const departmentDropdown = document.querySelector('.departmentDropdown'); // Corrected variable name
                departmentDropdown.style.display = departmentDropdown.style.display === 'none' ? 'block' : 'none';
            }

            function toggleSidebar() {
                const sidebar = document.querySelector('.navigation');
                sidebar.classList.toggle('collapsed');
            }
            document.addEventListener('DOMContentLoaded', function () {
                // Define the openEvaluationForm function globally
                function openEvaluationForm(button) {
                    console.log('Button clicked!'); // Debugging: Check if the function is triggered

                    const formContainer = document.getElementById('evaluationFormContainer');
                    const classTeacherIdInput = document.getElementById('classTeacherId');
                    const instructorNameDisplay = document.getElementById('instructorName');

                    // Log the values to confirm they are being correctly retrieved
                    console.log('Instructor ID:', button.getAttribute('data-instructor-id'));
                    console.log('Instructor Name:', button.getAttribute('data-instructor-name'));

                    // Set form values based on button data
                    classTeacherIdInput.value = button.getAttribute('data-instructor-id');
                    instructorNameDisplay.textContent = button.getAttribute('data-instructor-name');

                    // Show the form
                    formContainer.style.display = 'block';
                    formContainer.scrollIntoView({ behavior: 'smooth' });
                }

                // Add event listeners to the "Evaluate" buttons
                document.querySelectorAll('.custom-evaluate-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        openEvaluationForm(button);
                    });
                });
            });


        </script>
</body>

</html>