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
?>
