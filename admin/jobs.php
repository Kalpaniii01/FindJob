<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a job user
if (!isLoggedIn() || !isAdmin()) {
    // Redirect to login page or appropriate access denied page
    header('Location: ../public/login.php');
    exit;
}

// Get user_id from session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch admin details using user_id
$admin = getAdminDetailsByUserId($user_id);

if ($admin === null) {
    // Handle error, admin not found
    die('admin not found.');
}
$jobs = getAlljobs();

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage jobs</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css">
</head>
<body>
    <?php include '../templates/admin_navbar.php'; ?>

    <main class="container mt-5">
        <h1>Manage jobs</h1>
        
        <?php if (count($jobs) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Job Title</th>                        
                        <th>Company Name</th>
                        <th>Salary</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Date Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                        <tr>
                            
                            <td><?php echo $job['title']; ?></td>
                            <td><?php echo $job['company_name']; ?></td>
                            <td><?php echo $job['salary']; ?></td>
                            <td><?php echo $job['category']; ?></td>
                            <td><?php echo $job['description']; ?></td>
                            <td><?php echo $job['date_posted']; ?></td>
                            <td>
                            <a href="edit_job.php?id=<?php echo $job['job_id']; ?>" class="btn btn-primary" onclick="return confirm('Are you sure you want to edit this job details?');">Edit</a>
                            <a href="delete_job.php?id=<?php echo $job['job_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No job found.</p>
        <?php endif; ?>
    </main>

    <?php include '../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/index/js/scripts.js"></script>
   
</body>
</html>