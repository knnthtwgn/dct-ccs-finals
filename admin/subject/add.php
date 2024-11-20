<?php
require_once(_DIR_ . '/../../functions.php'); // Import functions
require_once '../partials/header.php'; // Import header
require_once '../partials/side-bar.php'; // Import sidebar
guardSession(); // Ensure the user is authenticated

$errorAlert = ''; // Initialize error message
$successAlert = ''; // Initialize success message

// Function to check for duplicate subject data or subject name in the database
function isSubjectDuplicate($subjectCode, $subjectName) {
    $dbConnection = databaseConnect(); // Get database connection
    
    // Check for duplicate subject code
    $query = "SELECT * FROM subjects WHERE subject_code = ?";
    $stmt = $dbConnection->prepare($query);
    $stmt->bind_param('s', $subjectCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return "Subject code already exists. Please choose another."; // Duplicate code found
    }
    
    // Check for duplicate subject name
    $query = "SELECT * FROM subjects WHERE subject_name = ?";
    $stmt = $dbConnection->prepare($query);
    $stmt->bind_param('s', $subjectName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return "Subject name already exists. Please choose another."; // Duplicate name found
    }
    
    return ''; // No duplicates found
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subject'])) {
    $subjectCode = trim($_POST['subject_code']); // Trim subject code
    $subjectName = trim($_POST['subject_name']); // Trim subject name

    $formErrors = []; // Initialize errors array
    if (empty($subjectCode)) {
        $formErrors[] = "Subject Code is required."; // Subject code validation
    } elseif (strlen($subjectCode) > 4) {
        $formErrors[] = "Subject Code cannot be longer than 4 characters."; // Length validation
    }
    if (empty($subjectName)) {
        $formErrors[] = "Subject Name is required."; // Subject name validation
    }

    if (empty($formErrors)) { // Proceed if no errors
        $duplicateCheck = isSubjectDuplicate($subjectCode, $subjectName); // Check for duplicates

        if (!empty($duplicateCheck)) {
            $errorAlert = showAlert([$duplicateCheck], 'danger'); // Show duplicate error
        } else {
            $dbConnection = databaseConnect(); // Get database connection
            $query = "INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)"; // Insert subject
            $stmt = $dbConnection->prepare($query);
            $stmt->bind_param('ss', $subjectCode, $subjectName);

            if ($stmt->execute()) {
                $successAlert = showAlert(["Subject added successfully!"], 'success'); // Success message
                $subjectCode = ''; // Reset subject code
                $subjectName = ''; // Reset subject name
            } else {
                $errorAlert = showAlert(["Error adding subject. Please try again."], 'danger'); // Show error
            }
        }
    } else {
        $errorAlert = showAlert($formErrors, 'danger'); // Show validation errors
    }
}

$dbConnection = databaseConnect(); // Get database connection
$query = "SELECT * FROM subjects"; // Select all subjects
$result = $dbConnection->query($query); // Execute query
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Student Management System</title>
</head>

<body class="bg-secondary-subtle">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-3">
            
            <!-- Display Validation Errors -->
            <?php if (!empty($errorAlert)): ?>
                <?php echo $errorAlert; ?> <!-- Display error message -->
            <?php endif; ?>

            <!-- Display Success Message -->
            <?php if (!empty($successAlert)): ?>
                <?php echo $successAlert; ?> <!-- Display success message -->
            <?php endif; ?>
            

            <!-- Login Form -->
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4 fw-normal">Login</h1>
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email" name="email" placeholder="user1@example.com" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            <label for="email">Email address</label> <!-- Email field -->
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="password">Password</label> <!-- Password field -->
                        </div>
                        <div class="form-floating mb-3">
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button> <!-- Submit button -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Add a New Subject</h1>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add a New Subject</li>
        </ol>
    </nav>

    <?php if (!empty($errorAlert)): ?>
        <?php echo $errorAlert; ?> <!-- Display error message -->
    <?php endif; ?>

    <?php if (!empty($successAlert)): ?>
        <?php echo $successAlert; ?> <!-- Display success message -->
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Subject Code" value="<?php echo htmlspecialchars($subjectCode ?? ''); ?>">
            <label for="subject_code">Subject Code</label> <!-- Subject Code field -->
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Subject Name" value="<?php echo htmlspecialchars($subjectName ?? ''); ?>">
            <label for="subject_name">Subject Name</label> <!-- Subject Name field -->
        </div>
        <div class="mb-3">
            <button type="submit" name="add_subject" class="btn btn-primary w-100">Add Subject</button> <!-- Submit button -->
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
                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td> <!-- Display subject code -->
                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td> <!-- Display subject name -->
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Edit</a> <!-- Edit button -->
                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a> <!-- Delete button -->
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php require_once '../partials/footer.php'; ?> <!-- Footer -->
