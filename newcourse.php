<?php
    // Include database connection code here
    require('db.php');
    if(session_status() === PHP_SESSION_NONE) session_start();

    $admin_username = $_SESSION['admin_username'];

?>
<!DOCTYPE html>
<html>

<head>
    <title>Add Course</title>
    <meta charset="utf-8" />

    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="select2.css" />
    <script src="jQuery.js"></script>
    <script src="select2.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $('.select2-search__field').css('width', '100%');
        });
    </script>
</head>

<body>
    <?php    
        // Check if form has been submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            // Retrieve form data and sanitize
            $start = mysqli_real_escape_string($con, $_POST["evtStart"]);
            $end = mysqli_real_escape_string($con, $_POST["evtEnd"]);
            $color = mysqli_real_escape_string($con, $_POST["evtColor"]);
            $bg = mysqli_real_escape_string($con, $_POST["evtBG"]);
            $txt = mysqli_real_escape_string($con, $_POST["evtTxt"]);
            // Ensure that $participants is always an array
            $participants = is_array($_POST["participants"]) ? $_POST["participants"] : array($_POST["participants"]);
            $coursecategory = mysqli_real_escape_string($con, $_POST["evtCourseCategory"]);
            $division = mysqli_real_escape_string($con, $_POST["evtDivision"]);
            $venue = mysqli_real_escape_string($con, $_POST["evtVenue"]);
            $duration = mysqli_real_escape_string($con, $_POST["evtDuration"]);
            $categoryarea = mysqli_real_escape_string($con, $_POST["evtCategoryArea"]);

            // Convert the array of participants to a comma-separated string
            $participantList = implode(",", $participants);
        
            // Insert form data into database
            $sql = "INSERT INTO `events` (`evt_start`, `evt_end`, `evt_text`, `evt_participants`, `evt_color`, `evt_bg`, `evt_coursecategory`, 
                `evt_division`, `evt_venue`, `evt_duration`, `evt_categoryarea`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sssssssssssssssss", $start, $end, $txt, $participantList, $color, $bg, $coursecategory, $division,
                $venue, $duration, $categoryarea);
            $result = $stmt->execute();

            if (!$result) {
                die("Insertion failed: " . $stmt->error);
            }

            // Get the ID of the newly inserted event
            $id = mysqli_insert_id($con);

            // Loop through each selected participant and insert into event_participants table
            foreach ($participants as $participantName) {
                // Prepare the INSERT statement for event_participants table
                $stmt = $con->prepare("INSERT INTO event_participants (event_id, Participant_Name) VALUES (?, ?)");

                // Bind the event ID and participant Name as parameters
                $stmt->bind_param("is", $id, $participantName);

                // Execute the statement
                $result = $stmt->execute();
            }
                        
            // Perform form validation here
            if ($result) {
                echo "<div class='form'>
                    <h3>The course is added successfully.</h3><br/>
                    <p class='link'><a href='courses.php'>Continue</a></p>
                    </div>";
            } else {
                echo "<div class='form'>
                    <h3>Required fields are missing.</h3><br/>
                    <p class='link'>Click here to <a href='newcourse.php'>add new course</a> again.</p>
                    </div>";
            }
        }else{
    ?>
    <h1 class="login-title">Add Course</h1>
    <form class="form" action="" method="post">
        <label for="evtStart">Start Date</label>
        <input type="date" class="login-input" name="evtStart" required>

        <label for="evtEnd">End Date</label>
        <input type="date" class="login-input" name="evtEnd" required>

        <label for="evtColor">Text Color</label>
        <input type="color" class="login-input" name="evtColor" value="#0071c5" required>

        <label for="evtBG">Background Color</label>
        <input type="color" class="login-input" name="evtBG" value="#f4f4f4" required>

        <label for="evtTxt">Course Name</label>
        <input type="text" class="login-input" name="evtTxt" required>

        <label for="participants">Participants</label>
        <p style="font-style: italic; font-weight: bold; color: #2C4E91;">Note: Only use when inserting new course. If
            changes are needed for the participants, need to re-insert ALL names of the relevant participants.</p>
        <select id="evtParticipants" class="select2" multiple name="participants[]">
            <?php
                $sql = "SELECT Participant_Name FROM users";
                $result = mysqli_query($con, $sql);
                if (!$result) {
                    die("Query execution failed: " . mysqli_error($con));
                }
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['Participant_Name'] . "'>" . $row['Participant_Name'] . "</option>";
                }
            ?>
        </select><br><br>

        <label for="evtCourseCategory">Course Category</label>
        <select class="login-input" name="evtCourseCategory" required>
            <option value="" selected disabled>Category</option>
            <option value="Non Core">Non Core</option>
            <option value="Core">Core</option>
            <option value="Management">Management</option>
        </select>

        <label for="evtDivision">Division</label>
        <input class="login-input" type="text" name="evtDivision" required>

        <label for="evtVenue">Trg. Location (Venue)</label>
        <input class="login-input" type="text" name="evtVenue" required>

        <label for="evtDuration">Training Duration (Hours)</label>
        <input class="login-input" name="evtDuration" type="number" min="1" required>

        <label for="evtCategoryArea">Category Area</label>
        <input class="login-input" type="text" name="evtCategoryArea" required>

        <input type="submit" value="Add Course" class="login-button">&nbsp;&nbsp;&nbsp;
        <button class="login-button"><a href="admindashboard.php">Dashboard</a></button>&nbsp;&nbsp;&nbsp;
        <button class="login-button"><a href="adminlogout.php">Logout</a></button>
    </form>
    <?php
}?>

</body>

</html>