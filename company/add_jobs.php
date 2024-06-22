<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a company user
if (!isLoggedIn() || !isCompany()) {
    header('Location: ../login.php');
    exit;
}

// Get user_id from session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Get company_id using user_id
$company_id = getCompanyIdByUserId($user_id);

if ($company_id === null) {
    die('Company not found for this user.');
}

// Initialize variables for form fields
$title = '';
$description = '';
$requirements = '';
$location = '';
$salary = '';
$category = ''; // Add category variable

// Error messages
$errors = [];

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $requirements = filter_input(INPUT_POST, 'requirements', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    if (empty($title)) {
        $errors[] = 'Job Title is required';
    }
    if (empty($description)) {
        $errors[] = 'Job Description is required';
    }
    if (empty($requirements)) {
        $errors[] = 'Job Requirements are required';
    }
    if (empty($location)) {
        $errors[] = 'Job Location is required';
    }
    if (empty($salary)) {
        $errors[] = 'Salary is required';
    }
    if (empty($category)) {
        $errors[] = 'Category is required';
    }

    if (empty($errors)) {
        $result = addJob($company_id, $title, $description, $requirements, $location, $salary, $category);

        if ($result === true) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job</title>
    <link rel="icon" type="image/x-icon" href="../assets/index/assert/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css" rel="stylesheet" />
    <link href="../assets/index/css/styles.css" rel="stylesheet" />
</head>
<style>
    /* Style definitions */
    html, body { 
        height: 100%; 
    }
    body { 
        display: flex; 
        flex-direction: column; 
        font-family: 'Merriweather Sans', sans-serif; color: #000; 
    }
    main { 
        flex: 1; padding-top: 60px; 
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
    }
    .navbar-dark .navbar-nav .nav-link { 
        color: #000; 
    }
    .navbar-dark .navbar-nav .nav-link:hover { 
        color: #ffa500; 
    }
    .card { 
        height: 250px; transition: all 0.3s ease; 
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
</style>
<body>
    <?php include '../templates/com_navbar.php'; ?>

    <div class="container mt-5">
        <h1>Add Job</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Job Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Job Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="requirements" class="form-label">Job Requirements</label>
                <textarea class="form-control" id="requirements" name="requirements" rows="3"><?php echo htmlspecialchars($requirements); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Job Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>">
            </div>
            <div class="mb-3">
                <label for="salary" class="form-label">Salary</label>
                <input type="text" class="form-control" id="salary" name="salary" value="<?php echo htmlspecialchars($salary); ?>">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-control" id="category" name="category">
                    <option value="" disabled selected>Select a category</option>
                    <option value="IT">IT</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Sales">Sales</option>
                    <option value="Human Resources">Human Resources</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="clearForm()">Clear</button><br><br><br><br>
        </form>
    </div>

    <?php include '../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs5-toggle-switch/dist/bs5-toggle-switch.min.js"></script>
    <script src="../assets/index/js/scripts.js"></script>
    <script>
        function clearForm() {
            document.getElementById("title").value = "";
            document.getElementById("description").value = "";
            document.getElementById("requirements").value = "";
            document.getElementById("location").value = "";
            document.getElementById("salary").value = "";
            document.getElementById("category").selectedIndex = 0;
        }
    </script>
</body>
</html>
