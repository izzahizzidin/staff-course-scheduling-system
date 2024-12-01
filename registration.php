<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Registration</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php
    require('db.php');
    // When form submitted, insert values into the database.
    if (isset($_REQUEST['username'])) {
        // removes backslashes
        $pers_no = stripslashes($_REQUEST['Pers_No']);
        //escapes special characters in a string
        $pers_no = mysqli_real_escape_string($con, $pers_no);
        
        $participant_name = stripslashes($_REQUEST['Participant_Name']);
        $participant_name = mysqli_real_escape_string($con, $participant_name);
        $persarea_desc = stripslashes($_REQUEST['PersArea_Desc']);
        $persarea_desc = mysqli_real_escape_string($con, $persarea_desc);
        $employee_subgroup = stripslashes($_REQUEST['Employee_Subgroup']);
        $employee_subgroup = mysqli_real_escape_string($con, $employee_subgroup);
        $elit_protege = stripslashes($_REQUEST['Elit_Protege']);
        $elit_protege = mysqli_real_escape_string($con, $elit_protege);
        $username = stripslashes($_REQUEST['username']);
        $username = mysqli_real_escape_string($con, $username);
        $email    = stripslashes($_REQUEST['email']);
        $email    = mysqli_real_escape_string($con, $email);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        $create_datetime = date("Y-m-d H:i:s");

        $query    = "INSERT into `users` (Pers_No, Participant_Name, PersArea_Desc, Employee_Subgroup, Elit_Protege, username, password, email, create_datetime)
                     VALUES ('$pers_no', '$participant_name', '$persarea_desc', '$employee_subgroup', '$elit_protege', '$username', '$password', '$email', '$create_datetime')";
        $result   = mysqli_query($con, $query);
        if ($result) {
            echo "<div class='form'>
                  <h3>You are registered successfully.</h3><br/>
                  <p class='link'>Click here to <a href='login.php'>Login</a></p>
                  </div>";
        } else {
            echo "<div class='form'>
                  <h3>Required fields are missing.</h3><br/>
                  <p class='link'>Click here to <a href='registration.php'>registration</a> again.</p>
                  </div>";
        }
    } else {
?>
    <h1 class="login-title">Registration</h1>
    <form class="form" action="" method="post">

        <label>Pers No</label>
        <input type="text" class="login-input" name="Pers_No" placeholder="Pers No" required />
        <label>Participant Name</label>
        <input type="text" class="login-input" name="Participant_Name" placeholder="Participant Name" required />
        <label>PersArea Desc</label>
        <input type="text" class="login-input" name="PersArea_Desc" placeholder="PersArea Desc" required />
        <label>Employee Subgroup</label>
        <select class="login-input" name="Employee_Subgroup" required>
            <option value="" selected disabled>Employee Subgroup</option>
            <option value="Executives">Executives</option>
            <option value="Non Executives">Non Executives</option>
        </select>
        <label>Elit Protege</label>
        <select class="login-input" name="Elit_Protege" required>
            <option value="" selected disabled>Elit Protege</option>
            <option value="TM STAFF">TM STAFF</option>
            <option value="Protege">Protege</option>
        </select>
        <label>Username</label>
        <input type="text" class="login-input" name="username" placeholder="Username" required />
        <label>Email Address</label>
        <input type="email" class="login-input" name="email" placeholder="Email Address">
        <label>Password</label>
        <input type="password" class="login-input" name="password" placeholder="Password">
        <input type="submit" name="submit" value="Register" class="login-button">
        <p class="link"><a href="login.php">Login here</a></p>
    </form>
    <?php
    }
?>
</body>

</html>