<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a company user
if (!isLoggedIn() || !isCompany()) {
    header('Location: ../login.php');
    exit;
}

// Get company details
$user_id = $_SESSION['user_id'];
$company_id = getCompanyIdByUserId($user_id);
$company = getCompanyDetails($company_id);

// Initialize variables for form fields
$company_name = $company['company_name'];
$location = $company['location'];
$industry = $company['industry'];
$description = $company['description'];

// Error messages
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $industry = filter_input(INPUT_POST, 'industry', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (empty($company_name)) {
        $errors[] = 'Company Name is required';
    }

    // If no errors, proceed with updating the profile
    if (empty($errors)) {
        $result = updateCompanyProfile($company_id, $company_name, $location, $industry, $description);

        if ($result === true) {
            // Profile updated successfully, redirect or display success message
            header('Location: profile.php'); // Redirect to the same page to see updated details
            exit;
        } else {
            // Error updating profile, handle accordingly
            $errors[] = $result; // Assuming updateCompanyProfile returns error message on failure
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>
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
            font-family: 'Merriweather Sans', sans-serif; /* Use Merriweather Sans as the font */
            color: #000; /* Set default text color to black */
        }
        
        main {
            flex: 1;
            padding-top: 60px; /* Adjust as needed for fixed navbar */
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
    <?php include '../templates/com_navbar.php'; ?>

    <div class="container mt-5">
        <h1>Company Profile</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form id="profileForm" style="padding-bottom: 7%;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" >
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($company_name); ?>">
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>">
            </div>
            <div class="mb-3">
                <label for="industry" class="form-label">Industry</label>
                <input type="text" class="form-control" id="industry" name="industry" value="<?php echo htmlspecialchars($industry); ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <button type="button" class="btn btn-secondary" onclick="cancelChanges()">Cancel</button>
            <button type="submit" class="btn btn-primary" id="updateButton" disabled>Update</button>
        </form>
    </div>

    <?php include '../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs5-toggle-switch/dist/bs5-toggle-switch.min.js"></script>
    <script src="../assets/index/js/scripts.js"></script>
    <script>
        const originalValues = {
            company_name: document.getElementById('company_name').value,
            location: document.getElementById('location').value,
            industry: document.getElementById('industry').value,
            description: document.getElementById('description').value,
        };

        function checkChanges() {
            const currentValues = {
                company_name: document.getElementById('company_name').value,
                location: document.getElementById('location').value,
                industry: document.getElementById('industry').value,
                description: document.getElementById('description').value,
            };

            const updateButton = document.getElementById('updateButton');
            updateButton.disabled = JSON.stringify(originalValues) === JSON.stringify(currentValues);
        }

        function cancelChanges() {
            document.getElementById('company_name').value = originalValues.company_name;
            document.getElementById('location').value = originalValues.location;
            document.getElementById('industry').value = originalValues.industry;
            document.getElementById('description').value = originalValues.description;
            checkChanges();
        }

        document.getElementById('company_name').addEventListener('input', checkChanges);
        document.getElementById('location').addEventListener('input', checkChanges);
        document.getElementById('industry').addEventListener('input', checkChanges);
        document.getElementById('description').addEventListener('input', checkChanges);
    </script>
</body>
</html>
