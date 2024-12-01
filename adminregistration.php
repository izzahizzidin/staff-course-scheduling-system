<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Admin Registration</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php
    require('db.php');
    // When form submitted, insert values into the database.
    if (isset($_REQUEST['admin_username'])) {
        // removes backslashes
        $admin_username = stripslashes($_REQUEST['admin_username']);
        //escapes special characters in a string
        $admin_username = mysqli_real_escape_string($con, $admin_username);
        $admin_email    = stripslashes($_REQUEST['admin_email']);
        $admin_email    = mysqli_real_escape_string($con, $admin_email);
        $admin_name    = stripslashes($_REQUEST['admin_name']);
        $admin_name    = mysqli_real_escape_string($con, $admin_name);
        $admin_password = stripslashes($_REQUEST['admin_password']);
        $admin_password = mysqli_real_escape_string($con, $admin_password);
        $admin_create_datetime = date("Y-m-d H:i:s");
        $query    = "INSERT into `admins` (admin_username, admin_password, admin_name, admin_email, admin_create_datetime)
                     VALUES ('$admin_username', '$admin_password', '$admin_name',  '$admin_email', '$admin_create_datetime')";
        $result   = mysqli_query($con, $query);
        if ($result) {
            echo "<div class='form'>
                  <h3>You are registered successfully.</h3><br/>
                  <p class='link'>Click here to <a href='adminlogin.php'>Login</a></p>
                  </div>";
        } else {
            echo "<div class='form'>
                  <h3>Required fields are missing.</h3><br/>
                  <p class='link'>Click here to <a href='adminregistration.php'>registration</a> again.</p>
                  </div>";
        }
    } else {
?>
    <form class="form" action="" method="post">
        <h1 class="login-title">Admin Registration</h1>
        <input type="text" class="login-input" name="admin_name" placeholder="Name" required />
        <input type="text" class="login-input" name="admin_username" placeholder="Username" required />
        <input type="email" class="login-input" name="admin_email" placeholder="Email Address">
        <input type="password" class="login-input" name="admin_password" placeholder="Password">
        <input type="submit" name="submit" value="Register" class="login-button">
        <p class="link">Already have an account? <a href="adminlogin.php">Login here</a></p>
    </form>
    <?php
    }
?>
</body>

</html>