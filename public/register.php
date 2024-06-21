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
    <script>
        function showAdditionalFields(role) {
            document.getElementById('adminFields').style.display = role === 'admin' ? 'block' : 'none';
            document.getElementById('candidateFields').style.display = role === 'candidate' ? 'block' : 'none';
            document.getElementById('companyFields').style.display = role === 'company' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <h2>Register</h2>
    <?php
    if (!empty($error_message)) {
        echo '<p style="color:red;">' . $error_message . '</p>';
    }
    ?>
    <form action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>
        
        <label for="role">Role:</label>
        <select name="role" id="role" required onchange="showAdditionalFields(this.value)">
            <option value="">Select role</option>
            <option value="candidate">Candidate</option>
            <option value="company">Company</option>
            <option value="admin">Admin</option>
        </select><br>

        <div id="adminFields" style="display:none;">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name"><br>
        </div>

        <div id="candidateFields" style="display:none;">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name"><br>
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number"><br>
            <label for="address">Address:</label>
            <textarea name="address" id="address"></textarea><br>
            <label for="cv_file">CV File:</label>
            <input type="file" name="cv_file" id="cv_file"><br>
        </div>

        <div id="companyFields" style="display:none;">
            <label for="company_name">Company Name:</label>
            <input type="text" name="company_name" id="company_name"><br>
            <label for="location">Location:</label>
            <input type="text" name="location" id="location"><br>
            <label for="industry">Industry:</label>
            <input type="text" name="industry" id="industry"><br>
            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea><br>
        </div>
        
        <input type="submit" value="Register">
    </form>
</body>
</html>
