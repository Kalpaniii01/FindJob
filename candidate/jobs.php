<?php
require_once '../includes/db.php'; // Include your database connection script
require_once '../includes/functions.php'; // Include your functions script

// Check if user is logged in and is a candidate user
if (!isLoggedIn() || !isCandidate()) {
    // Redirect to login page or appropriate access denied page
    header('Location: ../login.php');
    exit;
}

// Process search form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_jobs'])) {
    // Sanitize and get search parameters
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $min_salary = filter_input(INPUT_POST, 'min_salary', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $max_salary = filter_input(INPUT_POST, 'max_salary', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    // Fetch jobs based on search criteria
    $jobs = fetchJobs($title, $min_salary, $max_salary, $category);
} else {
    // Fetch all jobs if no search criteria provided
    $jobs = fetchJobs();
}

// Get user_id from session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Handle apply job action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_job') {
    if (isset($_POST['job_id'])) {
        $job_id = $_POST['job_id'];
        // Insert application into applications table
        $success = applyForJob($job_id, $user_id);

        if ($success) {
            // Redirect to same page after applying
            header('Location: jobs.php');
            exit;
        } else {
            // Handle application error
            echo '<script>alert("Failed to apply for the job.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs Listing</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css"> <!-- Adjust path as necessary -->
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="../assets/index/assert/favicon.ico" />

    <!-- Bootstrap Icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic"
        rel="stylesheet" type="text/css" />

    <!-- SimpleLightbox plugin CSS-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css"
        rel="stylesheet" />

    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../assets/index/css/styles.css" rel="stylesheet" />
</head>
<style>
    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
        font-family: 'Merriweather Sans', sans-serif; /* Use Merriweather Sans as the font */
        color: #000; /* Set default text color to black */
        padding-top: 0; /* Remove top padding to fix the navbar at the top */
        position: relative; /* Set body to relative positioning */
        margin: 0;
        min-height: 100vh; /* Ensure body fills the viewport height */
    }

    main {
        flex: 1;
        padding-bottom: 40px;
    }

    footer {
        background-color: #f8f9fa;
        padding: 20px 0;
        width: 100%;
    }

    .navbar {
        background-color: #fff; /* Set navbar background to white */
        position: fixed; /* Fix the navbar to the top */
        top: 0;
        width: 100%;
        z-index: 1030; /* Ensure the navbar stays above other content */
    }

    .navbar-dark .navbar-nav .nav-link {
        color: #000; /* Set navbar link text color to black */
    }

    .navbar-dark .navbar-nav .nav-link:hover {
        color: #ffa500; /* Set navbar link hover color to orange */
    }

    /* Increase card size and apply hover effect */
    .card {
        height: 250px; /* Set height of card */
        transition: all 0.3s ease; /* Smooth transition for hover effect */
        margin-bottom: 20px; /* Add bottom margin between cards */
    }

    .card:hover {
        background-color: #f4623a; /* Change background color on hover */
        color: #fff; /* Change text color to white on hover */
        transform: translateY(-10px); /* Move card up on hover */
    }

    /* Center card title vertically */
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Style for the search form */
    .search-form {
        margin-left: 0; /* Adjust margin to align form to the left */
    }
</style>

<body>
    <?php include '../templates/can_navbar.php'; ?>
    <br><br>

    <div class="container mt-5">
        <main class="flex-shrink-0">
            <!-- Search Form -->
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="title" class="form-control" placeholder="Search by Title">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="min_salary" class="form-control" placeholder="Minimum Salary">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="max_salary" class="form-control" placeholder="Maximum Salary">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">Select Category</option>
                            <option value="IT">IT</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Finance">Finance</option>
                            <option value="Sales">Sales</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                <div class="col-md-12 text-end">
                    <button type="submit" name="search_jobs" class="btn btn-primary">Search Jobs</button>
                </div>
                </div>
            </form>

            <!-- Jobs Listing -->
            <h2>Jobs Listing</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Salary</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['description']); ?></td>
                        <td><?php echo htmlspecialchars($job['salary']); ?></td>
                        <td><?php echo htmlspecialchars($job['category']); ?></td>
                        <td>
                            <?php
                            // Check if the job has been applied by the candidate
                            $applied = checkIfApplied($job['job_id'], $user_id);
                            if ($applied) {
                                echo '<button type="button" class="btn btn-success" disabled>Applied</button>';
                            } else {
                                echo '<form method="POST" action="jobs.php">';
                                echo '<input type="hidden" name="action" value="apply_job">';
                                echo '<input type="hidden" name="job_id" value="' . $job['job_id'] . '">';
                                echo '<button type="submit" class="btn btn-primary">Apply</button>';
                                echo '</form>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>

    <?php include '../templates/footer.php'; ?>

    <!-- Bootstrap and Custom Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
