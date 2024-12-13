<?php
require_once '../../core/models.php';

if (!isHR()) {
    redirect('../auth/login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Job Posts</title>
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
    <h1>Create Job Posts</h1>
</header>
<div class="main-content">
    <form action="../../core/handleForms.php" method="POST">
        <label for="title">Job Title:</label>
        <input type="text" name="title" required>
        <label for="description">Job Description:</label>
        <textarea name="description" required></textarea>
        <button type="submit" name="createJob">Create Job Post</button>
    </form>
</div>

<?php  
	if (isset($_SESSION['message'])) {
	    echo "<h1 style='color: #9AA6B2; font-size: 1.5em; text-align: center;'>{$_SESSION['message']}</h1>";
	}
	unset($_SESSION['message']);
?>


</body>
</html>