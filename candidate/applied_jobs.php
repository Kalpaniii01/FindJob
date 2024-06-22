<?php
// applied_jobs.php

// Include necessary files
require_once '../includes/db.php'; // Include your database connection script
require_once '../includes/functions.php'; // Include your functions script

// Check if user is logged in and is a candidate user
if (!isLoggedIn() || !isCandidate()) {
    // Redirect to login page or appropriate access denied page
    header('Location: ../login.php');
    exit;
}

// Get user_id from session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch applied jobs for the candidate using the function from functions.php
$applied_jobs = getAppliedJobsForCandidate($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applied Jobs</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css"> <!-- Adjust path as necessary -->
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="../assets/index/assert/favicon.ico" />
    
    <!-- Bootstrap Icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />
    
    <!-- SimpleLightbox plugin CSS-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css" rel="stylesheet" />
    
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../assets/index/css/styles.css" rel="stylesheet" />
</head>
<style>
    html, body {
        height: 100%;
    }
    
    body {
        display: flex;
        flex-direction: column;
        font-family: 'Merriweather Sans', sans-serif;
        color: #000;
        padding-top: 0;
        margin: 0; /* Reset margin to ensure full width */
        min-height: 100vh; /* Ensure body takes full viewport height */
    }
    
    main {
        flex: 1;
        padding-bottom: 40px; /* Space for footer */
    }
    
    footer {
        background-color: #f8f9fa;
        padding: 20px 0;
        position: relative;
        bottom: 0;
        width: 100%;
    }
    
    .navbar {
        background-color: #fff;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1030;
    }
    
    .navbar-dark .navbar-nav .nav-link {
        color: #000;
    }
    
    .navbar-dark .navbar-nav .nav-link:hover {
        color: #ffa500;
    }
    
    .card {
        height: 250px;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    
    .card:hover {
        background-color: #f4623a;
        color: #fff;
        transform: translateY(-10px);
    }
    
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Styling for the footer */
    footer {
        bottom: 0;
        width: 100%;
        padding: 20px 0;
    }
</style>
<body>
    <?php include '../templates/can_navbar.php'; ?>
    <br><br>

    <div class="container mt-5">
        <main class="flex-shrink-0">
            <h2>Applied Jobs</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Salary</th>
                        <th>Category</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applied_jobs as $job): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['description']); ?></td>
                            <td><?php echo htmlspecialchars($job['salary']); ?></td>
                            <td><?php echo htmlspecialchars($job['category']); ?></td>
                            <td>
                                <?php
                                $status = htmlspecialchars($job['status']);
                                $status_icon = '';
                                
                                // Determine status icon based on status
                                switch ($status) {
                                    case 'pending':
                                        $status_icon = '<i class="bi bi-clock"></i>'; // Example icon for pending
                                        break;
                                    case 'approved':
                                        $status_icon = '<i class="bi bi-check-circle-fill text-success"></i>'; // Example icon for approved
                                        break;
                                    case 'rejected':
                                        $status_icon = '<i class="bi bi-x-circle-fill text-danger"></i>'; // Example icon for rejected
                                        break;
                                    default:
                                        $status_icon = ''; // No icon for unknown status
                                        break;
                                }

                                echo $status_icon . ' ' . $status;
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
