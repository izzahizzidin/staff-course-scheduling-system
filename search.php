<?php
    session_start();

    require('db.php');

    // Retrieve username from session variable or input
    $adminusername = isset($_SESSION['adminusername']) ? $_SESSION['adminusername'] : (isset($_POST['adminusername']) ? $_POST['adminusername'] : null);

    if(isset($_POST['submitsearch'])){
        $searchbox = $_POST['searchbox'];
        $query = "SELECT * FROM users WHERE CONCAT(`Pers_No`, `Participant_Name`) LIKE '%".$searchbox."%'";
        $searchresult = filterTable($query);
        }else{
        $query = "SELECT * FROM users";
        $searchresult = filterTable($query);
    }

    function filterTable($query){
        $con = mysqli_connect("localhost", "root", "", "adatabase");
        $filterResult = mysqli_query($con, $query);
        return $filterResult;
    }

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Search</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<body>
    <br>
    <h1 class="login-title">Search</h1>
    <div class="tablebox">
        <div class="buttonmid">
            <button class="dashb"><a href="admindashboard.php" class="dash">Admin Dashboard</a></button>&nbsp;
            <button class="dashb"><a href="4a-cal-page.php" class="dash">Calendar</a></button>&nbsp;
            <button class="dashb"><a href="adminlogout.php" class="dash">Logout</a></button>
        </div>
        <br>
        <form action="search.php" method="POST">
            <input type="text" class="search-input" name="searchbox" placeholder="Search Pers_No/Name: ">
            <button type="submit" class="search" name="submitsearch"><i class="las la-search"></i></button>
        </form><br>
        <table class="namelist">
            <tr>
                <th>Person No.</th>
                <th>Participant Name</th>
                <th>Person Area Desc</th>
                <th>Employee Subgroup</th>
                <th>Elit/Protege</th>
                <th>Update/Delete</th>
            </tr>
            <?php
                    while($row = mysqli_fetch_array($searchresult)):
                ?>
            <tr>
                <td class="updel"><?php echo $row['Pers_No']; ?></td>
                <td><a target="_blank"
                        href="courselist.php?id=<?php echo $row['Pers_No']; ?>"><?php echo $row['Participant_Name']; ?></a>
                </td>
                <td>
                    <?php echo $row['PersArea_Desc']; ?>
                </td>
                <td>
                    <?php echo $row['Employee_Subgroup']; ?>
                </td>
                <td>
                    <?php echo $row['Elit_Protege']; ?>
                </td>
                <td class="updel"><a href="editprofile.php?Pers_No=<?php echo $row['Pers_No']; ?>" target="_blank"><span
                            class="las la-sync"></span></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="deleteprofile.php?id=<?php echo $row['Pers_No']; ?>"><span class="las la-trash"></span></a>
                </td>
            </tr>
            <?php endwhile;?>
        </table><br>

    </div>
</body>

</html>