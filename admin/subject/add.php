<?php
require_once(_DIR_ . '/../../helpers.php'); // Import functions
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
