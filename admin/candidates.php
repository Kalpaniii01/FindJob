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
$candidates = getAllCandidates();

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates</title>
    <link rel="stylesheet" href="../assets/index/css/styles.css">
</head>
<body>
    <?php include '../templates/admin_navbar.php'; ?>

    <main class="container mt-5">
        <h1>Manage Candidates</h1>
        
        <?php if (count($candidates) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                       
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>CV</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr>
                            <td><?php echo $candidate['full_name']; ?></td>
                            <td><?php echo $candidate['email']; ?></td>
                            <td><?php echo $candidate['phone_number']; ?></td>
                            <td><?php echo $candidate['address']; ?></td>
                            <td><?php echo $candidate['cv_file']; ?></td>
                            <td>
                            <a href="edit_candidate.php?id=<?php echo $candidate['candidate_id']; ?>" class="btn btn-primary" onclick="return confirm('Are you sure you want to edit this candidate details?');">Edit</a>
                            <a href="delete_candidate.php?id=<?php echo $candidate['candidate_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No candidates found.</p>
        <?php endif; ?>
    </main>

    <?php include '../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/index/js/scripts.js"></script>
   
</body>
</html>