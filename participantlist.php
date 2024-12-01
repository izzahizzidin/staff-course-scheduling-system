<?php
    session_start();
    require('db.php');
    
    // Retrieve username from session variable or input
    $adminusername = isset($_SESSION['adminusername']) ? $_SESSION['adminusername'] : (isset($_POST['adminusername']) ? $_POST['adminusername'] : null);
    
    // Get evt_id from GET parameter and sanitize it
    $evt_id = filter_input(INPUT_GET, 'evt_id', FILTER_SANITIZE_NUMBER_INT);
    
    // Retrieve participants from the event_participants table
    $query = "SELECT participant_name FROM event_participants WHERE event_id = '$evt_id'";
    $result = mysqli_query($con, $query);

    // Retrieve participants info from the users table
    $query = "SELECT pers_no, participant_name, persarea_desc, email FROM users WHERE participant_name IN (SELECT participant_name FROM event_participants WHERE event_id = '$evt_id')";
    $result = mysqli_query($con, $query);

    // Calculate the number of participants
    $num_participants = mysqli_num_rows($result);
    
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Participant List</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
</head>

<body>
    <h1 class="login-title">Participant List</h1>
    <div class="tablebox">
        <p style="text-align:center;">Number of Participants: <?php echo $num_participants; ?></p>
        <table class="list">
            <tr>
                <th>Pers No</th>
                <th>Participant Name</th>
                <th>PersArea Desc</th>
                <th>Email</th>
            </tr>
            <?php while($row = mysqli_fetch_array($result)): ?>
            <tr>
                <td><?php echo $row['pers_no']; ?></td>
                <td><?php echo $row['participant_name']; ?></td>
                <td><?php echo $row['persarea_desc']; ?></td>
                <td><?php echo $row['email']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table><br>
        <button class="search" onclick="goBack()">Back</button>

        <script>
            function goBack() {
                window.history.back();
            }
        </script>
    </div>
</body>

</html>