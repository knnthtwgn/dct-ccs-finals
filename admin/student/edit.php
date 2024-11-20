<?php
ob_start(); // Start output buffering
$title = "Edit Student"; // Set the title
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Edit Student</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
        </ol>
    </nav>

    <!-- Form for Editing Student -->
    <form method="post" action="">
        <!-- Display Student ID (Disabled) -->
        <div class="mb-3">
            <label for="student_id" class="form-label">Student ID</label>
            <input type="text" class="form-control" id="student_id" name="student_id" value="123" disabled>
        </div>

        <!-- First Name Input Field -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" value="John">
        </div>

        <!-- Last Name Input Field -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="Doe">
        </div>

        <!-- Submit Button -->
        <div class="mb-3">
            <button type="submit" name="update_student" class="btn btn-primary w-100">Update Student</button>
        </div>
    </form>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // Flush output buffer ?>
