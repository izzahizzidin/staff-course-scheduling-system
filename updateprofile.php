<?php
    session_start();

    require('db.php');

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
