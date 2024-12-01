<?php
    session_start();

    require('db.php');

    if (isset($_GET['Pers_No'])) {
        $Pers_No = $_GET['Pers_No'];
        $query = "SELECT * FROM users WHERE Pers_No='" . $Pers_No . "'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        $row = mysqli_fetch_assoc($result);
        extract($row);
    }

    if (isset($_POST['update'])) {
        $Pers_No = $_POST['Pers_No'];
        $Participant_Name = $_POST['Participant_Name'];
        $PersArea_Desc = $_POST['PersArea_Desc'];
        $Employee_Subgroup = $_POST['Employee_Subgroup'];
        $Elit_Protege = $_POST['Elit_Protege'];

        $query = "UPDATE users SET Participant_Name='" . $Participant_Name . "', PersArea_Desc='" . $PersArea_Desc . "', Employee_Subgroup='" . 
        $Employee_Subgroup . "', Elit_Protege='" . $Elit_Protege . "' WHERE Pers_No='" . $Pers_No . "'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));

        if ($result) {
            header("Location: namelist.php");
        } else {
            echo "Error updating record: " . mysqli_error($con);
        }
    }

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <br>
    <h1 class="login-title">Edit Profile</h1>
    <div class="login-box">
        <br>
        <form class="form" method="POST" action="updateprofile.php">
            <input type="hidden" name="Pers_No" value="<?php echo $row['Pers_No']; ?>">
            <label>Participant Name:</label><br>
            <input type="text" class="login-input" name="Participant_Name"
                value="<?php echo $row['Participant_Name']; ?>">
            <br><br>
            <label>Person Area Desc:</label><br>
            <input type="text" class="login-input" name="PersArea_Desc" value="<?php echo $row['PersArea_Desc']; ?>">
            <br><br>
            <label>Employee Subgroup:</label><br>
            <select class="login-input" name="Employee_Subgroup">
                <option value="" disabled>Employee Subgroup</option>
                <option value="Executives" <?php if($row['Employee_Subgroup'] == "Executives") echo "selected"; ?>>
                    Executives</option>
                <option value="Non Executives"
                    <?php if($row['Employee_Subgroup'] == "Non Executives") echo "selected"; ?>>Non Executives</option>
            </select>
            <br><br>
            <label>Elit/Protege:</label><br>
            <select class="login-input" name="Elit_Protege">
                <option value="" disabled>Elit Protege</option>
                <option value="TM STAFF" <?php if($row['Elit_Protege'] == "TM STAFF") echo "selected"; ?>>TM STAFF
                </option>
                <option value="Protege" <?php if($row['Elit_Protege'] == "Protege") echo "selected"; ?>>
                    Protege</option>
            </select>
            <br><br>
            <button type="submit" name="update" class="login-button">Update</button><br><br>
            
        </form>
        <button class="login-button"><a href="namelist.php">Back</a></button>
    </div>
</body>

</html>