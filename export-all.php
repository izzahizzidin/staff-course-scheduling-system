<?php
    // Start output buffering
    ob_start();

    // Retrieve the data to export
    require('db.php');
    
    // Retrieve courses
    $query = "SELECT * FROM events";
    $result = mysqli_query($con, $query);
    if (!$result) {
        die("Failed to retrieve courses: " . mysqli_error($con));
    }

    // Generate the CSV data
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    if (!empty($data)) {
        $headers = array_keys($data[0]);

        // Generate the CSV file
        $filename = "courselist_" . date("Y-m-d") . ".csv";
        $filepath = "csv/" . $filename;
        $fp = fopen($filepath, 'w');
        if ($fp === false) {
            die("Failed to open file");
        }
        $headers = array_keys($data[0]);
        fputcsv($fp, $headers);
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        // Set headers to download the CSV file
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Output the file to the browser
        readfile($filepath);

        // Redirect to the courses page
        header('Location: courses.php');
        exit();
    } else {
        echo "No data found.";
    }

    // End output buffering and output the buffered content
    ob_end_flush();
?>
