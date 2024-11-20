<?php
ob_start(); // Initiate output buffering to prevent premature content rendering
$title = "Remove Subject from Student"; // Title for the page
require_once '../../functions.php';
require_once '../partials/header.php'; 
require_once '../partials/side-bar.php'; 
guard(); 

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize status messages
$error_message = '';
$success_message = '';

// Function to retrieve the record for the student and their subject details
function fetchRecord($record_id) {
    // Establish a database connection
    $connection = getDatabaseConnection();

    if (!$connection || $connection->connect_error) {
        die("Unable to connect to the database: " . $connection->connect_error);
    }

    // SQL query to fetch student and subject data
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

    // Return the record if found
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null; // No record found
    }
}

// Function to handle the subject detachment process
function detachSubject($record_id, $student_id) {
    // Establish a database connection
    $connection = getDatabaseConnection();

    // SQL query to remove the subject from the student's record
    $delete_query = "DELETE FROM students_subjects WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_query);
    $delete_stmt->bind_param('i', $record_id);

    // Execute the delete operation and check if it succeeded
    if ($delete_stmt->execute()) {
        // Redirect to the page for attaching a subject with the student ID
        header("Location: attach-subject.php?id=" . htmlspecialchars($student_id));
        exit; // Exit to prevent further code execution after redirection
    } else {
        return "Unable to remove the subject. Please try again."; // Error message if the delete fails
    }
}

// Check if the 'id' parameter is provided in the URL query string
if (isset($_GET['id'])) {
    $record_id = intval($_GET['id']);
    $record = fetchRecord($record_id); 

    // If the record is found, proceed with the detachment logic
    if ($record) {
       
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detach_subject'])) {
          
            $error_message = detachSubject($record_id, $record['student_id']);
        }
    } else {
        
        header("Location: attach-subject.php");
        exit;
    }
} else {
   
    header("Location: attach-subject.php");
    exit;
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Remove a Subject</h1>

    <!-- Breadcrumb navigation for easy page orientation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id'] ?? ''); ?>">Attach Subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Remove Subject</li>
        </ol>
    </nav>

    <!-- Display error or success messages based on the operation -->
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

    <!-- Display the student and subject details if a valid record is found -->
    <?php if (isset($record)): ?>
        <div class="card">
            <div class="card-body">
                <p>Are you sure you want to remove this subject from the student record?</p>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($record['student_id']); ?></li>
                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($record['first_name']); ?></li>
                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($record['last_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($record['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($record['subject_name']); ?></li>
                </ul>

                <!-- Form to confirm detachment -->
                <form method="post">
                    <a href="attach-subject.php?id=<?php echo htmlspecialchars($record['student_id']); ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="detach_subject" class="btn btn-danger">Remove Subject</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../partials/footer.php'; ?> <!-- Include footer -->
<?php ob_end_flush(); ?>
