<!-- Template Files here -->
<?php 
$title = "Dashboard";
require_once '../admin/partials/header.php'; 
require_once '../admin/partials/side-bar.php';

guard();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connection = getDatabaseConnection();

// Collect the number of subjects
$Subjectcount_query = "SELECT COUNT(*) as Subjectcount FROM subjects";
$Subjectresult = $connection->query($Subjectcount_query);
$Subjectcount = 0;
if ($Subjectresult && $row = $Subjectresult->fetch_assoc()) {
    $Subjectcount = $row['Subjectcount'];
}

// Collect the number of students
$Studentcount_query = "SELECT COUNT(*) as Studentcount FROM students";
$Studentresult = $connection->query($Studentcount_query);
$Studentcount = 0;
if ($Studentresult && $row = $Studentresult->fetch_assoc()) {
    $Studentcount = $row['Studentcount'];
}

// Collect the number of failed students
$Failedstudents_query = "SELECT COUNT(*) as Studentcount FROM students";
$Failedresult = $connection->query($Failedstudents_query);
$Failedstudents = 0;
if ($Failedresult && $row = $Failedresult->fetch_assoc()) {
    $Failedstudents = $row['Studentcount'];
}

// Collect the number of Passed students
$Passedstudents_query = "SELECT COUNT(*) as Studentcount FROM students";
$Passedresult = $connection->query($Passedstudents_query);
$Passedstudents = 0;
if ($Passedresult && $row = $Passedresult->fetch_assoc()) {
    $Passedstudents = $row['Studentcount'];
}

?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Dashboard</h1>        
    
    <div class="row mt-5">
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                <div class="card-body text-primary">
                <!--Subject Count-->
                    <h5 class="card-title"><?php echo htmlspecialchars($Subjectcount); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                <div class="card-body text-success">
                    <!--Student Count-->
                    <h5 class="card-title"><?php echo htmlspecialchars($Studentcount); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                <div class="card-body text-danger">
                    <!--Failed Students-->
                    <h5 class="card-title"><?php echo htmlspecialchars($Failedstudents); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                <div class="card-body text-success">
                    <!--Passed Students-->
                    <h5 class="card-title"><?php echo htmlspecialchars($Passedstudents); ?></h5>
                </div>
            </div>
        </div>
    </div>    
</main>

<!-- Added footer -->
<?php require_once '../admin/partials/footer.php'; ?>
<!-- Template Files here -->