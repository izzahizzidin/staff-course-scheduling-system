<?php 
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  
  if(session_status() === PHP_SESSION_NONE) session_start();
  require('db.php');
  
  $admin_username = $_SESSION['admin_username'];
  
  if(!isset($_SESSION['admin_username'])) {
    header("Location: adminlogin.php");
    exit;
  }
  
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
  <script src="4b-calendar.js"></script>
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
    <input id="calAdd" type="button" value="+">
  </div>

  <!-- (C) CALENDAR WRAPPER -->
  <div id="calWrap">
    <div id="calDays"></div>
    <div id="calBody"></div>
  </div>

  <!-- (D) EVENT FORM -->
  <dialog id="calForm">
    <form method="dialog" autocomplete="on">
      <div id="evtCX">X</div>
      <h2 class="evt100">COURSE CALENDAR</h2>
      <div class="evt50">
        <label>Start</label>
        <input id="evtStart" type="date" required>
      </div>
      <div class="evt50">
        <label>End</label>
        <input id="evtEnd" type="date" required>
      </div>
      <div class="evt50">
        <label>Text Color</label>
        <input id="evtColor" type="color" value="#000000" required>
      </div>
      <div class="evt50">
        <label>Background Color</label>
        <input id="evtBG" type="color" value="#ffdbdb" required>
      </div>
      <div class="evt100">
        <label>Course Name</label>
        <input id="evtTxt" type="text" required>
      </div>
      <div class="evt100">
        <label>Participants</label>
        <p style="font-style: italic; color: #2C4E91;">Note: Only use when inserting new course. If changes are needed
          for the participants, need to re-insert ALL names of the relevant participants.</p>
        <select id="evtParticipants" class="select2" multiple required name="participants[]">
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
        <select id="evtCourseCategory" required>
          <option value="" selected disabled>Category</option>
          <option value="Non Core">Non Core</option>
          <option value="Core">Core</option>
          <option value="Management">Management</option>
        </select>
      </div>
      <div class="evt50">
        <label>Division</label>
        <input id="evtDivision" type="text" required>
      </div>
      <div class="evt50">
        <label>Trg. Location (Venue)</label>
        <input id="evtVenue" type="text" required>
      </div>
      <div class="evt50">
        <label>Training Duration (Hours)</label>
        <input id="evtDuration" type="number" min="1" required>
      </div>
      <div class="evt50">
        <label>Category Area</label>
        <input id="evtCategoryArea" type="text" required>
      </div>
      <div class="evt50">
        <input type="hidden" id="evtID">
        <input type="button" id="evtDel" value="Delete">
        <input type="submit" id="evtSave" value="Save">
      </div>
      <strong class="evt100" style="color: #2C4E91;">Please reload the webpage after inserting/updating/deleting the
        course form.</strong>
    </form>
  </dialog><br>
  <button class="dashb"><a href="admindashboard.php">Dashboard</a></button>&nbsp;&nbsp;&nbsp;
  <button class="dashb"><a href="adminlogout.php">Logout</a></button>
</body>

</html>