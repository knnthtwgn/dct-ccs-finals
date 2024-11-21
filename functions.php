<?php

// Start session if not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Establish and return a database connection.
 *
 * @return mysqli|null Database connection object, or null if connection fails.
 */
function getDatabaseConnection() {
    $DatabaseConfig = [
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'dct-ccs-finals'
    ];

    // Create a new database connection.
    $connection = new mysqli(
        $DatabaseConfig['host'], 
        $DatabaseConfig['user'], 
        $DatabaseConfig['password'], 
        $DatabaseConfig['database']
    );

    // Check if connection was successful and log error if not.
    if ($connection->connect_error) {
        error_log("Connection failed: " . $connection->connect_error, 3, '/var/log/db_errors.log');
        return null; // Return null if connection failed.
    }

    return $connection; // Return the connection object.
}

/**
 * Authenticate the user with given email and password.
 *
 * @param string $email User's email address.
 * @param string $password User's plain-text password.
 * @return array|bool User data if authentication is successful, false otherwise.
 */
function authenticateUser($email, $password) {
    $connection = getDatabaseConnection(); // Get a database connection.
    if (!$connection) {
        return false; // Return false if connection failed.
    }

    $password_hash = md5($password); // Hash the password using MD5 (note: not secure, consider using password_hash()).

    // Prepare and execute the SQL query to fetch user data.
    $query = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $email, $password_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and store session data.
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        return $user; // Return user data if authentication is successful.
    }

    return false; // Return false if user not found.
}


function guard() {
    if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $baseURL = $protocol . $host . '/'; 

        header("Location: " . $baseURL);
        exit();
    }
}

// Function to check for duplicate subject data or subject name in the database
function checkDuplicateSubject($subject_code, $subject_name) {
    $connection = getDatabaseConnection();
    
    // Check for duplicate subject code
    $query = "SELECT * FROM subjects WHERE subject_code = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return "Subject code already exists. Please choose another."; // Return the error message for duplicate code
    }
    
    // Check for duplicate subject name
    $query = "SELECT * FROM subjects WHERE subject_name = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $subject_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return "Subject name already exists. Please choose another."; // Return the error message for duplicate name
    }
    
    return ''; // No duplicate found
}

function displayErrors($errors) {
    if (empty($errors)) {
        return '';
    }
    $html = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    $html .= '<strong>Validation Errors:</strong><ul>';
    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error) . '</li>';
    }
    $html .= '</ul>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    $html .= '</div>';
    return $html;
}

function renderAlert($messages, $type = 'danger') {
    if (empty($messages)) {
        return '';
    }
    // Ensure messages is an array
    if (!is_array($messages)) {
        $messages = [$messages];
    }

    $html = '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
    $html .= '<ul>';
    foreach ($messages as $message) {
        $html .= '<li>' . htmlspecialchars($message) . '</li>';
    }
    $html .= '</ul>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    $html .= '</div>';

    return $html;
}
function generateValidStudentId($original_id) {
    // Truncate to the first 4 characters
    return substr($original_id, 0, 4);
}

function validateStudentData($student_data) {
    $errors = [];
    if (empty($student_data['student_id'])) {
        $errors[] = "Student ID is required.";
    }
    if (empty($student_data['first_name'])) {
        $errors[] = "First Name is required.";
    }
    if (empty($student_data['last_name'])) {
        $errors[] = "Last Name is required.";
    }

    // Removed the var_dump debug
    return $errors;
}

function checkDuplicateStudentData($student_data) {
    $connection = getDatabaseConnection();
    $query = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $student_data['student_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return "Student ID already exists.";
    }

    // Removed the var_dump debug
    return '';
}

function generateUniqueIdForStudents() {
    $connection = getDatabaseConnection();

    // Find the maximum current ID and add 1 to it
    $query = "SELECT MAX(id) AS max_id FROM students";
    $result = $connection->query($query);
    $row = $result->fetch_assoc();
    $max_id = $row['max_id'];

    $connection->close();

    return $max_id + 1; // Generate the next unique ID
}
<<<<<<< Updated upstream
=======

>>>>>>> Stashed changes
function getSelectedStudentData($student_id) {
    $connection = getDatabaseConnection();
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    $stmt->close();
    $connection->close();

    return $student;
}
<<<<<<< Updated upstream
=======

>>>>>>> Stashed changes
?>
