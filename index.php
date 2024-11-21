<?php
require_once 'functions.php';

// Start the session to manage dismissible alerts

// Initialize validation errors and success message
$validation_errors = $_SESSION['validation_errors'] ?? [];
$success_message = $_SESSION['success_message'] ?? '';

// Clear validation errors and success message from session after reading them
unset($_SESSION['validation_errors'], $_SESSION['success_message']);

// Clear messages if the dismiss form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dismiss_alert'])) {
    $validation_errors = [];
    $success_message = '';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Reset the validation errors array
    $validation_errors = [];

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate email
    if (empty($email)) {
        $validation_errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validation_errors[] = "Invalid email format.";
    }

    // Validate password
    if (empty($password)) {
        $validation_errors[] = "Password is required.";
    }

    // Attempt login if no validation errors
    if (empty($validation_errors)) {
        $user = authenticateUser($email, $password);

        if ($user) {
            // Redirect authenticated user to the dashboard
            $_SESSION['success_message'] = "Login successful!";
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $validation_errors[] = "Invalid email or password.";
        }
    }

    // Store errors in session for display
    $_SESSION['validation_errors'] = $validation_errors;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Login</title>
</head>

<body class="bg-secondary-subtle">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-md-4 col-lg-3">
            
            <!-- Display Validation Errors -->
            <?php if (!empty($validation_errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach ($validation_errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="dismiss_alert" class="btn-close" aria-label="Close"></button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Display Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div><?php echo htmlspecialchars($success_message); ?></div>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="dismiss_alert" class="btn-close" aria-label="Close"></button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4 fw-normal text-center">Login</h1>
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email" name="email" placeholder="user1@example.com" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="password">Password</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
