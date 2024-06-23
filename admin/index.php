<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a candidate user
if (!isLoggedIn() || !isCandidate()) {
    // Redirect to login page or appropriate access denied page
    header('Location: ../login.php');
    exit;
}

// Get user_id from session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch candidate details using user_id
$candidate = getCandidateDetailsByUserId($user_id);

if ($candidate === null) {
    // Handle error, candidate not found
    die('Candidate not found.');
}

// Fetch last 6 recommended jobs for the candidate
$jobs = getRecommendedJobsForCandidate(6); // Limit to last 6 jobs

// Get counts for jobs, companies, and recommended candidates
$numJobs = getCount('jobs');
$numCompanies = getCount('companies');
$numRecommendedCandidates = getCount('candidates');

// Process apply job action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_job') {
    if (isset($_POST['job_id'])) {
        $job_id = $_POST['job_id'];
        // Insert application into applications table
        $success = applyForJob($job_id, $user_id);

        if ($success) {
            // Redirect to same page after applying
            header('Location: index.php');
            exit;
        } else {
            // Handle application error
            echo '<script>alert("Failed to apply for the job.");</script>';
        }
    }
}
?>