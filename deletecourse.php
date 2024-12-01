<?php
    session_start();
    require('db.php');
    
    if(isset($_GET['evt_id'])) {
        $evt_id = $_GET['evt_id'];
    
        // Check if user confirmed deletion
        if(isset($_GET['confirm']) && $_GET['confirm'] == 'yes'){
            // Delete related records in event_participants table first
            $query1 = "DELETE FROM event_participants WHERE event_id='$evt_id'";
            $result1 = mysqli_query($con, $query1);
    
            // Delete record in events table
            $query2 = "DELETE FROM events WHERE evt_id='$evt_id'";
            $result2 = mysqli_query($con, $query2);
    
            if($result1 && $result2) {
                echo "<script>alert('Record deleted successfully!');</script>";
                header('Location: courses.php');
            } else {
                echo "<script>alert('Error deleting record.');</script>";
                header('Location: courses.php');
            }
        } else {
            // Show confirmation message
            echo "<script>
            if(confirm('Are you sure you want to delete this record?')){
                window.location.href = 'deletecourse.php?evt_id=$evt_id&confirm=yes';
            } else {
                window.location.href = 'courses.php';
            }
            </script>";
        }
    }
    
?>