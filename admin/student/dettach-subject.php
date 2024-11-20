<?php
ob_start(); // Start output buffering to control when content is sent to the browser
$title = "Detach a Subject"; // Set the page title
require_once '../../functions.php';
require_once '../partials/header.php'; // Include the header file
require_once '../partials/side-bar.php'; // Include the sidebar file
guard(); // Ensure the user is authenticated

// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define variables for error and success messages
$error_message = '';
$success_message = '';

// Check if the subject-student record ID is provided
if (isset($_GET['id'])) {
    $record_id = intval($_GET['id']); // Sanitize the record ID input

    // Connect to the database
    $connection = getDatabaseConnection();

    // Verify database connection
    if (!$connection || $connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

    // Fetch the student and subject details using the record ID
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

    // Check if a matching record is found
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc(); // Fetch the record details

        // Handle form submission to detach the subject
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detach_subject'])) {
            // Prepare the query to delete the record
            $delete_query = "DELETE FROM students_subjects WHERE id = ?";
            $delete_stmt = $connection->prepare($delete_query);
            $delete_stmt->bind_param('i', $record_id);

            // Execute the deletion and handle the result
            if ($delete_stmt->execute()) {
                // Redirect to the attach page with the associated student ID
                header("Location: attach-subject.php?id=" . htmlspecialchars($record['student_id']));
                exit;
            } else {
                $error_message = "Failed to detach the subject. Please try again.";
            }
        }
    } else {
        // Redirect to the attach-subject page if no record is found
        header("Location: attach-subject.php");
        exit;
    }
} else {
    // Redirect to the attach-subject page if no record ID is provided
    header("Location: attach-subject.php");
    exit;
}

?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Detach a Subject</h1>

    <!-- Breadcrumb navigation for better user experience -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id'] ?? ''); ?>">Attach Subject to Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detach Subject from Student</li>
        </ol>
    </nav>

    <!-- Display error or success messages -->
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
                <p>Are you sure you want to detach this subject from this student's record?</p>
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

<?php require_once '../partials/footer.php'; // Include the footer ?>
<?php ob_end_flush(); // End output buffering ?>
