<?php
ob_start(); // Buffer output to avoid header errors
$title = "Delete a Student";
require_once '../../functions.php';
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Error and success messages
$error_message = '';
$success_message = '';

if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']); // Get student ID from the URL

    // Retrieve student data
    $student_data = getSelectedStudentData($student_id);
    if (!$student_data) {
        $error_message = "Student not found.";
    }
} else {
    $error_message = "No student selected to delete.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    if (isset($student_id)) {
        // Delete student record
        $connection = getDatabaseConnection();
        $query = "DELETE FROM students WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('i', $student_id);

        if ($stmt->execute()) {
            header("Location: ../student/register.php"); // Redirect after successful deletion
            exit();
        } else {
            $error_message = "Failed to delete student record. Error: " . $stmt->error;
        }

        $stmt->close();
        $connection->close();
    } else {
        $error_message = "Invalid student ID.";
    }
}

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

    <!-- Display messages -->
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

    <!-- Confirm deletion -->
    <?php if (isset($student_data)): ?>
        <div class="card">
            <div class="card-body">
                <p>Are you sure you want to delete this student?</p>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student_data['student_id']); ?></li>
                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($student_data['first_name']); ?></li>
                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student_data['last_name']); ?></li>
                </ul>
                <form method="post" action="">
                    <button type="submit" name="delete_student" class="btn btn-danger">Delete Student</button>
                    <a href="../student/register.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); ?>