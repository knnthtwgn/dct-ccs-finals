<?php
ob_start(); // Start output buffering
$title = "Edit Student"; // Set the page title
require_once '../../functions.php'; // Include necessary functions
require_once '../partials/header.php'; // Include header
require_once '../partials/side-bar.php'; // Include sidebar
guard(); // Check if the user is authenticated

// Error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = '';
$success_message = '';

// Check if student ID is provided
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']); // Convert to integer for safety
    $student_data = getSelectedStudentData($student_id); // Fetch student data from DB
    if (!$student_data) {
        $error_message = "Student not found."; // Error if no data is found
    }
} else {
    $error_message = "No student selected."; // Error if no student ID is provided
}

// Handle form submission to update student data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    if (isset($student_id)) {
        $updated_data = [
            'first_name' => trim($_POST['first_name'] ?? ''), // Get first name from POST
            'last_name' => trim($_POST['last_name'] ?? '') // Get last name from POST
        ];

        // Validation for required fields
        if (empty($updated_data['first_name']) || empty($updated_data['last_name'])) {
            $error_message = "First Name and Last Name are required."; // Error if fields are empty
        } else {
            // Proceed with database update
            $connection = getDatabaseConnection(); // Connect to the database
            $query = "UPDATE students SET first_name = ?, last_name = ? WHERE id = ?"; // Update query
            $stmt = $connection->prepare($query); // Prepare the statement
            $stmt->bind_param('ssi', $updated_data['first_name'], $updated_data['last_name'], $student_id); // Bind parameters

            if ($stmt->execute()) {
                $success_message = "Student updated successfully."; // Success message
                // Redirect with success query parameter
                header("Location: ../student/register.php?update=success"); 
                exit();
            } else {
                $error_message = "Failed to update student: " . $stmt->error; // Error if update fails
            }
            $stmt->close(); // Close statement
            $connection->close(); // Close connection
        }
    } else {
        $error_message = "Invalid student ID."; // Error if student ID is invalid
    }
}
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

    <!-- Display error or success messages -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display the form to update student information if student data is available -->
    <?php if (isset($student_data)): ?>
        <form method="post">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student ID</label>
                <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_data['student_id']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student_data['first_name']); ?>" placeholder="Enter First Name">
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student_data['last_name']); ?>" placeholder="Enter Last Name">
            </div>

            <div class="mb-3">
                <button type="submit" name="update_student" class="btn btn-primary w-100">Update Student</button>
            </div>
        </form>
    <?php endif; ?>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // End output buffering ?>
