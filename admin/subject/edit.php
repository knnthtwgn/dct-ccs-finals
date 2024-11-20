<?php
require_once(__DIR__ .'/../../functions.php');
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard();

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
        header("Location: ../subject/add.php"); // Redirect to the subjects list
        exit;
    }
} else {
    header("Location: ../subject/add.php"); // Redirect to the subjects list
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_subject'])) {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);

    $errors = [];
    if (empty($subject_code)) {
        $errors[] = "Subject Code is required.";
    } elseif (strlen($subject_code) > 4) {
        $errors[] = "Subject Code cannot be longer than 4 characters.";
    }
    if (empty($subject_name)) {
        $errors[] = "Subject Name is required.";
    }

    if (empty($errors)) {
        $query = "UPDATE subjects SET subject_code = ?, subject_name = ? WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('ssi', $subject_code, $subject_name, $subject_id);

        if ($stmt->execute()) {
            $success_message = renderAlert(["Subject updated successfully!"], 'success');
            header("Location: ../subject/add.php"); // Redirect to add.php after successful update
            exit;
        } else {
            $error_message = renderAlert(["Error updating subject. Please try again."], 'danger');
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

    <?php if (!empty($error_message)): ?>
        <?php echo $error_message; ?>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <?php echo $success_message; ?>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Subject Code" value="<?php echo htmlspecialchars($subject_code); ?>" required>
            <label for="subject_code">Subject Code</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Subject Name" value="<?php echo htmlspecialchars($subject_name); ?>" required>
            <label for="subject_name">Subject Name</label>
        </div>
        <div class="mb-3">
            <button type="submit" name="update_subject" class="btn btn-primary w-100">Update Subject</button>
        </div>
    </form>
</main>

<?php require_once '../partials/footer.php'; ?>
