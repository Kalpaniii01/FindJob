<?php
require_once 'db.php';

session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get user role
function getRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

// Check if user is admin
function isAdmin() {
    return getRole() === 'admin';
}

// Check if user is company
function isCompany() {
    return getRole() === 'company';
}

// Check if user is candidate
function isCandidate() {
    return getRole() === 'candidate';
}

// Check if username or email already exists in database
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

// Register user with username, password, email, role, and additional details based on role
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

// Log in user with username and password
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

// Log out user by destroying session
function logoutUser() {
    session_unset();
    session_destroy();
}

// Get count of records in a table
function getCount($table) {
    $conn = dbConnect(); // Get the database connection
    $sql = "SELECT COUNT(*) as count FROM $table";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}

// Get company name based on company ID stored in session
function getCompanyName() {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $link = dbConnect();

        // Determine table and column based on user role
        switch ($_SESSION['user_role']) {
            case 'company':
                $query = "SELECT company_name FROM Companies WHERE user_id = ?";
                break;
            // Add cases for other roles as needed
            default:
                return "Guest Company"; // Default if role is not recognized
        }

        $stmt = mysqli_prepare($link, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $companyName);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            return $companyName;
        } else {
            // Handle query preparation error
            return "Company Name Not Found";
        }
    } else {
        return "Guest Company"; // Default if user is not logged in
    }
}

function getCompanyIdByUserId($user_id) {
    global $db;
    $db = dbConnect(); // Get the database connection

    // Check if the connection is still active
    if (!mysqli_ping($db)) {
        die("Connection lost. Reconnecting...");
        // Attempt to reconnect
        $db = dbConnect();
        if (!$db) {
            die("ERROR: Could not reconnect. ". mysqli_connect_error());
        }
    }

    // Example SQL query
    $sql = "SELECT company_id FROM companies WHERE user_id =?";
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die("prepare() failed: ". htmlspecialchars($db->error));
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row['company_id'];
    } else {
        return null; // Company not found
    }
}

function addJob($company_id, $title, $description, $requirements, $location, $salary, $category) {
    global $db; // Ensure $db is available in the function

    $sql = "INSERT INTO jobs (company_id, title, description, requirements, location, salary, category, date_posted)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die('prepare() failed: ' . htmlspecialchars($db->error));
    }

    // Correct data types: i (integer), s (string), d (double), s (string)
    $stmt->bind_param("isssdss", $company_id, $title, $description, $requirements, $location, $salary, $category);

    if ($stmt->execute()) {
        return true; // Insert successful
    } else {
        return $stmt->error; // Return error message on failure
    }
}


function getCompanyDetails($company_id) {
    global $db;

    $sql = "SELECT * FROM companies WHERE company_id = ?";
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die('prepare() failed: ' . htmlspecialchars($db->error));
    }

    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function updateCompanyProfile($company_id, $company_name, $location, $industry, $description) {
    global $db;

    $sql = "UPDATE companies SET company_name = ?, location = ?, industry = ?, description = ? WHERE company_id = ?";
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die('prepare() failed: ' . htmlspecialchars($db->error));
    }

    $stmt->bind_param("ssssi", $company_name, $location, $industry, $description, $company_id);

    if ($stmt->execute()) {
        return true;
    } else {
        return $stmt->error;
    }
}

function updateAdminProfile($admin_id, $full_name) {
    global $db;

    $sql = "UPDATE admins SET full_name = ? WHERE admin_id = ?";
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die('prepare() failed: ' . htmlspecialchars($db->error));
    }

    $stmt->bind_param("si",$full_name, $admin_id);

    if ($stmt->execute()) {
        return true;
    } else {
        return $stmt->error;
    }
}

function getCandidateDetailsByUserId($user_id) {
    global $db;
    $db = dbConnect(); // Get the database connection

    // Check if the connection is still active
    if (!mysqli_ping($db)) {
        die("Connection lost. Reconnecting...");
        // Attempt to reconnect
        $db = dbConnect();
        if (!$db) {
            die("ERROR: Could not reconnect. " . mysqli_connect_error());
        }
    }

    // Example SQL query to fetch candidate details
    $sql = "SELECT full_name, address, phone_number FROM candidates WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die("prepare() failed: " . htmlspecialchars($db->error));
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc(); // Return candidate details as an associative array
    } else {
        return null; // Candidate not found
    }
}


