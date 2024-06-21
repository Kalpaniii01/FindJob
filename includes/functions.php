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

function userExists($username, $email) {
    $link = dbConnect();
    $query = "SELECT user_id FROM Users WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $num_rows = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
    return $num_rows > 0;
}

function registerUser($username, $password, $email, $role, $additional_details) {
    if (userExists($username, $email)) {
        return 'Username or email already exists';
    }

    $link = dbConnect();
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO Users (username, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $username, $password_hashed, $email, $role);
        $execute_result = mysqli_stmt_execute($stmt);

        if ($execute_result) {
            $user_id = mysqli_insert_id($link); // Get the inserted user ID

            // Insert into the relevant table based on role
            switch ($role) {
                case 'admin':
                    $full_name = $additional_details['full_name'];
                    $query = "INSERT INTO Admins (user_id, full_name) VALUES (?, ?)";
                    break;
                case 'candidate':
                    $full_name = $additional_details['full_name'];
                    $phone_number = $additional_details['phone_number'];
                    $address = $additional_details['address'];
                    $cv_file = $additional_details['cv_file'];
                    $query = "INSERT INTO Candidates (user_id, full_name, phone_number, address, cv_file) VALUES (?, ?, ?, ?, ?)";
                    break;
                case 'company':
                    $company_name = $additional_details['company_name'];
                    $location = $additional_details['location'];
                    $industry = $additional_details['industry'];
                    $description = $additional_details['description'];
                    $query = "INSERT INTO Companies (user_id, company_name, location, industry, description) VALUES (?, ?, ?, ?, ?)";
                    break;
                default:
                    return 'Invalid role specified';
            }

            $stmt = mysqli_prepare($link, $query);

            if ($stmt) {
                switch ($role) {
                    case 'admin':
                        mysqli_stmt_bind_param($stmt, "is", $user_id, $full_name);
                        break;
                    case 'candidate':
                        mysqli_stmt_bind_param($stmt, "issss", $user_id, $full_name, $phone_number, $address, $cv_file);
                        break;
                    case 'company':
                        mysqli_stmt_bind_param($stmt, "issss", $user_id, $company_name, $location, $industry, $description);
                        break;
                }

                $execute_result = mysqli_stmt_execute($stmt);

                if ($execute_result) {
                    return true;
                } else {
                    error_log("MySQL execute error (insert into role table): " . mysqli_stmt_error($stmt));
                    return 'Database error: ' . mysqli_stmt_error($stmt);
                }
            } else {
                error_log("MySQL prepare error (role table): " . mysqli_error($link));
                return 'Database error: ' . mysqli_error($link);
            }
        } else {
            error_log("MySQL execute error (Users table): " . mysqli_stmt_error($stmt));
            return 'Database error: ' . mysqli_stmt_error($stmt);
        }
    } else {
        error_log("MySQL prepare error (Users table): " . mysqli_error($link));
        return 'Database error: ' . mysqli_error($link);
    }
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
