<?php ob_start(); // Start output buffering ?>
<title>Detach a Subject</title>
<?php require_once '../partials/header.php'; ?>
<?php require_once '../partials/side-bar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Detach a Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=">Attach Subject to Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detach Subject from Student</li>
        </ol>
    </nav>

    <!-- Error or Success Message -->
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Error message goes here.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Success message goes here.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="card">
        <div class="card-body">
            <p>Are you sure you want to detach this subject from this student record?</p>
            <ul>
                <li><strong>Student ID:</strong> Student ID here</li>
                <li><strong>First Name:</strong> First Name here</li>
                <li><strong>Last Name:</strong> Last Name here</li>
                <li><strong>Subject Code:</strong> Subject Code here</li>
                <li><strong>Subject Name:</strong> Subject Name here</li>
            </ul>

            <form method="post">
                <a href="attach-subject.php?id=" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="detach_subject" class="btn btn-danger">Detach Subject from Student</button>
            </form>
        </div>
    </div>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // Flush output buffer ?>