function getAdminDetailsByUserId($user_id) {
    global $db;
    $db = dbConnect(); // Get the database connection

    // Check if the connection is still active
    if (!mysqli_ping($db)) {
        die("Connection lost. Reconnecting...");
        // Attempt to reconnect
        $db = dbConnect();
        if (!$db) {
            die("ERROR: Could not reconnect. " . mysqli_connect_error());
        }
    }

    // Example SQL query to fetch candidate details
    $sql = "SELECT full_name FROM admins WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die("prepare() failed: " . htmlspecialchars($db->error));
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc(); // Return candidate details as an associative array
    } else {
        return null; // Candidate not found
    }
}


function getRecommendedJobsForCandidate($limit = 6) {
    global $db;

    // Example SQL query to fetch last 6 jobs
    $sql = "SELECT job_id, company_id, title, description, salary, category, date_posted 
            FROM jobs 
            ORDER BY date_posted DESC 
            LIMIT ?";

    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die("prepare() failed: " . htmlspecialchars($db->error));
    }

    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $jobs = [];
    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }

    return $jobs;
}

/**
 * Apply for a job.
 *
 * @param int $job_id The ID of the job to apply for.
 * @param int $candidate_id The ID of the candidate applying for the job.
 * @return bool Returns true on success, false on failure.
 */
function applyForJob($job_id, $candidate_id) {
    global $db; // Assuming $db is your MySQLi database connection object

    // Check if $db is a valid MySQLi connection
    if (!($db instanceof mysqli) || $db->connect_error) {
        die("ERROR: Could not connect to database.");
    }

    try {
        // Start a transaction
        $db->begin_transaction();

        // Insert application into applications table
        $sql = "INSERT INTO applications (job_id, candidate_id, date_applied) VALUES (?, ?, NOW())";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: (" . $db->errno . ") " . $db->error);
        }

        $stmt->bind_param('ii', $job_id, $candidate_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        // Commit the transaction
        $db->commit();

        return true; // Application successful
    } catch (Exception $e) {
        // Rollback the transaction on failure
        $db->rollback();
        // You can log the error or handle it as needed
        error_log('Failed to apply for job: ' . $e->getMessage());
        return false; // Application failed
    }
}

/**
 * Check if a candidate has already applied for a job.
 *
 * @param int $job_id The ID of the job to check.
 * @param int $candidate_id The ID of the candidate to check.
 * @return bool Returns true if candidate has applied, false otherwise.
 */
