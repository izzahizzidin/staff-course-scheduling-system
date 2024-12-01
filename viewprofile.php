<?php 
    if(session_status() === PHP_SESSION_NONE) session_start();

    // (A) CHECK LOGIN
    if (!isset($_SESSION['user'])) {
      header("Location: login.php");
      exit();
    }
  
    // (B) CONNECT TO DATABASE
    require "db.php";
  
    // (C) GET USER DATA
    $user = $con->getByID("users", $_GET['Pers_No']);
    if ($user===false) {
      echo "User not found";
      exit();
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>View Profile</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

</head>

<body>
    <h1>View Profile</h1>
    <table>
        <tr>
            <td>Participant Name:</td>
            <td><?= $user['Participant_Name'] ?></td>
        </tr>
        <tr>
            <td>Person Area Desc:</td>
            <td><?= $user['PersArea_Desc'] ?></td>
        </tr>
        <tr>
            <td>Employee Subgroup:</td>
            <td><?= $user['Employee_Subgroup'] ?></td>
        </tr>
        <tr>
            <td>Elit/Protege:</td>
            <td><?= $user['Elit_Protege'] ?></td>
        </tr>
    </table>
    <br />
    <a href="editprofile.php?Pers_No=<?= $user['Pers_No'] ?>">Edit Profile</a>
</body>

</html>