<?php
$title = "Register a New Student"; // Set the title for the page
require_once '../../functions.php';
require_once '../partials/header.php'; 
require_once '../partials/side-bar.php'; 
guard();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize message variables for success or error
$error_message = '';
$success_message = '';

// Handle form submission when method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input data for student details
    $student_data = [
        'student_id' => generateValidStudentId(trim($_POST['student_id'] ?? '')), // Ensure student ID is valid
        'first_name' => trim($_POST['first_name'] ?? ''), // Clean first name input
        'last_name' => trim($_POST['last_name'] ?? '') // Clean last name input
    ];

    $errors = validateStudentData($student_data);

    // Proceed if no validation errors
    if (empty($errors)) {
        // Check if the student ID is already in use
        $duplicateError = checkDuplicateStudentData($student_data);

        // Handle duplicate student ID error
        if (!empty($duplicateError)) {
            $error_message = renderAlert([$duplicateError], 'danger'); // Show duplicate error
        } else {
            // Proceed with database insertion if no duplicates found
            $connection = getDatabaseConnection(); // Establish database connection

            // Generate a unique student ID for the database entry
            $student_id_unique = generateUniqueIdForStudents();

            // Prepare the SQL query for inserting student data
            $query = "INSERT INTO students (id, student_id, first_name, last_name) VALUES (?, ?, ?, ?)";
            $stmt = $connection->prepare($query); // Prepare the query statement

            if ($stmt) {
                // Bind parameters to the query
                $stmt->bind_param('isss', $student_id_unique, $student_data['student_id'], $student_data['first_name'], $student_data['last_name']);
                // Execute the query and check for success
                if ($stmt->execute()) {
                    $success_message = renderAlert(["Student successfully registered!"], 'success'); // Show success message
                } else {
                    // Handle query execution failure
                    $error_message = renderAlert(["Failed to register student. Error: " . $stmt->error], 'danger');
                }
                $stmt->close(); // Close the prepared statement
            } else {
                // Handle query preparation failure
                $error_message = renderAlert(["Statement preparation failed: " . $connection->error], 'danger');
            }

            $connection->close(); // Close the database connection
        }
    } else {
        // Display validation errors
        $error_message = renderAlert($errors, 'danger');
    }
}

?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    
    <h1 class="h2">Register a New Student</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Register Student</li>
        </ol>
    </nav>

    <!-- Display error or success messages -->
    <?php if (!empty($error_message)): ?>
        <?php echo $error_message; ?>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <?php echo $success_message; ?>
    <?php endif; ?>

    <!-- Student registration form -->
    <form method="post" action="">
        <div class="mb-3">
            <label for="student_id" class="form-label">Student ID</label>
            <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter Student ID" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100">Add Student</button>
        </div>
    </form>

    <hr>

    <h2 class="h4">Student List</h2>
    <!-- Table displaying list of registered students -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Student ID</th>
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $connection = getDatabaseConnection(); // Establish connection to the database
            $query = "SELECT * FROM students"; // Query to fetch all students
            $result = $connection->query($query); // Execute the query

            // Loop through the fetched students and display in the table
            while ($student = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                    <td>
                        <!-- Links for editing, deleting, or attaching a subject to the student -->
                        <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                        <a href="attach-subject.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-warning">Attach Subject</a>
                    </td>
                </tr>
            <?php endwhile; ?>

            <?php
            $connection->close(); // Close the database connection
            ?>
        </tbody>
    </table>
</main>

<?php require_once '../partials/footer.php'; // Include the footer for the page ?>
