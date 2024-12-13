<?php  
require_once '../../core/models.php'; 
require_once '../../core/handleForms.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>

<header class="website-header">
    <h1>FindHire</h1>
</header>

<?php  
    if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

        if ($_SESSION['status'] == "200") {
            echo "<h1 style='color: #9AA6B2; font-size: 1.5em; text-align: center;'>{$_SESSION['message']}</h1>";
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
            <h1>Login</h1>
        </header>
        <form action="../../core/handleForms.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <input type="submit" name="loginUserBtn">
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>



</body>
</html>
