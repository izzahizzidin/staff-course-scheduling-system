<?php
    require('db.php');
    //include auth_session.php file on all user panel pages
    include("auth_session.php");

    if(session_status() === PHP_SESSION_NONE) session_start();

    $username = $_SESSION['username'];
    // Retrieve user data from database
    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if(!empty($username)){
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $pers_no = $row["Pers_No"];
            $participant_name = $row["Participant_Name"];
            $persarea_desc = $row["PersArea_Desc"];
            $username = $row["username"];
            $email = $row["email"];
        } else {
            echo "User not found.";
            exit();
        }
    }

    // Retrieve events that the user has registered for
    $query = "SELECT events.* FROM events 
              JOIN event_participants ON events.evt_id = event_participants.event_id 
              WHERE event_participants.Participant_Name = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $participant_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Query failed: " . mysqli_error($con));
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="tablebox">
        <div class="form">
            <p>Hey, <?php echo $_SESSION['username']; ?>!</p>
            <p>You are at user dashboard page.</p>

            <p><strong>Pers No:</strong> <?php echo $pers_no; ?></p>
            <p><strong>Participant Name:</strong> <?php echo $participant_name; ?></p>
            <p><strong>PersArea Desc:</strong> <?php echo $persarea_desc; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
        </div><br>
        <h1 class="login-title">Course List for <?php echo $participant_name; ?></h1>
        <br>
        <table class="list">
            <tr class="updel">
                <th>Start Date</th>
                <th>End Date</th>
                <th>Course Name</th>
                <th>Course Category</th>
                <th>Venue</th>
                <th>Duration</th>
                <th>Status</th>
            </tr>
            <?php
                while($row = mysqli_fetch_array($result)):
                    $evt_end_date = strtotime($row['evt_end']);
                    $current_date = time();
                    if($evt_end_date < $current_date) {
                        $status = 'Attended';
                    } else {
                        $status = 'Not yet Attended';
                    }
            ?>
            <tr>
                <td><?php echo $row['evt_start']; ?></td>
                <td><?php echo $row['evt_end']; ?></td>
                <td><?php echo $row['evt_text']; ?></td>
                <td><?php echo $row['evt_coursecategory']; ?></td>
                <td><?php echo $row['evt_venue']; ?></td>
                <td class="updel"><?php echo $row['evt_duration']; ?></td>
                <td><?php echo $status; ?></td>
            </tr>
            <?php endwhile;?>
        </table><br>
        <div class="buttonmid">
            <button class="dashb"><a href="export-user.php?id=<?php echo $pers_no; ?>" class="dash">Export to
                    CSV</a></button>
            <button class="dashb"><a href="4a-cal-page-readonly.php" class="dash"
                    target="_blank">Calendar</a></button>&nbsp;
            <button class="dashb"><a href="logout.php" class="dash">Logout</a></button>
        </div>

    </div>
</body>

</html>