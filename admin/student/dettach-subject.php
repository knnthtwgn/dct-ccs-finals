<?php
ob_start(); // Start output buffering
$title = "Detach a Subject"; // Set the title
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard(); // Ensure the user is authenticated

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$error_message = '';
$success_message = '';

function fetchRecord($record_id) {
    // Fetch student and subject data based on the record ID
    $connection = getDatabaseConnection();

    if (!$connection || $connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

    $query = "SELECT students.id AS student_id, students.first_name, students.last_name, 
                     subjects.subject_code, subjects.subject_name 
              FROM students_subjects 
              JOIN students ON students_subjects.student_id = students.id 
              JOIN subjects ON students_subjects.subject_id = subjects.id 
              WHERE students_subjects.id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $record_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function detachSubject($record_id, $student_id) {
    $connection = getDatabaseConnection();

    $delete_query = "DELETE FROM students_subjects WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_query);
    $delete_stmt->bind_param('i', $record_id);

    if ($delete_stmt->execute()) {
        // Redirect directly to the attach page with the correct student ID
        header("Location: attach-subject.php?id=" . htmlspecialchars($student_id));
        exit;
    } else {
        return "Failed to detach the subject. Please try again.";
    }
}

if (isset($_GET['id'])) {
    $record_id = intval($_GET['id']);
    $record = fetchRecord($record_id);

    if ($record) {
        // Handle form submission for detaching the subject
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detach_subject'])) {
            $error_message = detachSubject($record_id, $record['student_id']);
        }
    } else {
        // Redirect to the attach-subject page if no record is found
        header("Location: attach-subject.php");
        exit;
    }
} else {
    // Redirect to the attach-subject page if no ID is provided
    header("Location: attach-subject.php");
    exit;
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Detach a Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id'] ?? ''); ?>">Attach Subject to Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detach Subject from Student</li>
        </ol>
    </nav>

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

    <?php if (isset($record)): ?>
        <div class="card">
            <div class="card-body">
                <p>Are you sure you want to detach this subject from this student record?</p>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($record['student_id']); ?></li>
                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($record['first_name']); ?></li>
                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($record['last_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($record['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($record['subject_name']); ?></li>
                </ul>

                <form method="post">
                    <a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id']); ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="detach_subject" class="btn btn-danger">Detach Subject from Student</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // Flush output buffer ?>
