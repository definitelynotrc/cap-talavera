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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Results</title>
    <link rel="stylesheet" href="../sidebar.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        form {
            max-width: 500px;
            margin: auto;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        select,
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .view-evaluations-btn,
        .generate-evaluations-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;


        }

        @media print {
            .evaluator-name {
                display: none;
            }

            .printBtn {
                display: none;
            }
        }


        .btn-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {}

        td {
            border-bottom: 1px solid #ddd;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
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

            <table class="table ta">
                <h2 style="margin-left: 10px;">Evaluation Results</h2>
                <thead>
                    <tr>
                        <th>Instructor Name</th>
                        <th>Department</th>
                        <th>Total Respondents</th>
                        <th>Average Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database query to fetch instructor evaluation summary
                    $query = "
SELECT 
    ct.class_teacher_id,
    u.fname AS fname,
    u.user_id,
    UD.dep_id,
    D.department,
    u.lname AS lname,
    COUNT(DISTINCT e.eval_id) AS total_respondents,
    SUM(e.rate_result) AS total_ratings,  -- Summing all ratings
    COUNT(DISTINCT e.eval_id) AS total_respondents  -- Counting distinct evaluations (respondents)
FROM evaluation e
JOIN class_teacher ct ON e.class_teacher_id = ct.class_teacher_id
JOIN users u ON ct.user_id = u.user_id
JOIN user_dep UD ON u.user_id = UD.user_id
JOIN department D ON UD.dep_id = D.dep_id
GROUP BY ct.class_teacher_id, u.fname, u.lname
ORDER BY u.lname ASC;
";

                    $result = $conn->query($query);

                    // Check if results are available
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Calculate the average rating by dividing total_ratings by total_respondents
                            if ($row['total_respondents'] > 0) {
                                $avg_rating = $row['total_ratings'] / $row['total_respondents'];
                            } else {
                                $avg_rating = 0; // Prevent division by zero
                            }

                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['lname'] . ', ' . $row['fname']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['department']) . ' </td>';
                            echo '<td>' . $row['total_respondents'] . '</td>';
                            echo '<td>' . number_format($avg_rating, 2) . '</td>';  // Display the average rating with 2 decimal points
                            echo '<td>';
                            echo '<div class="btn-container"><button class="view-evaluations-btn" data-instructor-id="' . $row['class_teacher_id'] . '">View Evaluations</button><button class="generate-evaluations-btn" data-instructor-id="' . $row['class_teacher_id'] . '">Generate Results</button></div>';

                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4">No evaluations found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>

            <div id="evaluation-details" style="display: none;  background-color: #F2F2F2;">
                <h3>Evaluation Details</h3>
                <button id="printBtn">Print Evaluations</button>


                <div id="evaluator-info">
                    <!-- Dynamic Evaluator Info -->
                </div>
                <table class="table2">
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
                    <tbody id="evaluation-answers">
                        <!-- Evaluation details will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal for displaying evaluation results -->
        <div id="evaluationResultsModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h3>Evaluation Results</h3>
                <div id="evaluationResults"></div>
                <div>
                    <strong>Average Rating: </strong><span id="averageRating"></span>
                </div>
            </div>
        </div>
        <!-- <div id="evaluationResults">
       
    </div> -->

        <script src="../js/sidebar.js"></script>
        <script>

            $(document).ready(function () {
                // Set up the event listener for dynamically loaded content
                $(document).on('click', '.printBtn', function () {
                    const table = $(this).closest('table')[0]; // Get the closest table to the print button
                    const newWindow = window.open('', '', 'width=800,height=600');

                    // Define the print styles
                    const styles = `
            <style>
                body { font-family: Arial, sans-serif; }
                .evaluator-name { display: none; } /* Hide evaluator's name during print */
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
            </style>
        `;

                    newWindow.document.write('<html><head><title>Print Evaluation</title>' + styles + '</head><body>');
                    newWindow.document.write(table.outerHTML); // Print the closest table's HTML
                    newWindow.document.write('</body></html>');
                    newWindow.document.close();
                    newWindow.print();
                });

                // Handle the view evaluations button click
                $('.view-evaluations-btn').click(function () {
                    const instructorId = $(this).data('instructor-id'); // Fetch the instructor ID from the button

                    $.ajax({
                        url: 'fetch_instructor_evaluations.php',
                        method: 'POST',
                        dataType: 'json',
                        data: { instructor_id: instructorId },
                        success: function (response) {
                            if (response.error) {
                                alert(response.error); // Handle any errors sent by PHP
                            } else {
                                $('#evaluation-details').html(response.evaluationTables); // Populate multiple tables
                                $('#evaluation-details').show(); // Show the evaluation details section
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText); // Log the raw response
                            alert('Failed to fetch evaluation details.');
                        }
                    });
                });

                // Open the modal when the "Generate Results" button is clicked
                $(document).on('click', '.generate-evaluations-btn', function () {
                    const instructorId = $(this).data('instructor-id');

                    // Show the modal
                    document.getElementById('evaluationResultsModal').style.display = 'block';

                    // Fetch evaluation results for this instructor
                    fetchEvaluationResults(instructorId);
                });

                // Close the modal when the close button is clicked
                $('.close-btn').click(function () {
                    document.getElementById('evaluationResultsModal').style.display = 'none';
                });

                // Function to fetch evaluation results via AJAX
                function fetchEvaluationResults(instructorId) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'fetch_evaluation_results.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                alert(response.error);
                            } else {
                                displayEvaluationResults(response);
                            }
                        }
                    };
                    xhr.send('instructor_id=' + instructorId);
                }

                // Function to display the evaluation results in the modal
                function displayEvaluationResults(response) {
                    const evaluationResultsDiv = document.getElementById('evaluationResults');
                    const averageRatingSpan = document.getElementById('averageRating');

                    // Clear previous results
                    evaluationResultsDiv.innerHTML = '';

                    let evalResultsHTML = '';
                    let ratingCounts = {}; // Object to store rating counts for each question

                    response.evaluations.forEach((evaluation, index) => {
                        evalResultsHTML += `Evaluation ${index + 1}:<br>`;
                        evalResultsHTML += `<strong>Question ${evaluation.question_id}:</strong> ${evaluation.question_text}<br>`;

                        // Loop through the ratings for each question and count them
                        const counts = evaluation.rating_counts;
                        evalResultsHTML += `
             Outstanding - ${counts[5]}<br>
           Very Satisfactory - ${counts[4]}<br>
            Satisfactory- ${counts[3]}<br>
            Poor- ${counts[2]}<br>
             Very Poor - ${counts[1]}<br><br>
        `;
                    });



                    // Display the evaluation results in the modal
                    evaluationResultsDiv.innerHTML = evalResultsHTML;
                    averageRatingSpan.textContent = response.average_rating.toFixed(2);
                }


            });


        </script>
</body>

</html>