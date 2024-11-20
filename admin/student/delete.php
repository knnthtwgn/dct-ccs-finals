<?php
$title = "Delete a Student"; // Set the title
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Delete a Student</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
        </ol>
    </nav>

    <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="errorMessage">
       
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="successMessage">
     
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

   
    <div class="card">
        <div class="card-body">
            <p>Are you sure you want to delete the following student record?</p>
            <ul>
                <li><strong>Student ID:</strong> [Student ID]</li>
                <li><strong>First Name:</strong> [First Name]</li>
                <li><strong>Last Name:</strong> [Last Name]</li>
            </ul>
            <form method="post" action="">
                <button type="submit" name="delete_student" class="btn btn-danger">Delete Student Record</button>
                <a href="../student/register.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php require_once '../partials/footer.php'; ?>
