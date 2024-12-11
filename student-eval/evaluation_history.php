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

$studentId = $user_id;


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
                    <a href="student_evaluation.php"><span class="icon"> <svg width="24" height="24" viewBox="0 0 24 24"
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
            <div class="custom-instructor-container">
                <h1>Evaluation History</h1>
                <?php
                // Fetch distinct semesters for the dropdown
                $semestersQuery = "SELECT DISTINCT semesters, sem_id FROM semester ORDER BY semesters ASC";
                $semStmt = $conn->prepare($semestersQuery);
                $semStmt->execute();
                $semesters = $semStmt->get_result();
                ?>

                <!-- Semester Filter Dropdown -->
                <div style="margin-bottom: 20px; width: 10%;">
                    <label for="semesterFilter" style="font-weight: bold;">Filter by Semester:</label>
                    <select id="semesterFilter" name="semester" style="padding: 5px; margin-left: 10px;">
                        <option value=""> Semesters</option>
                        <?php while ($row = $semesters->fetch_assoc()):
                            $semesterFilter = null; ?>

                            <option value="<?php echo htmlspecialchars($row['sem_id']); ?>" <?php echo ($row['sem_id'] == $semesterFilter) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['semesters']); ?>
                            </option>

                        <?php endwhile; ?>
                    </select>
                </div>

                <table class="instructorContainer" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 8px;">Instructor Name</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Teacher Type</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Subject</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Semester</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Evaluation Date</th>
                        </tr>
                    </thead>
                    <tbody id="evaluationHistory">
                        <?php
                        // Query to fetch evaluation history with semester filter
                        $semesterFilter = isset($_GET['semester']) ? $_GET['semester'] : '';


                        $query = "
SELECT DISTINCT 
    ct.class_teacher_id,
    s.subjects, 
    u.fname AS instructor_fname,
    u.lname AS instructor_lname,
    ct.teacher_type,
    sem.semesters,
    e.date_created AS evaluation_date
FROM user_class uc
JOIN advisory_class ac ON uc.advisory_class_id = ac.advisory_class_id
JOIN class_teacher ct ON ac.advisory_class_id = ct.advisory_class_id
JOIN subject s ON ct.sub_id = s.sub_id
JOIN users u ON ct.user_id = u.user_id
JOIN class c ON ac.class_id = c.class_id
JOIN semester sem ON ac.sem_id = sem.sem_id
JOIN evaluation e ON e.class_teacher_id = ct.class_teacher_id AND e.user_id = uc.user_id
WHERE uc.user_id = ?

";

                        // Conditionally add semester filter
                        if (!empty($semesterFilter)) {
                            $query .= " AND sem.sem_id = ?";
                        }


                        // Add ORDER BY clause
                        $query .= " ORDER BY ct.class_teacher_id ASC, s.subjects ASC";

                        $stmt = $conn->prepare($query);

                        // Bind parameters
                        if (!empty($semesterFilter)) {
                            $stmt->bind_param('is', $studentId, $semesterFilter);
                        } else {
                            $stmt->bind_param('i', $studentId);
                        }

                        $stmt->execute();
                        $result = $stmt->get_result();



                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $fullName = htmlspecialchars($row['instructor_fname']) . ' ' . htmlspecialchars($row['instructor_lname']);
                                $teacherType = htmlspecialchars($row['teacher_type']);
                                $subjectName = htmlspecialchars($row['subjects']);
                                $semester = htmlspecialchars($row['semesters']);
                                $evaluationDate = !empty($row['evaluation_date']) ? date("F j, Y", strtotime($row['evaluation_date'])) : 'Not Evaluated';

                                $isEvaluated = !empty($row['evaluation_date']);
                                $action = $isEvaluated
                                    ? '<span>Evaluated</span>'
                                    : '<button class="custom-evaluation-button" data-class-teacher-id="' . $row['class_teacher_id'] . '" data-instructor-name="' . $fullName . '">Evaluate</button>';
                                ?>
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $fullName; ?></td>
                                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $teacherType; ?></td>
                                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $subjectName; ?></td>
                                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $semester; ?></td>
                                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $evaluationDate; ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No evaluations found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>


            </div>
        </div>
    </div>
    <script src="../js/sidebar.js"></script>
    <script>
        // Add event listener to filter evaluations by semester
        document.getElementById('semesterFilter').addEventListener('change', function () {
            const selectedSemester = this.value;
            const url = new URL(window.location.href);
            if (selectedSemester) {
                url.searchParams.set('semester', selectedSemester);
            } else {
                url.searchParams.delete('semester');
            }
            window.location.href = url.toString();
        });

    </script>
</body>


</html>