<?php  
require_once '../../core/models.php'; 
require_once '../../core/handleForms.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>

<header class="website-header">
    <h1>FindHire</h1>
</header>

<?php  
    if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

        if ($_SESSION['status'] == "200") {
            echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
        }

        else {
            echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>";    
        }

    }
    unset($_SESSION['message']);
    unset($_SESSION['status']);
?>

<div class="login-container">
    <div class="login-box">
        <header>
            <h1>Register</h1>
        </header>
        <form action="../../core/handleForms.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <label for="role">Role:</label>
            <select name="role" required>
                <option value="Applicant">Applicant</option>
                <option value="HR">HR</option>
            </select>
            <input type="submit" name="insertNewUserBtn">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

</body>
</html>
