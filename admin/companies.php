<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a company user
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
$companies = getAllCompanies();

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Companies</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css">
</head>
<body>
    <?php include '../templates/admin_navbar.php'; ?>

    <main class="container mt-5">
        <h1>Manage Companies</h1>
        
        <?php if (count($companies) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        
                        <th>Company Name</th>
                        <th>Location</th>
                        <th>Industry</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($companies as $company): ?>
                        <tr>
                            
                            <td><?php echo $company['company_name']; ?></td>
                            <td><?php echo $company['designation']; ?></td>
                            <td><?php echo $company['location']; ?></td>
                            <td><?php echo $company['industry']; ?></td>
                            <td><?php echo $company['description']; ?></td>
                            <td>
                            <a href="edit_company.php?id=<?php echo $company['company_id']; ?>" class="btn btn-primary" onclick="return confirm('Are you sure you want to edit this company details?');">Edit</a>
                            <a href="delete_company.php?id=<?php echo $company['company_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this company?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No company found.</p>
        <?php endif; ?>
    </main>

    <?php include '../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/index/js/scripts.js"></script>
   
</body>
</html>