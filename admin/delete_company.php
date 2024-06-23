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
    $company_id = $_GET['id'];
    deletecompany($company_id);
    header('Location: companies.php');
    exit;
} else {
    die('company ID not provided.');
}
?>
