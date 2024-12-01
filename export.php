<?php
    // Retrieve the data to export
    require('db.php');

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

    // Generate the CSV data
    $filename = "courselist_" . $user['Pers_No'] . ".csv";
    $filepath = "csv/" . $filename;
    $fp = fopen($filepath, 'w');
    if ($fp === false) {
        die("Failed to open file");
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    if (!empty($data)) {
        $headers = array_keys($data[0]);
        fputcsv($fp, $headers);
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        // Output the file to the browser
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        readfile($filepath);

        // Redirect to the course list page
        header('Location: courselist.php?id=' . $user['Pers_No']);
        exit();
    } else {
        echo "No data found.";
    }

?>