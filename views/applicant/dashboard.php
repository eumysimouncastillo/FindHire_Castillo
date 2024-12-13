<?php
require_once '../../core/models.php';

if (!isApplicant()) {
    redirect('../auth/login.php');
}

$jobPosts = getJobPosts($pdo); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicant Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="navbar-brand">FindHire</div>
    <div class="navbar-links">
        <a href="dashboard.php" class="btn">Dashboard</a>
        <a href="../auth/logout.php" class="btn">Log Out</a>
    </div>
</nav>

<header class="header">
    <h1>APPLICANT Dashboard</h1>
    <p class="welcome-message">Welcome, <?php echo $_SESSION['username']; ?>!</p>
</header>

<div class="main-content">
    <div class="dashboard-links">
        <a href="applications.php" class="btn secondary-btn">Your Applications</a>
        <a href="viewMessages.php" class="btn secondary-btn">Your Messages</a>
    </div>


<?php  
	if (isset($_SESSION['message'])) {
	    echo "<h1 style='color: #9AA6B2; font-size: 1.5em; text-align: center;'>{$_SESSION['message']}</h1>";
	}
	unset($_SESSION['message']);
?>

<div class="section">
        <h2>Available Job Listings</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $jobPosts = getAllJobPosts($pdo); 
                foreach ($jobPosts as $job) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['id']); ?></td>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['description']); ?></td>
                        <td>
                        <a href="applyJob.php?job_id=<?php echo $job['id']; ?>" class="btn">Apply</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


</body>
</html>
