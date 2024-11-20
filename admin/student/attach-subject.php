<?php
$title = "Attach a Subject"; // Set the title
require_once '../partials/header.php';
require_once '../partials/side-bar.php';
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

    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
        Error message placeholder.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
        Success message placeholder.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="card">
        <div class="card-body">
            <p><strong>Selected Student Information:</strong></p>
            <ul>
                <li><strong>Student ID:</strong> [Student ID]</li>
                <li><strong>Name:</strong> [First Name] [Last Name]</li>
            </ul>

            <form method="post" action="">
                <p><strong>Select Subjects to Attach:</strong></p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="subjects[]" value="1" id="subject_1">
                    <label class="form-check-label" for="subject_1">[Subject Code] - [Subject Name]</label>
                </div>
                <button type="submit" name="attach_subjects" class="btn btn-primary mt-3">Attach Subjects</button>
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
            <tr>
                <td>[Subject Code]</td>
                <td>[Subject Name]</td>
                <td>[Grade]</td>
                <td>
                    <button class="btn btn-danger btn-sm">Detach</button>
                    <button class="btn btn-success btn-sm">Assign Grade</button>
                </td>
            </tr>
        </tbody>
    </table>
</main>

<?php require_once '../partials/footer.php'; ?>
