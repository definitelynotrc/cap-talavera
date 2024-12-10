const toggle = document.querySelector(".toggle");
const navigation = document.querySelector(".navigation");

toggle.addEventListener("click", () => {
  navigation.classList.toggle("active");
});

function toggleUser() {
  const userDropdown = document.querySelector(".dropdown-content");
  userDropdown.style.display =
    userDropdown.style.display === "none" ? "block" : "none";
}
function showUsersDropdown() {
  const instructorDropdown = document.querySelector(".userDropdown");
  instructorDropdown.style.display =
    instructorDropdown.style.display === "none" ? "block" : "none";
}

function showSubjectDropdown() {
  const studentDropdown = document.querySelector(".subjectDropdown"); // Corrected variable name
  studentDropdown.style.display =
    studentDropdown.style.display === "none" ? "block" : "none";
}

function showClassesDropdown() {
  const studentDropdown = document.querySelector(".classesDropdown"); // Corrected variable name
  studentDropdown.style.display =
    studentDropdown.style.display === "none" ? "block" : "none";
}

function showDepartmentDropdown() {
  const departmentDropdown = document.querySelector(".departmentDropdown"); // Corrected variable name
  departmentDropdown.style.display =
    departmentDropdown.style.display === "none" ? "block" : "none";
}

function toggleSidebar() {
  const sidebar = document.querySelector(".navigation");
  sidebar.classList.toggle("collapsed");
}
