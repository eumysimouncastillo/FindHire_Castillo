<?php
require_once '../../core/models.php'; // Include the functions file

if (!isApplicant()) {
    redirect('../auth/login.php');
}

// Check if job_id is passed
if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
    die('Job ID not provided. Please go back to the dashboard.');
}

$jobId = intval($_GET['job_id']); // Sanitize the input
$job = getJobPostById($pdo, $jobId); // Fetch the specific job post

if (!$job) {
    die('Job not found. Please go back to the dashboard.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Job</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">FindHire</div>
    <div class="navbar-links">
        <a href="dashboard.php" class="btn">Dashboard</a>
        <a href="../auth/logout.php" class="btn">Log Out</a>
    </div>
</nav>

<header>
    <h1>Apply for Job</h1>
</header>

<div class="section">
    <h2>Job Details</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Job ID</th>
                <th>Title</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($job['id']); ?></td>
                <td><?php echo htmlspecialchars($job['title']); ?></td>
                <td><?php echo htmlspecialchars($job['description']); ?></td>
            </tr>
        </tbody>
    </table>
    <form action="../../core/handleForms.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="jobID" value="<?php echo htmlspecialchars($job['id']); ?>">
        <input type="hidden" name="applicantID" value="<?php echo $_SESSION['user_id']; ?>">
        <label for="message">How does your experience and skill set align with the requirements of this role?</label>
        <textarea name="message" id="message" class="form-control" required></textarea>
        <label for="resume">Upload Resume (PDF):</label>
        <input type="file" name="resume" id="resume" class="form-control" accept="application/pdf" required>
        <button type="submit" name="applyToJobBtn" class="btn">Apply</button>
    </form>
</div>

<?php  
	if (isset($_SESSION['message'])) {
	    echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
	}
	unset($_SESSION['message']);
?>

</body>
</html>
