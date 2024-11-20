<?php
// Authenticate the user with given email and password.
function authenticateUser($email, $password) {
    $connection = getDatabaseConnection();
    $password_hash = md5($password);

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
        return $user;
    }

    return false; // Return false if user not found.
}

// Start session if not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Establish and return a database connection.
function getDatabaseConnection() {
    $db_config = [
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'dct-ccs-finals'
    ];

    // Create a new database connection.
    $connection = new mysqli(
        $db_config['host'], 
        $db_config['user'], 
        $db_config['password'], 
        $db_config['database']
    );

    // Check if connection was successful and log error if not.
    if ($connection->connect_error) {
        error_log("Connection failed: " . $connection->connect_error, 3, '/var/log/db_errors.log');
        return null;
    }

    return $connection; // Return the connection object.
}

// Render alert messages as HTML.
function displayAlertMessages($messages, $type = 'danger') {
    if (empty($messages)) {
        return ''; // Return empty string if no messages.
    }
    
    if (!is_array($messages)) {
        $messages = [$messages]; // Convert to array if not already.
    }

    // Generate HTML for alert messages.
    $html = '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
    $html .= '<ul>';
    foreach ($messages as $message) {
        $html .= '<li>' . htmlspecialchars($message) . '</li>';
    }
    $html .= '</ul>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    $html .= '</div>';

    return $html; // Return the generated HTML.
}

?>
