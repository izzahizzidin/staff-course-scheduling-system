<?php
    session_start();

    require('db.php');

    // Retrieve username from session variable or input
    $adminusername = isset($_SESSION['adminusername']) ? $_SESSION['adminusername'] : (isset($_POST['adminusername']) ? $_POST['adminusername'] : null);

    // Retrieve information from the database
    $query = "SELECT * FROM events";
    $result = mysqli_query($con, $query);

    if(isset($_POST['submitsearch'])){
        $searchbox = $_POST['searchbox'];
        $query = "SELECT * FROM events WHERE CONCAT(`evt_start`, `evt_end`,`evt_text`, `evt_coursecategory`, `evt_division`, `evt_venue`,
            `evt_categoryarea`) 
        LIKE '%".$searchbox."%'";
        $searchresult = filterTable($query);
        }else{
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
    <title>Course List</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
</head>

<body>
    <br>
    <h1 class="login-title">Course List</h1>
    <div class="tablebox">
        <div class="buttonmid">
            <button class="dashb"><a href="export-all.php" class="dash">Export to
                    CSV</a></button>&nbsp;
            <button class="dashb"><a href="newcourse.php" class="dash">Add New Course</a></button>&nbsp;
            <button class="dashb"><a href="admindashboard.php" class="dash">Admin Dashboard</a></button>&nbsp;
            <button class="dashb"><a href="4a-cal-page.php" class="dash">Calendar</a></button>&nbsp;
            <button class="dashb"><a href="adminlogout.php" class="dash">Logout</a></button>
        </div>
        <br>
        <form action="courses.php" method="POST">
            <input type="text" class="search-input" name="searchbox" placeholder="Search: ">
            <button type="submit" class="search" name="submitsearch"><i class="las la-search"></i></button>
        </form><br>
        <table class="list">
            <tr class="updel">
                <th>Start Date</th>
                <th>End Date</th>
                <th>Course Name</th>
                <th>Course Category</th>
                <th>Number of Participants</th>
                <th>Division</th>
                <th>Trg. Location (Venue)</th>
                <th>Training Duration (Hours)</th>
                <th>Category Area</th>
                <th>Event Status</th>
                <th>Delete</th>
            </tr>
            <?php while($row = mysqli_fetch_array($searchresult)): ?>
            <?php
                $evt_end_date = strtotime($row['evt_end']);
                $current_date = time();

                if($evt_end_date < $current_date) {
                    $status = 'Completed';
                } else {
                    $status = 'Ongoing';
                }

                // Retrieve the number of participants for the event
                $evt_id = $row['evt_id'];
                $query = "SELECT COUNT(*) AS num_participants FROM event_participants WHERE event_id = '$evt_id'";
                $result = mysqli_query($con, $query);
                $num_participants = mysqli_fetch_assoc($result)['num_participants'];
            ?>
            <tr>
                <td><?php echo $row['evt_start']; ?></td>
                <td><?php echo $row['evt_end']; ?></td>
                <td style="word-wrap: normal;"><a href="participantlist.php?evt_id=<?php echo $row['evt_id']; ?>"><?php echo $row['evt_text']; ?>
                </td>
                <td><?php echo $row['evt_coursecategory']; ?></td>
                <td class="updel"><?php echo $num_participants; ?></td>
                <td><?php echo $row['evt_division']; ?></td>
                <td><?php echo $row['evt_venue']; ?></td>
                <td class="updel"><?php echo $row['evt_duration']; ?></td>
                <td><?php echo $row['evt_categoryarea']; ?></td>
                <td><?php echo $status; ?></td>
                <td class="updel">
                    <a href="deletecourse.php?evt_id=<?php echo $row['evt_id']; ?>"><span
                            class="las la-trash"></span></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <br>
    </div>
</body>

</html>