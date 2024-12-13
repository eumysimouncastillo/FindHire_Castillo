<?php
require_once '../../core/models.php';

if (!isHR()) {
    redirect('../auth/login.php');
}
$jobPosts = getJobPosts($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Dashboard</title>
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

<!-- Header Section -->
<header class="header">
    <h1>HR Dashboard</h1>
    <p class="welcome-message">Welcome, <?php echo $_SESSION['username']; ?>!</p>
</header>

<!-- Main Content Section -->
<div class="main-content">
    <div class="dashboard-links">
        <a href="jobPosts.php" class="btn secondary-btn">Create Job Posts</a>
        <a href="viewMessages.php" class="btn secondary-btn">View Messages</a>
    </div>

    <!-- Job Posts Table -->
    <div class="section job-posts">
        <h2>Job Posts</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobPosts as $jobPost) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($jobPost['title']); ?></td>
                        <td><?php echo htmlspecialchars($jobPost['description']); ?></td>
                        <td>
                            <a href="manage_applications.php?job_id=<?php echo $jobPost['id']; ?>" class="btn action-btn">Manage Applications</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
