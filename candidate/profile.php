<?php
require_once '../includes/db.php'; // Include your database connection script
require_once '../includes/functions.php'; // Include your functions script

// Check if user is logged in and is a candidate user
if (!isLoggedIn() || !isCandidate()) {
    header('Location: ../login.php');
    exit;
}

// Get candidate details
$user_id = $_SESSION['user_id'];
$candidate = getCandidateDetailsByUserId($user_id);

if ($candidate === null) {
    // Handle error, candidate not found
    die('Candidate not found.');
}

// Initialize variables for form fields
$full_name = $candidate['full_name'];
$phone_number = $candidate['phone_number'];
$address = $candidate['address'];

// Error messages
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (empty($full_name)) {
        $errors[] = 'Full Name is required';
    }

    // If no errors, proceed with updating the profile
    if (empty($errors)) {
        $result = updateCandidateProfile($user_id, $full_name, $phone_number, $address);

        if ($result === true) {
            // Profile updated successfully, redirect or display success message
            header('Location: profile.php'); // Redirect to the same page to see updated details
            exit;
        } else {
            // Error updating profile, handle accordingly
            $errors[] = $result; // Assuming updateCandidateProfile returns error message on failure
        }
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile</title>
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
        margin: 0;
        display: flex;
        flex-direction: column;
    }
    
    body {
        font-family: 'Merriweather Sans', sans-serif; /* Use Merriweather Sans as the font */
        color: #000; /* Set default text color to black */
    }
    
    main {
        flex: 1; /* This makes the main content area take up remaining space */
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
    <?php include '../templates/can_navbar.php'; ?>

    <main class="container mt-5">
        <h1>Candidate Profile</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form id="profileForm" style="padding-bottom: 7%;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>">
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($address); ?></textarea>
            </div>
            <button type="button" class="btn btn-secondary" onclick="cancelChanges()">Cancel</button>
            <button type="submit" class="btn btn-primary" id="updateButton" disabled>Update</button>
        </form>
    </main>

    <?php include '../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/index/js/scripts.js"></script>
    <script>
        const originalValues = {
            full_name: document.getElementById('full_name').value,
            phone_number: document.getElementById('phone_number').value,
            address: document.getElementById('address').value,
        };

        function checkChanges() {
            const currentValues = {
                full_name: document.getElementById('full_name').value,
                phone_number: document.getElementById('phone_number').value,
                address: document.getElementById('address').value,
            };

            const updateButton = document.getElementById('updateButton');
            updateButton.disabled = JSON.stringify(originalValues) === JSON.stringify(currentValues);
        }

        function cancelChanges() {
            document.getElementById('full_name').value = originalValues.full_name;
            document.getElementById('phone_number').value = originalValues.phone_number;
            document.getElementById('address').value = originalValues.address;
            checkChanges();
        }

        document.getElementById('full_name').addEventListener('input', checkChanges);
        document.getElementById('phone_number').addEventListener('input', checkChanges);
        document.getElementById('address').addEventListener('input', checkChanges);
    </script>
</body>
</html>
