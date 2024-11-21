<?php
ob_start(); 
$title = "Assign Grade"; // Page title for the header
require_once '../../functions.php';
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard(); // Ensure only authorized users can access the page


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize feedback messages
$error_message = '';
$success_message = '';

// Determine the record ID based on POST or GET request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $record_id = intval($_POST['id']);
} elseif (isset($_GET['id'])) {
    $record_id = intval($_GET['id']);
} else {
    header("Location: attach-subject.php"); // Redirect if no valid ID is provided
    exit;
}

if (!empty($record_id)) {
    // Establish a database connection and validate it
    $connection = getDatabaseConnection();

    if (!$connection || $connection->connect_error) {
        die("Failed to connect to the database: " . $connection->connect_error);
    }

    // Retrieve relevant data about the student, subject, and grade
    $query = "SELECT students.id AS student_id, students.first_name, students.last_name, 
                     subjects.subject_code, subjects.subject_name, students_subjects.grade 
              FROM students_subjects 
              JOIN students ON students_subjects.student_id = students.id 
              JOIN subjects ON students_subjects.subject_id = subjects.id 
              WHERE students_subjects.id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $record_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();

        // Handle form submission for grade assignment
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_grade'])) {
            $grade = $_POST['grade'];

            // Validate the grade input
            if (empty($grade)) {
                $error_message = "Grade is required.";
            } elseif (!is_numeric($grade) || $grade < 0 || $grade > 100) {
                $error_message = "Please enter a valid grade between 0 and 100.";
            } else {
                $grade = floatval($grade);

                // Update the database with the assigned grade
                $update_query = "UPDATE students_subjects SET grade = ? WHERE id = ?";
                $update_stmt = $connection->prepare($update_query);
                $update_stmt->bind_param('di', $grade, $record_id);

                if ($update_stmt->execute()) {
                    // Redirect to the attach page after successful update
                    $success_message = "Grade assigned successfully.";
                    header("Location: attach-subject.php?id=" . htmlspecialchars($record['student_id']));
                    exit;
                } else {
                    $error_message = "Failed to assign grade. Please try again.";
                }
            }
        }
    } else {
        header("Location: attach-subject.php"); // Redirect if no record found
        exit;
    }
} else {
    header("Location: attach-subject.php"); // Redirect if no valid ID is provided
    exit;
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Assign Grade to Subjects</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id'] ?? ''); ?>">Attach Subject to Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Grade</li>
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
                <h5>Student and Subject Details</h5>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($record['student_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($record['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($record['subject_name']); ?></li>
                </ul>

                <form method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($record_id); ?>">
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade</label>
                        <input type="number" step="0.01" class="form-control" id="grade" name="grade" value="<?php echo htmlspecialchars($record['grade']); ?>">
                    </div>
                    <a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id']); ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="assign_grade" class="btn btn-primary">Assign Grade</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main> 

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // Send the output buffer contents ?>


