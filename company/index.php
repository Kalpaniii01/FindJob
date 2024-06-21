<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$companyCount = getCount('companies');
$candidateCount = getCount('candidates');
$jobCount = getCount('jobs');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
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
    
    <!-- Custom CSS -->
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
    </style>
    
    <title>Company Dashboard</title>
</head>
<body id="page-top">
    <?php include '../templates/com_navbar.php'; ?>

    <!-- Main Content -->
    <main class="flex-shrink-0">
        <div class="container mt-5">
            <h1>Welcome, <?php echo getCompanyName(); ?></h1>
            <p>Welcome to the company dashboard. Here you can see an overview of your company's statistics.</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Number of Companies</h5>
                            <p class="card-text"><?php echo $companyCount; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Number of Candidates</h5>
                            <p class="card-text"><?php echo $candidateCount; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Number of Jobs</h5>
                            <p class="card-text"><?php echo $jobCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../templates/footer.php'; ?>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SimpleLightbox plugin JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
    <!-- Core theme JS-->
    <script src="../assets/index/js/scripts.js"></script>
    <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
</body>
</html>
