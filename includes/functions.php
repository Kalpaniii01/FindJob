<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

function isAdmin() {
    return getRole() === 'admin';
}

function isCompany() {
    return getRole() === 'company';
}

function isCandidate() {
    return getRole() === 'candidate';
}

function registerUser($username, $password, $email, $role) {
    $link = dbConnect();
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO Users (username, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password_hashed, $email, $role);
    return mysqli_stmt_execute($stmt);
}

function loginUser($username, $password) {
    $link = dbConnect();
    $query = "SELECT user_id, password, role FROM Users WHERE username = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $hashed_password, $role);
    if (mysqli_stmt_fetch($stmt)) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role'] = $role;
            return true;
        }
    }
    return false;
}

function logoutUser() {
    session_unset();
    session_destroy();
}
?>
