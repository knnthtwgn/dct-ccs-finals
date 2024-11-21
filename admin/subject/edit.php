<?php
ob_start(); // Start output buffering
require_once(__DIR__ . '/../../functions.php');
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard();

// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    $subject_id = $_GET['id'];

    $connection = getDatabaseConnection();
    $query = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $subject = $result->fetch_assoc();
        $subject_code = $subject['subject_code'];
        $subject_name = $subject['subject_name'];
    } else {
        // Redirect if subject not found
        header("Location: ../subject/add.php");
        exit;
    }
} else {
    // Redirect if 'id' not set
    header("Location: ../subject/add.php");
    exit;
}

$error_message = '';
$success_message = '';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_subject'])) {
    $subject_name = trim($_POST['subject_name']); // Only subject_name is editable

    $errors = [];
    if (empty($subject_name)) {
        $errors[] = "Subject Name is required.";
    }

    if (empty($errors)) {
        $query = "UPDATE subjects SET subject_name = ? WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('si', $subject_name, $subject_id);

        if ($stmt->execute()) {
            // Redirect after successful update
            header("Cache-Control: no-cache, no-store, must-revalidate"); // Clear cache to avoid issues
            header("Location: ../subject/add.php");
            exit;
        } else {
            $error_message = renderAlert(["Error updating subject: " . $stmt->error], 'danger');
        }
    } else {
        $error_message = renderAlert($errors, 'danger');
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Edit Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../subject/add.php">Subjects</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
        </ol>
    </nav>

    <!-- Display error or success messages -->
    <?php if (!empty($error_message)): ?>
        <?php echo $error_message; ?>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <?php echo $success_message; ?>
    <?php endif; ?>

    <!-- Edit Subject Form -->
    <form method="post" action="">
        <div class="form-floating mb-3">
            <!-- Subject Code is disabled -->
            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Subject Code" value="<?php echo htmlspecialchars($subject_code); ?>" disabled>
            <label for="subject_code">Subject Code</label>
        </div>
        <div class="form-floating mb-3">
            <!-- Subject Name is editable -->
            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Subject Name" value="<?php echo htmlspecialchars($subject_name); ?>" required>
            <label for="subject_name">Subject Name</label>
        </div>
        <div class="mb-3 d-flex gap-2">
            <!-- Submit Button -->
            <button type="submit" name="update_subject" class="btn btn-primary flex-grow-1">Update Subject</button>
            <!-- Cancel Button -->
            <a href="../subject/add.php" class="btn btn-secondary flex-grow-1">Cancel</a>
        </div>
    </form>
</main>

<?php require_once '../partials/footer.php'; ?>
<?php ob_end_flush(); // End output buffering ?>
