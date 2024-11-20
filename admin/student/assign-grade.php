<?php
ob_start(); // Start output buffering
$title = "Assign Grade"; // Set the title
require_once '../../functions.php';
require_once '../partials/header.php';
require_once '../partials/side-bar.php';

// Placeholder variables for demonstration
$error_message = '';
$success_message = '';
$record_id = null;
$student_info = [
    'student_id' => '12345',
    'student_name' => 'John Doe',
    'subject_code' => 'MATH101',
    'subject_name' => 'Basic Mathematics',
    'grade' => 85
];

// Placeholder function to simulate data validation
function validate_grade($grade) {
    if (empty($grade)) {
        return "Grade cannot be blank.";
    }
    if (!is_numeric($grade) || $grade < 0 || $grade > 100) {
        return "Grade must be a numeric value between 0 and 100.";
    }
    return '';
}

// Placeholder function to simulate grade assignment
function assign_grade($record_id, $grade) {
    // Simulate success without database connection
    return true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_grade'])) {
    $record_id = $_POST['id'] ?? null;
    $grade = $_POST['grade'] ?? '';

    // Validate grade
    $error_message = validate_grade($grade);

    if (empty($error_message)) {
        // Simulate grade assignment success
        if (assign_grade($record_id, $grade)) {
            $success_message = "Grade successfully assigned.";
            // Uncomment this line to redirect after successful assignment
            // header("Location: attach-subject.php?id={$student_info['student_id']}");
        } else {
            $error_message = "Failed to assign the grade. Please try again.";
        }
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Assign Grade to Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo htmlspecialchars($student_info['student_id']); ?>">Attach Subject to Student</a></li>
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
                <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student_info['student_id']); ?></li>
                <li><strong>Name:</strong> <?php echo htmlspecialchars($student_info['student_name']); ?></li>
                <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($student_info['subject_code']); ?></li>
                <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($student_info['subject_name']); ?></li>
            </ul>

            <form method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($record_id ?? ''); ?>">
                <div class="mb-3">
                    <label for="grade" class="form-label">Grade</label>
                    <input type="number" step="0.01" class="form-control" id="grade" name="grade" value="<?php echo htmlspecialchars($student_info['grade']); ?>">
                </div>
                <a href="attach-subject.php?id=<?php echo htmlspecialchars($student_info['student_id']); ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="assign_grade" class="btn btn-primary">Assign Grade to Subject</button>
            </form>
        </div>
    </div>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // Flush output buffer ?>
