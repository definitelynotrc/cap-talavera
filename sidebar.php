<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="sidebar.css">
    <style>
        .custom-sidebar {
            width: 250px;
            background-color: #f8f9fa;
            padding: 15px 0;
            border-right: 1px solid #ddd;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Styling for the Menu List */
        .custom-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* Styling for List Items */
        .custom-menu li {
            margin-bottom: 10px;
        }

        .custom-menu li a {
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            transition: background-color 0.3s, color 0.3s;
        }

        .custom-menu li a .custom-icon {
            margin-right: 10px;
            display: flex;
            align-items: center;
        }

        .custom-menu li a:hover {
            background-color: #007bff;
            color: #fff;
        }

        .custom-menu li a:hover .custom-icon svg {
            stroke: #fff;
        }

        /* Dropdown Menu Styling */
        .custom-menu li ul {
            list-style: none;
            padding-left: 20px;
            display: none;
        }

        .custom-menu li ul li {
            margin: 5px 0;
        }

        .custom-menu li ul li a {
            padding: 8px 10px;
            font-size: 13px;

        }

        .custom-menu li:hover ul {
            display: block;
        }

        /* Active State for Menu Items */
        .custom-menu li.active>a {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }

        /* For Collapsible Dropdowns */
        .custom-menu li span.icon {
            cursor: pointer;
            margin-right: 10px;
        }

        #instructor {
            margin: 5px 0;
            padding: 8px 10px;
        }

        #student {
            margin: 5px 0;
            padding: 8px 10px;
        }

        #department {
            margin: 5px 0;
            padding: 10px 10px;
        }

        #department,
        #instructor,
        #student {
            color: white;
            font-size: 14px;
        }

        .custom-sidebar {
            width: 250px;
            background: #2A2185;
            /* Primary color */
            margin-top: 60px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            padding: 20px 20px;
            color: #FFFFFF;
            /* White text for contrast */
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
        }

        .custom-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .custom-menu-link {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            text-decoration: none;
            color: #FFFFFF;
            font-size: 16px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .custom-menu-link:hover {
            background: #4133A5;
            /* Slightly lighter shade */
            border-left: 4px solid #FFFFFF;
            /* Highlight indicator */
            border-radius: 0 5px 5px 0;
        }

        .custom-icon {
            margin-right: 10px;
        }

        .custom-title {
            font-weight: bold;
        }

        /* Navbar Styling */
        .custom-navbar {
            height: 60px;
            background: #2A2185;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            color: #FFFFFF;
            /* White text for navbar */
        }

        .custom-topbar {
            display: flex;
            align-items: center;
        }

        .custom-toggle {
            cursor: pointer;
            margin-right: 10px;
        }

        .custom-user {
            display: flex;
            align-items: center;
        }

        .custom-profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            cursor: pointer;
        }

        .custom-dropdown {
            position: relative;
        }

        .custom-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: #FFFFFF;
            color: #2A2185;
            /* Contrast with sidebar color */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            overflow: hidden;
            z-index: 10;
        }

        .custom-dropdown:hover .custom-dropdown-content {
            display: block;
        }

        .custom-dropdown-content a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #2A2185;
            transition: background 0.3s ease;
        }

        .custom-dropdown-content a:hover {
            background: #F4F4F4;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .custom-sidebar {
                width: 200px;
            }

            .custom-navbar {
                padding: 0 10px;
            }
        }
    </style>

</head>

