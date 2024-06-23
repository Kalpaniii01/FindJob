<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a candidate user
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

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
    <br><br>

    <div class="container mt-5 ">
        <main class="flex-shrink-0">
            <div class="row">
                <div class="col-md-12">
                    <h1>Welcome, <?php echo htmlspecialchars($admin['full_name']); ?></h1>
                </div>
            </div>
            
            <!-- Additional Sections or Content can be added here -->
        </main>


        <br>

        <!-- Additional Sections or Content can be added here -->

    </div>

    <?php include '../templates/footer.php'; ?>

    <!-- Bootstrap and Custom Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs5-toggle-switch/dist/bs5-toggle-switch.min.js"></script>
    <script src="../assets/index/js/scripts.js"></script>
</body>
</html>