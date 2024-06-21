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
    <!-- Link to custom CSS -->
    <link href="../assets/index/css/styles.css" rel="stylesheet">
</head>
<body>
    <section class="page-section" id="register">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 justify-content-center">
                <div class="col-lg-8 col-xl-6 text-center">
                    <h2 class="mt-0">Register with FindJob</h2>
                    <hr class="divider" />
                    <p class="text-muted mb-5">Fill in the form below to create your account.</p>
                </div>
            </div>
            <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
                <div class="col-lg-6">
                    <?php
                    if (!empty($error_message)) {
                        echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                    }
                    ?>
                    <form action="register.php" method="post">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                            <label for="username">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <label for="password">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            <label for="email">Email</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="role" name="role" onchange="showAdditionalFields(this.value)" required>
                                <option value="">Select role</option>
                                <option value="candidate">Candidate</option>
                                <option value="company">Company</option>
                                <option value="admin">Admin</option>
                            </select>
                            <label for="role">Role</label>
                        </div>
                        
                        <div id="adminFields" style="display:none;">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter your full name">
                                <label for="full_name">Full Name</label>
                            </div>
                        </div>

                        <div id="candidateFields" style="display:none;">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter your full name">
                                <label for="full_name">Full Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter your phone number">
                                <label for="phone_number">Phone Number</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="address" name="address" placeholder="Enter your address"></textarea>
                                <label for="address">Address</label>
                            </div>
                            <div class="mb-3">
                                <label for="cv_file" class="form-label">CV File</label>
                                <input type="file" class="form-control" id="cv_file" name="cv_file">
                            </div>
                        </div>

                        <div id="companyFields" style="display:none;">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter your company name">
                                <label for="company_name">Company Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="location" name="location" placeholder="Enter your location">
                                <label for="location">Location</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="industry" name="industry" placeholder="Enter your industry">
                                <label for="industry">Industry</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="description" name="description" placeholder="Enter your company description"></textarea>
                                <label for="description">Description</label>
                            </div>
                        </div>

                        <div class="d-grid"><button type="submit" class="btn btn-primary btn-xl">Register</button></div>
                    </form>
                </div>
            </div>
        </div>
    </section>

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
