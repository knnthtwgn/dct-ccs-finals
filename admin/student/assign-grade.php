<?php
ob_start(); // Start output buffering
$title = "Assign Grade"; // Set the title
require_once '../../functions.php'; // Contains the database connection function
require_once '../partials/header.php';
require_once '../partials/side-bar.php';

$error_message = '';
$success_message = '';
$record_id = $_GET['id'] ?? null;
$student_info = null;

// Database connection
$conn = getDatabaseConnection(); 

// Fetch student and subject information
if ($record_id) {
    $stmt = $conn->prepare("SELECT student_id, student_name, subject_code, subject_name, grade FROM student_subject_grades WHERE id = ?");
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student_info = $result->fetch_assoc();
    } else {
        $error_message = "Record not found.";
    }
    $stmt->close();
} else {
    $error_message = "No record ID provided.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_grade'])) {
    $grade = $_POST['grade'] ?? '';

    // Validate grade
    if (empty($grade)) {
        $error_message = "Grade cannot be blank.";
    } elseif (!is_numeric($grade) || $grade < 0 || $grade > 100) {
        $error_message = "Grade must be a numeric value between 0 and 100.";
    }

    if (empty($error_message)) {
        // Update the grade in the database
        $stmt = $conn->prepare("UPDATE student_subject_grades SET grade = ? WHERE id = ?");
        $stmt->bind_param("di", $grade, $record_id);
        if ($stmt->execute()) {
            $success_message = "Grade successfully assigned.";
            $student_info['grade'] = $grade; // Update local variable
        } else {
            $error_message = "Failed to assign the grade. Please try again.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Assign Grade to Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo htmlspecialchars($student_info['student_id'] ?? ''); ?>">Attach Subject to Student</a></li>
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
            <?php if ($student_info): ?>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student_info['student_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($student_info['student_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($student_info['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($student_info['subject_name']); ?></li>
                </ul>

                <form method="post">
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade</label>
                        <input type="number" step="0.01" class="form-control" id="grade" name="grade" value="<?php echo htmlspecialchars($student_info['grade']); ?>">
                    </div>
                    <a href="attach-subject.php?id=<?php echo htmlspecialchars($student_info['student_id']); ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="assign_grade" class="btn btn-primary">Assign Grade to Subject</button>
                </form>
            <?php else: ?>
                <p>No student information available.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // Flush output buffer ?>
