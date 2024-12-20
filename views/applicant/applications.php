<?php
require_once '../../core/models.php';
require_once '../../core/handleForms.php';

if (!isApplicant()) {
    redirect('../auth/login.php');
}


$applicantId = $_SESSION['user_id'];

// Initialize $selectedHR to avoid undefined variable warning
$selectedHR = null;

// Fetch HR representatives
$stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'HR'");
$hrUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Safely handle the 'hr_id' parameter from GET request
$selectedHR = $_GET['hr_id'] ?? null;

// Fetch messages with a specific HR
$messages = $selectedHR ? getMessages($applicantId, $selectedHR) : [];

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = $_POST['hr_id'];
    $content = trim($_POST['content']);

    if (!empty($content)) {
        sendMessage($applicantId, $receiverId, $content);
        $success = "Message sent successfully.";
        $messages = getMessages($applicantId, $receiverId); // Refresh messages
    } else {
        $error = "Message content cannot be empty.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Follow Up on Application</title>
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
    <h1>Your Applications</h1>
</header>

<div class="main-content">
        <h2>Your Applications</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Job Title</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $applications = getUserApplications($pdo, $_SESSION['user_id']); 
                foreach ($applications as $application) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application['applicationID']); ?></td>
                        <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($application['status']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<br>
    <div class="main-content">
        <h1>Message HR</h1>

        <?php if (!empty($error)): ?>
            <p class="error"> <?= htmlspecialchars($error) ?> </p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"> <?= htmlspecialchars($success) ?> </p>
        <?php endif; ?>

        <form method="GET">
            <div class="form-group">
                <label for="hr_id">HR Representative:</label>
                <select name="hr_id" id="hr_id" required onchange="this.form.submit()">
                    <option value="">Select HR</option>
                    <?php foreach ($hrUsers as $hr): ?>
                        <option value="<?= $hr['id'] ?>" <?= $selectedHR == $hr['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($hr['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($selectedHR): ?>
            <div>
                <h2>Messages with <?= htmlspecialchars($hrUsers[array_search($selectedHR, array_column($hrUsers, 'id'))]['username']) ?></h2>
                <div class="messages">
                    <?php foreach ($messages as $msg): ?>
                        <p>
                            <strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong>
                            <?= nl2br(htmlspecialchars($msg['content'])) ?>
                            <small>(<?= htmlspecialchars($msg['sent_at']) ?>)</small>
                        </p>
                    <?php endforeach; ?>
                </div>
                <form method="POST">
                    <input type="hidden" name="hr_id" value="<?= $selectedHR ?>">
                    <div class="form-group">
                        <textarea name="content" placeholder="Type your message here..." required></textarea>
                    </div>
                    <button type="submit" class="btn">Send Follow-Up</button>
                </form>
            </div>
        <?php endif; ?>

        
    </div>

</body>
</html>