<body>

    <nav class="custom-navbar">
        <div class="custom-logo">
            <a href="index.php" class="custom-menu-link">
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
                <span class="custom-title">NEUST</span>
            </a>
        </div>
        <div class="custom-topbar">

            <div class="custom-user">
                <div class="custom-dropdown">
                    <button class="custom-dropdown-btn">
                        <img src="/img/admin.jpg" alt="User Profile" class="custom-profile-img">
                    </button>
                    <div class="custom-dropdown-content">
                        <a href="#">Manage Account</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="custom-toggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M10.05 2.53004L4.03002 6.46004C2.10002 7.72004 2.10002 10.54 4.03002 11.8L10.05 15.73C11.13 16.44 12.91 16.44 13.99 15.73L19.98 11.8C21.9 10.54 21.9 7.73004 19.98 6.47004L13.99 2.54004C12.91 1.82004 11.13 1.82004 10.05 2.53004Z"
                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path
                d="M5.63 13.08L5.62 17.77C5.62 19.04 6.6 20.4 7.8 20.8L10.99 21.86C11.54 22.04 12.45 22.04 13.01 21.86L16.2 20.8C17.4 20.4 18.38 19.04 18.38 17.77V13.13"
                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M21.4 15V9" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>

    </div>
    <aside class="custom-sidebar">
        <ul class="custom-menu">


            <li id="dashboard">
                <a href="dashboard.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22 10.9V4.1C22 2.6 21.36 2 19.77 2H15.73C14.14 2 13.5 2.6 13.5 4.1V10.9C13.5 12.4 14.14 13 15.73 13H19.77C21.36 13 22 12.4 22 10.9Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M22 19.9V18.1C22 16.6 21.36 16 19.77 16H15.73C14.14 16 13.5 16.6 13.5 18.1V19.9C13.5 21.4 14.14 22 15.73 22H19.77C21.36 22 22 21.4 22 19.9Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M10.5 13.1V19.9C10.5 21.4 9.86 22 8.27 22H4.23C2.64 22 2 21.4 2 19.9V13.1C2 11.6 2.64 11 4.23 11H8.27C9.86 11 10.5 11.6 10.5 13.1Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M10.5 4.1V5.9C10.5 7.4 9.86 8 8.27 8H4.23C2.64 8 2 7.4 2 5.9V4.1C2 2.6 2.64 2 4.23 2H8.27C9.86 2 10.5 2.6 10.5 4.1Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </span><span class="title">Dashboard</span></a>
            </li>
            <li id="instructor">
                <span class="icon" onclick="showInstructorDropdown()"><svg width="24" height="24" viewBox="0 0 24 24"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M19.2101 15.74L15.67 19.2801C15.53 19.4201 15.4 19.68 15.37 19.87L15.18 21.22C15.11 21.71 15.45 22.05 15.94 21.98L17.29 21.79C17.48 21.76 17.75 21.63 17.88 21.49L21.42 17.95C22.03 17.34 22.32 16.63 21.42 15.73C20.53 14.84 19.8201 15.13 19.2101 15.74Z"
                            stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M18.7001 16.25C19.0001 17.33 19.84 18.17 20.92 18.47" stroke="white" stroke-width="1.5"
                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M3.40991 22C3.40991 18.13 7.25994 15 11.9999 15C13.0399 15 14.0399 15.15 14.9699 15.43"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </span><span class="title">Instructor</span>
                <ul id="instructorDropdown">
                    <li>
                        <a href="student.php"><span class="icon">
                            </span><span class="title">Manage Instructors</span></a>
                    </li>
                    <li>
                        <a href="student.php"><span class="icon">
                            </span><span class="title">Instructor Subjects</span></a>
                    </li>
                </ul>
            </li>
            <li id="student">
                <span class="icon" onclick="showStudentDropdown()"><svg width="24" height="24" viewBox="0 0 24 24"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M19.2101 15.74L15.67 19.2801C15.53 19.4201 15.4 19.68 15.37 19.87L15.18 21.22C15.11 21.71 15.45 22.05 15.94 21.98L17.29 21.79C17.48 21.76 17.75 21.63 17.88 21.49L21.42 17.95C22.03 17.34 22.32 16.63 21.42 15.73C20.53 14.84 19.8201 15.13 19.2101 15.74Z"
                            stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M18.7001 16.25C19.0001 17.33 19.84 18.17 20.92 18.47" stroke="white" stroke-width="1.5"
                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M3.40991 22C3.40991 18.13 7.25994 15 11.9999 15C13.0399 15 14.0399 15.15 14.9699 15.43"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </span><span class="title">Student</span>
                <ul id="studentDropdown">
                    <li>
                        <a href="student.php"><span class="icon">
                            </span><span class="title">Manage Student</span></a>
                    </li>
                    <li>
                        <a href="student.php"><span class="icon">
                            </span><span class="title">Student Section</span></a>
                    </li>
                </ul>
            </li>
            <li id="department">
                <span class="icon" onclick="showdepartmentDropdown()"><svg width="24" height="24" viewBox="0 0 24 24"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M6.44 2H17.55C21.11 2 22 2.89 22 6.44V12.77C22 16.33 21.11 17.21 17.56 17.21H6.44C2.89 17.22 2 16.33 2 12.78V6.44C2 2.89 2.89 2 6.44 2Z"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 17.22V22" stroke="white" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M2 13H22" stroke="white" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M7.5 22H16.5" stroke="white" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>

                </span><span class="title">Department</span>
                <ul id="departmentDropdown">
                    <li id="department">
                        <a href="department.php">
                            <span class="title">Manage Department</span></a>
                    </li>
                    <li id="subject">
                        <a href="subject.php"><span class="title">Manage Subject</span></a>
                    </li>
                    <li id="class">
                        <a href="class.php"><span class="title">Manage Class</span></a>
                    </li>
                    <li id="section">
                        <a href="section.php"><span class="title">Manage Section</span></a>
                    </li>
                    <li id="semester">
                        <a href="semester.php"><span class="title">Manage Semester</span></a>
                    </li>
                    <li id="academic">
                        <a href="acad_year.php"><span class="title">Manage Academic Year</span></a>
                    </li>
                </ul>
            </li>

            <li id="question">
                <a href="question.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M17 18.4301H13L8.54999 21.39C7.88999 21.83 7 21.3601 7 20.5601V18.4301C4 18.4301 2 16.4301 2 13.4301V7.42999C2 4.42999 4 2.42999 7 2.42999H17C20 2.42999 22 4.42999 22 7.42999V13.4301C22 16.4301 20 18.4301 17 18.4301Z"
                                stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M12.0001 11.36V11.15C12.0001 10.47 12.4201 10.11 12.8401 9.82001C13.2501 9.54001 13.66 9.18002 13.66 8.52002C13.66 7.60002 12.9201 6.85999 12.0001 6.85999C11.0801 6.85999 10.3401 7.60002 10.3401 8.52002"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.9955 13.75H12.0045" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>


                    </span><span class="title">Question</span></a>
            </li>
            <li id="rate">
                <a href="rate.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.39 5.21L16.7999 8.02999C16.9899 8.41999 17.4999 8.78999 17.9299 8.86999L20.48 9.28999C22.11 9.55999 22.49 10.74 21.32 11.92L19.3299 13.91C18.9999 14.24 18.81 14.89 18.92 15.36L19.4899 17.82C19.9399 19.76 18.9 20.52 17.19 19.5L14.7999 18.08C14.3699 17.82 13.65 17.82 13.22 18.08L10.8299 19.5C9.11994 20.51 8.07995 19.76 8.52995 17.82L9.09996 15.36C9.20996 14.9 9.01995 14.25 8.68995 13.91L6.69996 11.92C5.52996 10.75 5.90996 9.56999 7.53996 9.28999L10.0899 8.86999C10.5199 8.79999 11.03 8.41999 11.22 8.02999L12.63 5.21C13.38 3.68 14.62 3.68 15.39 5.21Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8 5H2" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M5 19H2" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M3 12H2" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>


                    </span><span class="title">Rate</span></a>
            </li>
            <li id="evaluation">
                <a href="eval_result.php"><span class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.26001 11.0199V15.9899C4.26001 17.8099 4.26001 17.8099 5.98001 18.9699L10.71 21.6999C11.42 22.1099 12.58 22.1099 13.29 21.6999L18.02 18.9699C19.74 17.8099 19.74 17.8099 19.74 15.9899V11.0199C19.74 9.19994 19.74 9.19994 18.02 8.03994L13.29 5.30994C12.58 4.89994 11.42 4.89994 10.71 5.30994L5.98001 8.03994C4.26001 9.19994 4.26001 9.19994 4.26001 11.0199Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M17.5 7.63V5C17.5 3 16.5 2 14.5 2H9.5C7.5 2 6.5 3 6.5 5V7.56" stroke="white"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M12.63 10.99L13.2 11.88C13.29 12.02 13.49 12.16 13.64 12.2L14.66 12.46C15.29 12.62 15.46 13.16 15.05 13.66L14.38 14.47C14.28 14.6 14.2 14.83 14.21 14.99L14.27 16.04C14.31 16.69 13.85 17.02 13.25 16.78L12.27 16.39C12.12 16.33 11.87 16.33 11.72 16.39L10.74 16.78C10.14 17.02 9.68002 16.68 9.72002 16.04L9.78002 14.99C9.79002 14.83 9.71002 14.59 9.61002 14.47L8.94002 13.66C8.53002 13.16 8.70002 12.62 9.33002 12.46L10.35 12.2C10.51 12.16 10.71 12.01 10.79 11.88L11.36 10.99C11.72 10.45 12.28 10.45 12.63 10.99Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </span><span class="title">Evaluation</span></a>
            </li>
        </ul>
    </aside>
    <div class="main"></div>









    <script src="main.js"></script>

    <script>
        function showInstructorDropdown() {
            const dropdown = document.getElementById("instructorDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        function showStudentDropdown() {
            const dropdown = document.getElementById("studentDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        function showdepartmentDropdown() {
            const dropdown = document.getElementById("departmentDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

    </script>
</body>

</html>