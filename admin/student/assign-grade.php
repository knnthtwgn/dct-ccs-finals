<?php
ob_start(); // Start output buffering
$title = "Assign Grade"; // Set the title
require_once '../../functions.php';
require_once '../partials/header.php';
require_once '../partials/side-bar.php';

?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Assign Grade to Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=">Attach Subject to Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Grade to Subject</li>
        </ol>
    </nav>

    <!-- Alert messages for errors or success -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Selected student and subject details -->
    <div class="card">
        <div class="card-body">
            <h5>Selected Student and Subject Information</h5>
            <ul>
                <li><strong>Student ID:</strong> [Student ID]</li>
                <li><strong>Name:</strong> [Student Name]</li>
                <li><strong>Subject Code:</strong> [Subject Code]</li>
                <li><strong>Subject Name:</strong> [Subject Name]</li>
            </ul>

            <form method="post">
                <input type="hidden" name="id" value="[Record ID]">
                <div class="mb-3">
                    <label for="grade" class="form-label">Grade</label>
                    <input type="number" step="0.01" class="form-control" id="grade" name="grade" value="[Grade]">
                </div>
                <a href="attach-subject.php?id=" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="assign_grade" class="btn btn-primary">Assign Grade to Subject</button>
            </form>
        </div>
    </div>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // Flush output buffer ?>
