<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $additional_details = [];

    if ($role === 'admin') {
        $additional_details['full_name'] = trim($_POST['full_name']);
    } elseif ($role === 'candidate') {
        $additional_details['full_name'] = trim($_POST['full_name']);
        $additional_details['phone_number'] = trim($_POST['phone_number']);
        $additional_details['address'] = trim($_POST['address']);
        $additional_details['cv_file'] = trim($_POST['cv_file']);
    } elseif ($role === 'company') {
        $additional_details['company_name'] = trim($_POST['company_name']);
        $additional_details['location'] = trim($_POST['location']);
        $additional_details['industry'] = trim($_POST['industry']);
        $additional_details['description'] = trim($_POST['description']);
    }

    if (empty($username) || empty($password) || empty($email) || empty($role)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email format.';
    } else {
        $register_result = registerUser($username, $password, $email, $role, $additional_details);
        if ($register_result === true) {
            header("Location: login.php");
            exit();
        } else {
            $error_message = $register_result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Bootstrap Icons (if needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-3">Register</h2>
        <?php
        if (!empty($error_message)) {
            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
        }
        ?>
        <form action="register.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" onchange="showAdditionalFields(this.value)" required>
                    <option value="">Select role</option>
                    <option value="candidate">Candidate</option>
                    <option value="company">Company</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div id="adminFields" style="display:none;">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name">
                </div>
            </div>

            <div id="candidateFields" style="display:none;">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name">
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address"></textarea>
                </div>
                <div class="mb-3">
                    <label for="cv_file" class="form-label">CV File</label>
                    <input type="file" class="form-control" id="cv_file" name="cv_file">
                </div>
            </div>

            <div id="companyFields" style="display:none;">
                <div class="mb-3">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name">
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location">
                </div>
                <div class="mb-3">
                    <label for="industry" class="form-label">Industry</label>
                    <input type="text" class="form-control" id="industry" name="industry">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <!-- Link Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        function showAdditionalFields(role) {
            document.getElementById('adminFields').style.display = role === 'admin' ? 'block' : 'none';
            document.getElementById('candidateFields').style.display = role === 'candidate' ? 'block' : 'none';
            document.getElementById('companyFields').style.display = role === 'company' ? 'block' : 'none';
        }
    </script>
</body>
</html>
