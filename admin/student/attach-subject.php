<?php
$title = "Attach a Subject";
require_once '../../functions.php';
require_once '../partials/header.php'; 
require_once '../partials/side-bar.php';
guard(); // Ensure user is authenticated

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); 

// Initialize message variables for feedback
$error_message = '';
$success_message = '';

// Function to fetch available subjects
function getAvailableSubjects($connection, $student_id) {
    $query = "SELECT * FROM subjects WHERE id NOT IN (SELECT subject_id FROM students_subjects WHERE student_id = ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch attached subjects
function getAttachedSubjects($connection, $student_id) {
    $query = "SELECT subjects.subject_code, subjects.subject_name, students_subjects.grade, students_subjects.id
              FROM subjects 
              JOIN students_subjects ON subjects.id = students_subjects.subject_id 
              WHERE students_subjects.student_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle form submission to attach subjects
function handleAttachSubjects($connection, $student_id) {
    global $error_message, $success_message;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attach_subjects'])) {
        if (!empty($_POST['subjects'])) {
            $selected_subjects = $_POST['subjects'];
            foreach ($selected_subjects as $subject_id) {
                $query = "INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)";
                $stmt = $connection->prepare($query);
                $grade = 0.00; // Default grade
                $stmt->bind_param('iid', $student_id, $subject_id, $grade);
                $stmt->execute();
            }
            $success_message = "Subjects successfully attached to the student.";
        } else {
            $error_message = "Please select at least one subject to attach.";
        }
    }
}

// Ensure student ID is provided
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $student_data = getSelectedStudentData($student_id); // Fetch student data

    if (!$student_data) {
        $error_message = "Student not found.";
    } else {
        // Connect to the database
        $connection = getDatabaseConnection();
        if (!$connection || $connection->connect_error) {
            die("Database connection failed: " . $connection->connect_error);
        }

        handleAttachSubjects($connection, $student_id);

        // Fetch available and attached subjects
        $available_subjects = getAvailableSubjects($connection, $student_id);
        $attached_subjects = getAttachedSubjects($connection, $student_id);
    }
} else {
    $error_message = "No student selected.";
}

?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Attach Subject to Student</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../student/register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attach Subject to Student</li>
        </ol>
    </nav>

    <!-- Error or Success message display -->
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

    <?php if (isset($student_data)): ?>
        <div class="card">
            <div class="card-body">
                <p><strong>Selected Student Information:</strong></p>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student_data['student_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']); ?></li>
                </ul>

                <!-- Form for attaching subjects -->
                <form method="post" action="">
                    <p><strong>Select Subjects to Attach:</strong></p>
                    <?php if ($available_subjects->num_rows > 0): ?>
                        <?php while ($subject = $available_subjects->fetch_assoc()): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="subjects[]" value="<?php echo $subject['id']; ?>" id="subject_<?php echo $subject['id']; ?>">
                                <label class="form-check-label" for="subject_<?php echo $subject['id']; ?>">
                                    <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                                </label>
                            </div>
                        <?php endwhile; ?>
                        <button type="submit" name="attach_subjects" class="btn btn-primary mt-3">Attach Subjects</button>
                    <?php else: ?>
                        <p>No subjects available to attach.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <hr>

        <h3>Attached Subject List</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Grade</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($attached_subjects->num_rows > 0): ?>
                    <?php while ($row = $attached_subjects->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><?php echo $row['grade'] > 0 ? number_format($row['grade'], 2) : '--.--'; ?></td>
                            <td>
                                <!-- Detach Subject -->
                                <form method="get" action="dettach-subject.php" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>"> 
                                    <button type="submit" class="btn btn-danger btn-sm">Detach</button>
                                </form>
                                <!-- Assign Grade -->
                                <form method="post" action="assign-grade.php" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Assign Grade</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No subjects attached.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

