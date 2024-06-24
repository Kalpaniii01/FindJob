<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin user
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../public/login.php');
    exit;
}

// Check if candidate ID is provided
if (isset($_GET['id'])) {
    $candidate_id = $_GET['id'];
    $candidate = getCandidateDetailsByCandidateId($candidate_id);
    
    if ($candidate === null) {
        die('Candidate not found.');
    }
} else {
    die('Candidate ID not provided.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $cv_file = $_POST['cv_file'];

    updateCandidate($candidate_id, $full_name, $email, $phone_number, $address, $cv_file);
    header('Location: candidates.php');
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Candidate</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css">
</head>
<body>
    <?php include '../templates/admin_navbar.php'; ?>

    <main class="container mt-5">
        <h1>Edit Candidate</h1>
        
        <form action="edit_candidate.php?id=<?php echo $candidate_id; ?>" method="post">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $candidate['full_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $candidate['phone_number']; ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address"><?php echo $candidate['address']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="cv_file" class="form-label">CV File</label>
                <input type="text" class="form-control" id="cv_file" name="cv_file" value="<?php echo $candidate['cv_file']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </main>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