function checkIfApplied($job_id, $candidate_id) {
    global $link; // Assuming $link is your mysqli connection object

    // Check if $link is a valid mysqli connection
    if (!($link instanceof mysqli) || $link->connect_error) {
        // Attempt to reconnect
        $link = dbConnect();
        if (!($link instanceof mysqli) || $link->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL statement
    $sql = "SELECT COUNT(*) FROM applications WHERE job_id = ? AND candidate_id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ii", $job_id, $candidate_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return ($count > 0); // Returns true if the candidate has applied, false otherwise
}

function updateCandidateProfile($user_id, $full_name, $phone_number, $address) {
    global $db; // Assuming $db is your database connection object

    try {
        // Check if $db is a valid mysqli connection
        if (!$db instanceof mysqli || $db->connect_error) {
            // Attempt to reconnect
            $db = dbConnect();
            if (!$db) {
                return "ERROR: Could not reconnect to database.";
            }
        }

        // Update candidate details in the candidates table
        $stmt = $db->prepare("UPDATE candidates SET full_name = ?, phone_number = ?, address = ? WHERE user_id = ?");
        if ($stmt === false) {
            return "prepare() failed: " . htmlspecialchars($db->error);
        }
        $stmt->bind_param("sssi", $full_name, $phone_number, $address, $user_id);

        // Execute the statement
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->affected_rows === 1) {
            return true; // Profile updated successfully
        } else {
            return "Failed to update profile."; // Error updating profile
        }
    } catch (mysqli_sql_exception $e) {
        return "MySQL error: " . $e->getMessage(); // Handle MySQL errors
    }
}

function fetchJobs($title = null, $min_salary = null, $max_salary = null, $category = null) {
    global $db; // Assuming $db is your database connection

    // Check if $db is a valid mysqli connection
    if (!($db instanceof mysqli) || $db->connect_error) {
        // Attempt to reconnect
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Base SQL query to select jobs
    $sql = "SELECT job_id, company_id, title, description, salary, category, date_posted FROM jobs";

    // Initialize an array to store conditions
    $conditions = [];
    $params = [];

    // Build the WHERE clause based on provided filters
    if (!empty($title)) {
        $conditions[] = "title LIKE ?";
        $params[] = "%" . $title . "%";
    }
    if (!empty($min_salary)) {
        $conditions[] = "salary >= ?";
        $params[] = $min_salary;
    }
    if (!empty($max_salary)) {
        $conditions[] = "salary <= ?";
        $params[] = $max_salary;
    }
    if (!empty($category)) {
        $conditions[] = "category = ?";
        $params[] = $category;
    }

    // If there are conditions, append them to the base query
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Prepare and execute the SQL query
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        die("prepare() failed: " . htmlspecialchars($db->error));
    }

    // Bind parameters if there are any
    if (!empty($params)) {
        // Dynamically bind parameters
        $types = str_repeat("s", count($params)); // Assuming all parameters are strings
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch jobs into an array
    $jobs = [];
    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }

    // Close statement and return jobs
    $stmt->close();
    return $jobs;
}
function getAppliedJobsForCandidate($user_id) {
    $db = dbConnect(); // Get database connection

    // Check if database connection is established
    if (!$db) {
        die("Database connection error: " . mysqli_connect_error());
    }

    // Prepare SQL statement to fetch applied jobs with details and status
    $sql = "SELECT j.job_id, j.title, j.description, j.salary, j.category, a.status
            FROM jobs j
            INNER JOIN applications a ON j.job_id = a.job_id
            WHERE a.candidate_id = ?
            ORDER BY a.date_applied DESC";

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $applied_jobs = [];
    while ($row = $result->fetch_assoc()) {
        $applied_jobs[] = $row;
    }

    return $applied_jobs;
}

function getApplicantsForCompanyJobs($company_id) {
    global $db;

    // Ensure $db is a valid MySQLi object and reconnect if necessary
    if (!($db instanceof mysqli) || $db->connect_error) {
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL query
    $sql = "SELECT a.application_id, j.title AS job_title, c.full_name, c.phone_number AS phone, a.status
            FROM applications a
            INNER JOIN jobs j ON a.job_id = j.job_id
            INNER JOIN candidates c ON a.candidate_id = c.user_id
            WHERE j.company_id = ?";

    // Prepare statement and bind parameters
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die('ERROR: Failed to prepare statement: ' . $db->error);
    }

    $stmt->bind_param("i", $company_id);
    $stmt->execute();

    // Get result set
    $result = $stmt->get_result();

    // Fetch applicants into array
    $applicants = [];
    while ($row = $result->fetch_assoc()) {
        $applicants[] = $row;
    }

    // Close statement
    $stmt->close();

    // Return array of applicants
    return $applicants;
}

function updateApplicationStatus($application_id, $status) {
    $db = dbConnect();
    
    $sql = "UPDATE applications SET status = ? WHERE application_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("si", $status, $application_id);
    
    return $stmt->execute();
}

function deleteCandidate($candidate_id) {
    global $db;

    // Check if $db is a valid MySQLi connection
    if (!($db instanceof mysqli) || $db->connect_error) {
        // Attempt to reconnect
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL statement to delete candidate
    $sql = "DELETE FROM candidates WHERE candidate_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();

    // Check if the candidate was deleted
    if ($stmt->affected_rows === 1) {
        return true; // Candidate deleted successfully
    } else {
        return false; // Candidate not found or deletion failed
    }
}

function deleteJob($job_id) {
    global $db;

    // Check if $db is a valid MySQLi connection
    if (!($db instanceof mysqli) || $db->connect_error) {
        // Attempt to reconnect
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL statement to delete job
    $sql = "DELETE FROM jobs WHERE job_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("i", $job_id);
    $stmt->execute();

    // Check if the job was deleted
    if ($stmt->affected_rows === 1) {
        return true; // Job deleted successfully
    } else {
        return false; // Job not found or deletion failed
    }
}

function deletecompany ($company_id) {
    global $db;

    // Check if $db is a valid MySQLi connection
    if (!($db instanceof mysqli) || $db->connect_error) {
        // Attempt to reconnect
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL statement to delete company
    $sql = "DELETE FROM companies WHERE company_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("i", $company_id);
    $stmt->execute();

    // Check if the company was deleted
    if ($stmt->affected_rows === 1) {
        return true; // Company deleted successfully
    } else {
        return false; // Company not found or deletion failed
    }
}

function getAllCandidates() {
    global $db;

    // Ensure $db is a valid MySQLi object and reconnect if necessary
    if (!($db instanceof mysqli) || $db->connect_error) {
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL query to fetch all candidates
    $sql = "SELECT candidate_id, full_name, phone_number,address,cv_file FROM candidates";
    $result = $db->query($sql);

    // Fetch candidates into array
    $candidates = [];
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }

    return $candidates;
}

function getAllCompanies () {
    global $db;

    // Ensure $db is a valid MySQLi object and reconnect if necessary
    if (!($db instanceof mysqli) || $db->connect_error) {
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL query to fetch all companies
    $sql = "SELECT company_id, company_name, location, industry, description FROM companies";
    $result = $db->query($sql);

    // Fetch companies into array
    $companies = [];
    while ($row = $result->fetch_assoc()) {
        $companies[] = $row;
    }

    return $companies;
}

function getAlljobs() {
    global $db;

    // Ensure $db is a valid MySQLi object and reconnect if necessary
    if (!($db instanceof mysqli) || $db->connect_error) {
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL query to fetch all jobs
    $sql = "SELECT  job_id,title,company_name, jobs.description, salary, category, date_posted FROM jobs INNER JOIN companies ON jobs.company_id = companies.company_id";
    $result = $db->query($sql);

    // Fetch jobs into array
    $jobs = [];
    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }

    return $jobs;
}

function updateCandidate($candidate_id, $full_name, $email, $phone_number, $address, $cv_file) {
    global $db;

    // Check if $db is a valid MySQLi connection
    if (!($db instanceof mysqli) || $db->connect_error) {
        // Attempt to reconnect
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL statement to update candidate details
    $sql = "UPDATE candidates SET full_name = ?, phone_number = ?, address = ?, cv_file = ? WHERE candidate_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("sssssi", $full_name,$phone_number, $address, $cv_file, $candidate_id);
    $stmt->execute();

    // Check if the candidate was updated
    if ($stmt->affected_rows === 1) {
        return true; // Candidate updated successfully
    } else {
        return false; // Candidate not found or update failed
    }
}

function updateCompany($company_id, $company_name, $location, $industry, $description) {
    global $db;

    // Check if $db is a valid MySQLi connection
    if (!($db instanceof mysqli) || $db->connect_error) {
        // Attempt to reconnect
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL statement to update company details
    $sql = "UPDATE companies SET company_name = ?, location = ?, industry = ?, description = ? WHERE company_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("ssssi", $company_name, $location, $industry, $description, $company_id);
    $stmt->execute();

    // Check if the company was updated
    if ($stmt->affected_rows === 1) {
        return true; // Company updated successfully
    } else {
        return false; // Company not found or update failed
    }
}

function updateJob($job_id, $title, $description, $salary, $category,$date_posted) {
    global $db;
    $date_posted = date('Y-m-d H:i:s');

    // Check if $db is a valid MySQLi connection
    if (!($db instanceof mysqli) || $db->connect_error) {
        // Attempt to reconnect
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL statement to update job details
    $sql = "UPDATE jobs SET title = ?, description = ?, salary = ?, category = ?,date_posted = ? WHERE job_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("ssdssi", $title, $description, $salary, $category, $date_posted,$job_id);
    $stmt->execute();

    // Check if the job was updated
    if ($stmt->affected_rows === 1) {
        return true; // Job updated successfully
    } else {
        return false; // Job not found or update failed
    }
}

function getCandidateDetailsByCandidateId ($candidate_id) {
    global $db;

    // Ensure $db is a valid MySQLi object and reconnect if necessary
    if (!($db instanceof mysqli) || $db->connect_error) {
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL query to fetch candidate details by candidate ID
    $sql = "SELECT full_name, phone_number, address, cv_file FROM candidates WHERE candidate_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch candidate details
    $candidate = $result->fetch_assoc();

    return $candidate;
}

function getCompanyDetailsByCompanyId($company_id) {
    global $db;

    // Ensure $db is a valid MySQLi object and reconnect if necessary
    if (!($db instanceof mysqli) || $db->connect_error) {
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL query to fetch company details by company ID
    $sql = "SELECT company_name, location, industry, description FROM companies WHERE company_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch company details
    $company = $result->fetch_assoc();

    return $company;
}

function getJobDetailsByjobId ($job_id) {
    global $db;

    // Ensure $db is a valid MySQLi object and reconnect if necessary
    if (!($db instanceof mysqli) || $db->connect_error) {
        $db = dbConnect();
        if (!($db instanceof mysqli) || $db->connect_error) {
            die("ERROR: Could not reconnect to database.");
        }
    }

    // Prepare SQL query to fetch job details by job ID
    $sql = "SELECT title, description, salary, category, date_posted FROM jobs WHERE job_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $db->errno . ") " . $db->error);
    }

    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch job details
    $job = $result->fetch_assoc();

    return $job;
}


function getRecent($table, $id) {
    $conn = dbConnect(); // Assuming this function connects to the database
    $sql = "SELECT * FROM $table ORDER BY $id DESC LIMIT 5";
    $result = $conn->query($sql);

    $rows = array(); // Initialize an empty array to store results

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row; // Append each row to the $rows array
        }
    }

    return $rows;
}



?>
