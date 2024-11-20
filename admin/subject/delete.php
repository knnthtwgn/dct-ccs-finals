<?php
ob_start(); // Start output buffering
$title = "Delete Subject"; // Set the title

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../functions.php';
require_once '../partials/header.php';
require_once '../partials/side-bar.php';

guard(); // Ensure user is authenticated

// Initialize variables
$error_message = '';
$success_message = '';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Delete Subject</h1>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
        <a class="breadcrumb-item" href="/admin/dashboard.php">Dashboard</a>
        <a class="breadcrumb-item" href="/admin/subject/add.php">Add Subject</a>
        <span class="breadcrumb-item active">Delete Subject</span>
    </nav>

    <!-- Error or Success Message Display -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Subject Deletion Confirmation -->
    <div class="card mt-4">
        <div class="card-body">
            <p>Are you sure you want to delete the following subject record?</p>
            <ul>
                <li><strong>Subject Code:</strong> [Subject Code Here]</li>
                <li><strong>Subject Name:</strong> [Subject Name Here]</li>
            </ul>
            <form method="post">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='/admin/subject/add.php'">Cancel</button>
                <button type="submit" name="delete_subject" class="btn btn-primary">Delete Subject Record</button>
            </form>
        </div>
    </div>
</main>

<?php
require_once '../partials/footer.php';
ob_end_flush(); // Flush output buffer
?>
