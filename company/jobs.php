<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a company user
if (!isLoggedIn() || !isCompany()) {
    header('Location: ../login.php');
    exit;
}

// Get company ID
$user_id = $_SESSION['user_id'];
$company_id = getCompanyIdByUserId($user_id);

// Fetch jobs for the company
$sql = "SELECT * FROM jobs WHERE company_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle job update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_job'])) {
    $job_id = $_POST['job_id'];
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    $sql = "UPDATE jobs SET title = ?, description = ?, location = ?, salary = ?, category = ? WHERE job_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sssssi", $title, $description, $location, $salary, $category, $job_id);
    if ($stmt->execute()) {
        header('Location: jobs.php');
        exit;
    } else {
        $error = "Error updating job: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Jobs</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="../assets/index/assert/favicon.ico" />
    
    <!-- Bootstrap Icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />
    
    <!-- SimpleLightbox plugin CSS-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css" rel="stylesheet" />
    
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../assets/index/css/styles.css" rel="stylesheet" />

    <style>
        /* Custom styles for aligning footer to bottom */
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: 'Merriweather Sans', sans-serif;
            color: #000;
            min-height: 100vh; /* Ensure full viewport height */
        }

        main {
            flex: 1;
            padding-top: 60px; /* Adjust as needed for fixed navbar */
        }

        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        .navbar {
            background-color: #fff;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #000;
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: #ffa500;
        }

        .card {
            height: 250px;
            transition: all 0.3s ease;
        }

        .card:hover {
            background-color: #f4623a;
            color: #fff;
            transform: translateY(-10px);
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include '../templates/com_navbar.php'; ?>

    <div class="container mt-5">
        <h1>Company Jobs</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Salary</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['description']); ?></td>
                        <td><?php echo htmlspecialchars($job['location']); ?></td>
                        <td><?php echo htmlspecialchars($job['salary']); ?></td>
                        <td><?php echo htmlspecialchars($job['category']); ?></td>
                        <td>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editJobModal" data-job-id="<?php echo $job['job_id']; ?>" data-title="<?php echo htmlspecialchars($job['title']); ?>" data-description="<?php echo htmlspecialchars($job['description']); ?>" data-location="<?php echo htmlspecialchars($job['location']); ?>" data-salary="<?php echo htmlspecialchars($job['salary']); ?>" data-category="<?php echo htmlspecialchars($job['category']); ?>">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Job Modal -->
    <div class="modal fade" id="editJobModal" tabindex="-1" aria-labelledby="editJobModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="jobs.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editJobModalLabel">Edit Job</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-job-id" name="job_id">
                        <div class="mb-3">
                            <label for="edit-title" class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="edit-title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-description" class="form-label">Job Description</label>
                            <textarea class="form-control" id="edit-description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit-location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="edit-location" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-salary" class="form-label">Salary</label>
                            <input type="text" class="form-control" id="edit-salary" name="salary" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="edit-category" name="category" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_job" class="btn btn-primary">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs5-toggle-switch/dist/bs5-toggle-switch.min.js"></script>
    <script src="../assets/index/js/scripts.js"></script>
    <script>
        const editJobModal = document.getElementById('editJobModal');
        editJobModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const jobId = button.getAttribute('data-job-id');
            const title = button.getAttribute('data-title');
            const description = button.getAttribute('data-description');
            const location = button.getAttribute('data-location');
            const salary = button.getAttribute('data-salary');
            const category = button.getAttribute('data-category');

            document.getElementById('edit-job-id').value = jobId;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-location').value = location;
            document.getElementById('edit-salary').value = salary;
            document.getElementById('edit-category').value = category;
        });
    </script>
</body>
</html>
