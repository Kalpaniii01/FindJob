<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin user
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../public/login.php');
    exit;
}

// Check if job ID is provided
if (isset($_GET['id'])) {
    $job_id = $_GET['id'];
    $job = getJobDetailsByjobId($job_id);
    
    if ($job === null) {
        die('job not found.');
    }
} else {
    die('job ID not provided.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $salary = $_POST['salary'];
    $category = $_POST['category'];
    $date_posted = $_POST['date_posted'];

    updatejob($job_id, $title, $description, $salary, $category, $date_posted);
    header('Location: jobs.php');
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit job</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css">
</head>
<body>
    <?php include '../templates/admin_navbar.php'; ?>

    <main class="container mt-5">
        <h1>Edit job</h1>
        
        <form action="edit_job.php?id=<?php echo $job_id; ?>" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Job Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $job['title']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="description" class="form-control" id="description" name="description" value="<?php echo $job['description']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="salary" class="form-label">salary</label>
                <input type="text" class="form-control" id="salary" name="salary" value="<?php echo $job['salary']; ?>">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">category</label>
                <textarea class="form-control" id="category" name="category"><?php echo $job['category']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="date_posted" class="form-label">Date_Posted</label>
                <input type="text" class="form-control" id="date_posted" name="date_posted" value="<?php echo $job['date_posted']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </main>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
