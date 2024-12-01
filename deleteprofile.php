<?php
    session_start();

    require('db.php');

    if(isset($_GET['id'])){
        $id = $_GET['id'];

        // Check if user confirmed deletion
        if(isset($_GET['confirm']) && $_GET['confirm'] == 'yes'){
            $query = "DELETE FROM users WHERE Pers_No='$id'";
            $result = mysqli_query($con, $query);

            if($result){
                echo "<script>alert('Record deleted successfully!');</script>";
                header('Location: namelist.php');
            }else{
                echo "<script>alert('Error deleting record.');</script>";
                header('Location: namelist.php');
            }
        }else{
            // Show confirmation message
            echo "<script>
            if(confirm('Are you sure you want to delete this record?')){
                window.location.href = 'deleteprofile.php?id=$id&confirm=yes';
            }else{
                window.location.href = 'namelist.php';
            }
            </script>";
        }
    }
?>
