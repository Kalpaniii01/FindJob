<?php
// applicants.php

// Include necessary files
require_once '../includes/db.php'; // Include your database connection script
require_once '../includes/functions.php'; // Include your functions script

// Check if user is logged in and is a company user
if (!isLoggedIn() || !isCompany()) {
    // Redirect to login page or appropriate access denied page
    header('Location: ../login.php');
    exit;
}

// Get user_id from session (assuming company owner's user_id is stored in session)
$user_id = $_SESSION['user_id'];

// Get company_id using user_id
$company_id = getCompanyIdByUserId($user_id);

if (!$company_id) {
    die("Company not found for user ID $user_id.");
}

// Fetch applicants for company's jobs
$applicants = getApplicantsForCompanyJobs($company_id);

// Handle status update (accept/reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'accept' || $_POST['action'] === 'reject') {
        $application_id = $_POST['application_id'];
        $status = ($_POST['action'] === 'accept') ? 'approved' : 'rejected';
        
        $success = updateApplicationStatus($application_id, $status);

        if ($success) {
            // Redirect to same page after status update
            header('Location: applicants.php');
            exit;
        } else {
            // Handle update error
            echo '<script>alert("Failed to update application status.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company View Applicants</title>
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

    <style>
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
            font-family: 'Merriweather Sans', sans-serif; /* Use Merriweather Sans as the font */
            color: #000; /* Set default text color to black */
            padding-top: 0; /* Remove top padding to fix the navbar at the top */
            margin: 0; /* Reset margin */
            min-height: 100vh; /* Ensure body takes full viewport height */
        }
        
        #wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Full viewport height */
        }
        
        main {
            flex: 1; /* Flex grow to push footer to the bottom */
            padding-bottom: 40px; /* Space for footer */
        }
        
        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            width: 100%; /* Push footer to bottom */
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
    </style>
</head>
<body>
    <div id="wrapper">
        <?php include '../templates/com_navbar.php'; ?>
        <br><br>

        <div class="container mt-5">
            <main class="flex-shrink-0">
                <h2>Company View Applicants</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Candidate Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applicants as $applicant): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($applicant['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($applicant['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($applicant['phone']); ?></td>
                                <td><?php echo htmlspecialchars($applicant['status']); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <input type="hidden" name="application_id" value="<?php echo $applicant['application_id']; ?>">
                                        <button type="submit" class="btn btn-success" name="action" value="accept">Accept</button>
                                        <button type="submit" class="btn btn-danger" name="action" value="reject">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div><br><br>

        <?php include '../templates/footer.php'; ?>
    </div>

    <!-- Bootstrap and Custom Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
