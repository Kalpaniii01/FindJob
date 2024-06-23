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
    deleteCandidate($candidate_id);
    header('Location: candidates.php');
    exit;
} else {
    die('Candidate ID not provided.');
}
?>
