<?php
require_once(__DIR__ .'/../../functions.php');
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
guard();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subject'])) {
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
        $duplicate_error = checkDuplicateSubject($subject_code, $subject_name);

        if (!empty($duplicate_error)) {
            $error_message = renderAlert([$duplicate_error], 'danger');
        } else {
            $connection = getDatabaseConnection();
            $query = "INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param('ss', $subject_code, $subject_name);

            if ($stmt->execute()) {
                $success_message = renderAlert(["Subject added successfully!"], 'success');
                $subject_code = '';
                $subject_name = '';
            } else {
                $error_message = renderAlert(["Error adding subject. Please try again."], 'danger');
            }
        }
    } else {
        $error_message = renderAlert($errors, 'danger');
    }
} // <-- This closing brace was missing

$connection = getDatabaseConnection();
$query = "SELECT * FROM subjects";
$result = $connection->query($query);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Add a New Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add a New Subject</li>
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
            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Subject Code" value="<?php echo htmlspecialchars($subject_code ?? ''); ?>">
            <label for="subject_code">Subject Code</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Subject Name" value="<?php echo htmlspecialchars($subject_name ?? ''); ?>">
            <label for="subject_name">Subject Name</label>
        </div>
        <div class="mb-3">
            <button type="submit" name="add_subject" class="btn btn-primary w-100">Add Subject</button>
        </div>
    </form>

    <h3 class="mt-5">Subject List</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Option</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php require_once '../partials/footer.php'; ?>
