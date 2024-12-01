<?php
    session_start();

    require('db.php');

    // Retrieve username from session variable or input
    $adminusername = isset($_SESSION['adminusername']) ? $_SESSION['adminusername'] : (isset($_POST['adminusername']) ? $_POST['adminusername'] : null);

    // Get user's Pers_No from URL parameter
    $Pers_No = $_GET['id'];

    // Retrieve user's information from the database
    $query = "SELECT * FROM users WHERE Pers_No='$Pers_No'";
    $result = mysqli_query($con, $query);
    $user = mysqli_fetch_assoc($result);

    // Retrieve events that the user has registered for
    $query = "SELECT events.* FROM events 
    JOIN event_participants ON events.evt_id = event_participants.event_id 
    WHERE event_participants.Participant_Name = '{$user['Participant_Name']}'";
    $result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Course List</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
</head>

<body>
    <br>
    <h1 class="login-title">Course List for <?php echo $user['Participant_Name']; ?></h1>
    <div class="tablebox">
        <br>
        <table class="list">
            <tr class="updel">
                <th>Start Date</th>
                <th>End Date</th>
                <th>Course Name</th>
                <th>Course Category</th>
                <th>Venue</th>
                <th>Duration</th>
            </tr>
            <?php
                    while($row = mysqli_fetch_array($result)):
                ?>
            <tr>
                <td><?php echo $row['evt_start']; ?></td>
                <td><?php echo $row['evt_end']; ?></td>
                <td><?php echo $row['evt_text']; ?></td>
                <td><?php echo $row['evt_coursecategory']; ?></td>
                <td><?php echo $row['evt_venue']; ?></td>
                <td class="updel"><?php echo $row['evt_duration']; ?></td>
            </tr>
            <?php endwhile;?>
        </table><br>
        <div class="buttonmid">
            <button class="dashb"><a href="export.php?id=<?php echo $Pers_No; ?>" class="dash">Export to
                    CSV</a></button>
            <button class="dashb"><a href="admindashboard.php" class="dash">Admin Dashboard</a></button>&nbsp;
            <button class="dashb"><a href="4a-cal-page.php" class="dash">Calendar</a></button>&nbsp;
            <button class="dashb"><a href="4a-cal-page-readonly.php" class="dash">Calendar Read</a></button>&nbsp;
            <button class="dashb"><a href="adminlogout.php" class="dash">Logout</a></button><br>
        </div>
    </div>
</body>

</html>