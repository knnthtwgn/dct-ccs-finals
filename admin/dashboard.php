<?php 
$title = "Dashboard"; // Set the title for the page

require_once '../functions.php'; // Include the functions file
require_once '../admin/partials/header.php'; // Include the header file
require_once '../admin/partials/side-bar.php'; // Include the sidebar file

guard(); // Ensure the user is authenticated before accessing the page

// Enable detailed error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establish a database connection
$connection = getDatabaseConnection();

// Fetch the total number of subjects from the database
$Subjectcount_query = "SELECT COUNT(*) as Subjectcount FROM subjects";
$Subjectresult = $connection->query($Subjectcount_query);
$Subjectcount = 0;
if ($Subjectresult && $row = $Subjectresult->fetch_assoc()) {
    $Subjectcount = $row['Subjectcount']; // Assign the subject count
}

// Fetch the total number of students from the database
$Studentcount_query = "SELECT COUNT(*) as Studentcount FROM students";
$Studentresult = $connection->query($Studentcount_query);
$Studentcount = 0;
if ($Studentresult && $row = $Studentresult->fetch_assoc()) {
    $Studentcount = $row['Studentcount']; // Assign the student count
}

// Query to fetch the number of students who failed (average grade < 75)
$Failedstudents_query = "
    SELECT COUNT(*) AS Failedstudents
    FROM (
        SELECT 
            students.id AS student_id,
            AVG(students_subjects.grade) AS average_grade
        FROM students
        LEFT JOIN students_subjects ON students.id = students_subjects.student_id
        GROUP BY students.id
        HAVING average_grade < 75
    ) AS failed";
$Failedstudents = 0;
$Failedstudents_result = $connection->query($Failedstudents_query);
if ($Failedstudents_result && $row = $Failedstudents_result->fetch_assoc()) {
    $Failedstudents = $row['Failedstudents']; // Assign the failed students count
}

// Query to fetch the number of students who passed (average grade >= 75)
$Passedstudents_query = "
    SELECT COUNT(*) AS Passedstudents
    FROM (
        SELECT 
            students.id AS student_id,
            AVG(students_subjects.grade) AS average_grade
        FROM students
        LEFT JOIN students_subjects ON students.id = students_subjects.student_id
        GROUP BY students.id
        HAVING average_grade >= 75
    ) AS passed";
$Passedstudents = 0;
$Passedstudents_result = $connection->query($Passedstudents_query);
if ($Passedstudents_result && $row = $Passedstudents_result->fetch_assoc()) {
    $Passedstudents = $row['Passedstudents']; // Assign the passed students count
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Dashboard</h1>

    <!-- Display statistical data -->
    <div class="row mt-5">
        <!-- Number of Subjects -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?php echo htmlspecialchars($Subjectcount); ?></h5>
                </div>
            </div>
        </div>

        <!-- Number of Students -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?php echo htmlspecialchars($Studentcount); ?></h5>
                </div>
            </div>
        </div>

        <!-- Number of Failed Students -->
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                <div class="card-body text-danger">
                    <h5 class="card-title"><?php echo htmlspecialchars($Failedstudents); ?></h5>
                </div>
            </div>
        </div>

        <!-- Number of Passed Students -->
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?php echo htmlspecialchars($Passedstudents); ?></h5>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
require_once '../admin/partials/footer.php'; // Include the footer file
