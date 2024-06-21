<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    if (registerUser($username, $password, $email, $role)) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: Could not register user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <form action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>
        
        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="candidate">Candidate</option>
            <option value="company">Company</option>
            <option value="admin">Admin</option>
        </select><br>
        
        <input type="submit" value="Register">
    </form>
</body>
</html>
