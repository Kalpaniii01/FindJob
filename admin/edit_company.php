<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin user
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../public/login.php');
    exit;
}

// Check if company ID is provided
if (isset($_GET['id'])) {
    $company_id = $_GET['id'];
    $company = getcompanyDetailsBycompanyId($company_id);
    
    if ($company === null) {
        die('company not found.');
    }
} else {
    die('company ID not provided.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];
    $location = $_POST['location'];
    $industry = $_POST['industry'];
    $description = $_POST['description'];

    updatecompany($company_id, $company_name, $location, $industry, $description);
    header('Location: companies.php');
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit company</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css">
</head>
<body>
    <?php include '../templates/admin_navbar.php'; ?>

    <main class="container mt-5">
        <h1>Edit company</h1>
        
        <form action="edit_company.php?id=<?php echo $company_id; ?>" method="post">
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $company['company_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="location" class="form-control" id="location" name="location" value="<?php echo $company['location']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="industry" class="form-label">Industry</label>
                <input type="text" class="form-control" id="industry" name="industry" value="<?php echo $company['industry']; ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo $company['description']; ?></textarea>
            </div>
           
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </main>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
