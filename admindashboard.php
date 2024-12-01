<?php
    require('db.php');
    //include auth_session.php file on all user panel pages
    include("adminauth_session.php");

    if(session_status() === PHP_SESSION_NONE) session_start();

    $admin_username = $_SESSION['admin_username'];
    // Retrieve user data from database
    $sql = "SELECT * FROM admins WHERE admin_username='$admin_username'";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $admin_username = $row["admin_username"];
        $admin_name = $row["admin_name"];
        $admin_email = $row["admin_email"];
    } else {
        echo "Admin not found.";
        exit();
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="form">
        <p>Hey, <?php echo $_SESSION['admin_username']; ?>!</p>
        <p>You are at admin dashboard page.</p>

        <p><strong>Username:</strong> <?php echo $admin_username; ?></p>
        <p><strong>Admin Name:</strong> <?php echo isset($admin_name) ? $admin_name : ""; ?></p>
        <p><strong>Email:</strong> <?php echo isset($admin_email) ? $admin_email : ""; ?></p>
        
        <button class="dashb"><a href="4a-cal-page.php" class="dash" >Calendar</a></button>&nbsp;
        <button class="dashb"><a href="courses.php" class="dash" >Course History</a></button>&nbsp;
        <button class="dashb"><a href="search.php" class="dash" >Search</a></button>&nbsp;
        <button class="dashb"><a href="adminlogout.php" class="dash">Logout</a></button>
    </div>
</body>

</html>
