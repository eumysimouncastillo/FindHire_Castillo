<?php
require_once '../../core/models.php';

if (!isHR()) {
    redirect('../auth/login.php');
}

$jobID = $_GET['job_id'] ?? ''; 
if (empty($jobID)) {
    header("Location: manage_HR_applicant.php");
    exit;
}

// fetch job details
$jobPost = getJobPostById($pdo, $jobID);
if (!$jobPost) {
    header("Location: manage_HR_applicant.php");
    exit;
}

// handle application status 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateStatus'])) {
    $applicationID = $_POST['applicationID'];
    $newStatus = $_POST['status'];

    if (updateApplicationStatus($pdo, $applicationID, $newStatus)) {
        $_SESSION['message'] = "Application status updated successfully!";
        header("Location: manage_applications.php?job_id=$jobID");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update application status.";
    }
}

$applications = getApplicationsByJobId($pdo, $jobID);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Applications</title>
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

<div class="main-content">
    <h1>Manage Applications for Job: <?php echo htmlspecialchars($jobPost['title']); ?></h1>

    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class='message'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <div class="section job-posts">
        <h2>Applications</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Applicant ID</th>
                    <th>Applicant Name</th>
                    <th>Application Status</th>
                    <th>Resume</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application['applicant_id']); ?></td>
                        <td><?php echo htmlspecialchars($application['username']); ?></td>
                        <td><?php echo htmlspecialchars($application['application_status']); ?></td>
                        <td>
                            <?php if (!empty($application['resume'])) { ?>
                                <a href="findhire/../../../uploads/<?php echo htmlspecialchars($application['resume']); ?>" target="_blank">View Resume</a>
                            <?php } else { ?>
                                No resume uploaded
                            <?php } ?>
                        </td>
                        <td><?php echo nl2br(htmlspecialchars($application['application_message'])); ?></td>
                        <td>
                            <form action="manage_applications.php?job_id=<?php echo $jobID; ?>" method="POST">
                                <input type="hidden" name="applicationID" value="<?php echo htmlspecialchars($application['application_id']); ?>">
                                <select name="status" class="form-control" required>
                                    <option value="Pending" <?php echo ($application['application_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Accepted" <?php echo ($application['application_status'] == 'Accepted') ? 'selected' : ''; ?>>Accepted</option>
                                    <option value="Rejected" <?php echo ($application['application_status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                                <button type="submit" name="updateStatus" class="btn secondary-btn">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
