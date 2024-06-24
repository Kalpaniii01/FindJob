<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin user
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../public/login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch candidate details using user_id
$admin = getAdminDetailsByUserId($user_id);


// Fetch metrics
$numCompanies = getCount('companies');
$numCandidates = getCount('candidates');
$numJobs = getCount('jobs');
$recentCandidates = getRecent('candidates', 'candidate_id');
$recentJobs = getRecent('jobs', 'job_id');
$recentCompanies = getRecent('companies', 'company_id');

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
</head>
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
    }
    
    main {
        flex: 1;
        padding-bottom: 40px;
    }
    
    footer {
        background-color: #f8f9fa;
        padding: 20px 0;
        position: relative;
        bottom: 0;
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
</style>

<body>
    <?php include '../templates/admin_navbar.php'; ?>
    <div class="container mt-5">
       
    <main class="flex-shrink-0">   
    <div class="row">
                <div class="col-md-12">
                    <h1>Welcome, <?php echo htmlspecialchars($admin['full_name']); ?></h1>
                    
                </div>
            </div>     
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Number of Companies</h5>
                        <p class="card-text"><?php echo $numCompanies; ?></p>
                        <i class="bi bi-building fs-2"></i> <!-- Bootstrap icon for companies -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Number of Candidates</h5>
                        <p class="card-text"><?php echo $numCandidates; ?></p>
                        <i class="bi bi-person fs-2"></i> <!-- Bootstrap icon for candidates -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Number of Jobs</h5>
                        <p class="card-text"><?php echo $numJobs; ?></p>
                        <i class="bi bi-briefcase fs-2"></i> <!-- Bootstrap icon for jobs -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Candidates Section -->
        <h2 class="mt-5">Recent Candidates</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentCandidates as $candidate): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($candidate['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($candidate['phone_number']); ?></td>
                        <td>
                            <a href="edit_candidate.php?id=<?php echo $candidate['candidate_id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="delete_candidate.php?id=<?php echo $candidate['candidate_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Recent Jobs Section -->
        <h2 class="mt-4">Recent Jobs</h2>
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
                <?php foreach ($recentJobs as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['description']); ?></td>
                        <td><?php echo htmlspecialchars($job['salary']); ?></td>
                        <td><?php echo htmlspecialchars($job['category']); ?></td>
                        <td>
                            <a href="edit_job.php?id=<?php echo $job['job_id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="delete_job.php?id=<?php echo $job['job_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Recent Companies Section -->
        <h2 class="mt-4">Recent Companies</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Industry</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentCompanies as $company): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($company['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($company['location']); ?></td>
                        <td><?php echo htmlspecialchars($company['industry']); ?></td>
                        <td><?php echo htmlspecialchars($company['description']); ?></td>
                        <td>
                            <a href="edit_company.php?id=<?php echo $company['company_id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="delete_company.php?id=<?php echo $company['company_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this company?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </main>
    </div>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
