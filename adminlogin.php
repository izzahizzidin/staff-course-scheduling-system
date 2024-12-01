<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
<?php
    require('db.php');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_regenerate_id();
    // When form submitted, check and create user session.
    if (isset($_POST['admin_username'])) {
        $admin_username = stripslashes($_REQUEST['admin_username']);    // removes backslashes
        $admin_username = mysqli_real_escape_string($con, $admin_username);
        $admin_password = stripslashes($_REQUEST['admin_password']);
        $admin_password = mysqli_real_escape_string($con, $admin_password);
        // Check if user exists in the database
        $stmt = $con->prepare("SELECT * FROM admins WHERE admin_username=? AND admin_password=?");
        $stmt->bind_param("ss", $admin_username, $admin_password);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = mysqli_num_rows($result);
        if ($rows == 1) {
            $_SESSION['admin_username'] = $admin_username;
            // Redirect to admin dashboard page
            header("Location: admindashboard.php");
        } else {
            echo "<div class='form'>
                  <h3>Incorrect Username/password.</h3><br/>
                  <p class='link'>Click here to <a href='adminlogin.php'>Login</a> again.</p>
                  </div>";
        }
    } else {
?>
    <form class="form" method="post" name="login">
        <h1 class="login-title">Admin Login</h1>
        <input type="text" class="login-input" name="admin_username" placeholder="Username" autofocus="true" />
        <input type="password" class="login-input" name="admin_password" placeholder="Password" />
        <input type="submit" value="Login" name="submit" class="login-button" />
        <p class="link"><a href="adminregistration.php">Register Now</a></p>
    </form>
    <?php
    }
?>
</body>

</html>