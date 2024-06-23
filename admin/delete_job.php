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
    $job_id = $_GET['id'];
    deletejob($job_id);
    header('Location: companies.php');
    exit;
} else {
    die('job ID not provided.');
}
?>
