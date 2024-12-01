<?php 
  error_reporting(E_ALL);	
  ini_set('display_errors', 1);
  
  if(session_status() === PHP_SESSION_NONE) session_start();
  require('db.php');

  $username = $_SESSION['username'];

  if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
  }

  $sql = "SELECT * FROM users WHERE username='$username'";
  $result = $con->query($sql);
  if(!empty($username)){
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $pers_no = $row["Pers_No"];
      $Participant_Name = $row["Participant_Name"];
    } else {
      echo "User not found.";
      exit();
    }
  }

  $sql = "SELECT * FROM event_participants WHERE Participant_Name='$Participant_Name'";
  if (!empty($Participant_Name)) {
    // Retrieve the event_id from the event_participants table
    $sql = "SELECT event_id FROM event_participants WHERE Participant_Name='$Participant_Name'";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $event_id = $row["event_id"];
  
      // Query the events table and display the events on the calendar
      $sql = "SELECT * FROM events WHERE evt_id='$event_id'";
      $result = $con->query($sql);
  
      $query = "SELECT * FROM events
      INNER JOIN event_participants ON events.evt_id = event_participants.event_id
      WHERE event_participants.Participant_Name = '$Participant_Name'";
      $result = mysqli_query($con, $query);
      $selectedValues = array(); // Initialize the array

      if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
          array_push($selectedValues, $row['Participant_Name']);
        }
      } else {
        die("Query execution failed: " . mysqli_error($con));
      }
    }
  }
  
  mysqli_close($con);
?>
<!DOCTYPE html>
<html>

<head>
  <title>Course Calendar</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">
  <meta name="disable-source-maps" content="true">

  <link rel="icon" type="image/png" href="favicon.png">

  <!-- JS + CSS -->
  <script src="4b-calendar-readonly.js"></script>
  <script src="jQuery.js"></script>
  <script src="select2.js"></script>
  <script>
    $(document).ready(function () {
      $('.select2').select2();
    });

    $(document).ready(function () {
      $('.select2-search__field').css('width', '100%');
    });
  </script>
  <link rel="stylesheet" href="4c-calendar.css">
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="select2.css" />
</head>

<body>
  <?php
    // (A) DAYS MONTHS YEAR
    $months = [
      1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 
      11 => "November", 12 => "December"
    ];
    $monthNow = date("m");
    $yearNow = date("Y"); ?>

  <!-- (B) PERIOD SELECTOR -->
  <div id="calHead">
    <div id="calPeriod">
      <input id="calBack" type="button" class="mi" value="&lt;">
      <select id="calMonth"><?php foreach ($months as $m=>$mth) {
          printf("<option value='%u'%s>%s</option>",
            $m, $m==$monthNow?" selected":"", $mth
          );
        } ?></select>
      <input id="calYear" type="number" value="<?=$yearNow?>">
      <input id="calNext" type="button" class="mi" value="&gt;">
    </div>
    <!--<input id="calAdd" type="button" value="+">-->
  </div>

  <!-- (C) CALENDAR WRAPPER -->
  <div id="calWrap">
    <div id="calDays"></div>
    <div id="calBody"></div>
  </div>

  <!-- (D) EVENT FORM -->
  <dialog id="calForm">
    <form method="dialog">
      <div id="evtCX">X</div>
      <h2 class="evt100">COURSE CALENDAR</h2>
      <div class="evt50">
        <label>Start</label>
        <input id="evtStart" type="date" readonly>
      </div>
      <div class="evt50">
        <label>End</label>
        <input id="evtEnd" type="date" readonly>
      </div>
      <div class="evt50">
        <label>Text Color</label>
        <input id="evtColor" type="color" readonly disabled>
      </div>
      <div class="evt50">
        <label>Background Color</label>
        <input id="evtBG" type="color" readonly disabled>
      </div>
      <div class="evt100">
        <label>Course Name</label>
        <input id="evtTxt" type="text" readonly>
      </div>
      <div class="evt100">
        <label>Participants</label>
        <select id="evtParticipants" class="select2" multiple readonly disabled name="participants[]">
          <?php
            $sql = "SELECT Participant_Name FROM users";
            $result = mysqli_query($con, $sql);
            if ($result) {
              while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="' . $row['Participant_Name'] . '">' . $row['Participant_Name'] . '</option>';
              }
            } else {
              die("Query execution failed: " . mysqli_error($con));
            }
            mysqli_close($con);
          ?>
        </select>
      </div>
      <div class="evt50">
        <label>Course Category</label>
        <select id="evtCourseCategory" readonly disabled>
          <option value="" disabled>Category</option>
          <option value="Non Core">Non Core</option>
          <option value="Core">Core</option>
          <option value="Management">Management</option>
        </select>
      </div>
      <div class="evt50">
        <label>Division</label>
        <input id="evtDivision" type="text" readonly>
      </div>
      <div class="evt50">
        <label>Trg. Location (Venue)</label>
        <input id="evtVenue" type="text" readonly>
      </div>
      <div class="evt50">
        <label>Training Duration (Hours)</label>
        <input id="evtDuration" type="number" min="1" readonly disabled>
      </div>
      <div class="evt50">
        <label>Category Area</label>
        <input id="evtCategoryArea" type="text" readonly>
      </div>
      <div class="evt50">
        <input type="hidden" id="evtID">
        <input type="button" id="evtDel" value="Delete" hidden>
        <input type="submit" id="evtSave" value="Save" hidden>
      </div>
    </form>
  </dialog><br>

  <?php echo '<script>var loggedInUser = "'.$pers_no.'";</script>'; ?>
  <button class="dashb"><a href="dashboard.php">Dashboard</a></button>&nbsp;&nbsp;&nbsp;
  <button class="dashb"><a href="logout.php">Logout</a></button>
</body>

</html>