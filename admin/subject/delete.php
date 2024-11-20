<?php
ob_start(); // Start output buffering
$title = "Delete Subject"; // Set the title for the page

ini_set('display_errors', 1); // Enable displaying of errors
ini_set('display_startup_errors', 1); // Show startup errors
error_reporting(E_ALL); // Set error reporting level to all errors

require_once '../../functions.php'; // Include functions file for reusable code
require_once '../partials/header.php'; // Include header layout
require_once '../partials/side-bar.php'; // Include sidebar layout

guard(); // Ensure the user is authenticated

// Initialize message variables
$error_message = '';
$success_message = '';

// Check if an ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: /admin/subject/add.php"); // Redirect if no subject ID is provided
    exit();
}

$subject_id = intval($_GET['id']); // Sanitize and convert the subject ID to an integer

// Connect to the database and fetch the subject details
$connection = getDatabaseConnection(); // Establish a connection to the database
$query = "SELECT * FROM subjects WHERE id = ?"; // SQL query to fetch subject details
$stmt = $connection->prepare($query); // Prepare the query
$stmt->bind_param('i', $subject_id); // Bind the ID parameter
$stmt->execute(); // Execute the query
$result = $stmt->get_result(); // Get the result set
$subject = $result->fetch_assoc(); // Fetch the subject as an associative array

// If no subject found, display error message
if (!$subject) {
    $error_message = "Subject not found."; // Set error message
}

// Handle the delete request when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_subject'])) {
    // Prepare and execute the delete query
    $delete_query = "DELETE FROM subjects WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_query); // Prepare the delete statement
    $delete_stmt->bind_param('i', $subject_id); // Bind the subject ID for deletion

    if ($delete_stmt->execute()) {
        $success_message = "Subject deleted successfully."; // Success message if delete is successful
        header("Location: /admin/subject/add.php"); // Redirect to subject page after deletion
        exit(); // Stop further script execution
    } else {
        $error_message = "Failed to delete the subject: " . $connection->error; // Error message if delete fails
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Delete Subject</h1>

    <!-- Display error message if any -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display success message if any -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display subject details if found -->
    <?php if (!empty($subject)): ?>
        <nav class="breadcrumb">
            <a class="breadcrumb-item" href="/admin/dashboard.php">Dashboard</a>
            <a class="breadcrumb-item" href="/admin/subject/add.php">Add Subject</a>
            <span class="breadcrumb-item active">Delete Subject</span>
        </nav>

        <div class="card mt-4">
            <div class="card-body">
                <p>Are you sure you want to delete the following subject record?</p>
                <ul>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subject['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                </ul>
                <!-- Form to confirm deletion -->
                <form method="post">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='/admin/subject/add.php'">Cancel</button>
                    <button type="submit" name="delete_subject" class="btn btn-primary">Delete Subject Record</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php
require_once '../partials/footer.php'; // Include footer
ob_end_flush(); // End output buffering and send the output
?>
