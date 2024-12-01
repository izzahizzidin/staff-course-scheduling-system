<?php
  if (isset($_POST["req"])) {
    // (A) LOAD LIBRARY
    require "2-cal-lib.php";
    switch ($_POST["req"]) {
      // (B) GET DATES & EVENTS FOR SELECTED PERIOD
      case "get":
        echo json_encode($_CAL->get($_POST["month"], $_POST["year"]));
        break;

      // (C) SAVE EVENT
      case "save":
        $id = $_POST["id"] ?? null;
        $participants = isset($_POST['participants']) ? $_POST['participants'] : [];

        echo $_CAL->save(
          $_POST["start"], 
          $_POST["end"], 
          $_POST["txt"], 
          $participants, // pass in the array of participant IDs
          $_POST["color"], 
          $_POST["bg"], 
          $_POST["coursecategory"] ?? null, 
          $_POST["division"] ?? null, 
          $_POST["venue"] ?? null, 
          $_POST["duration"] ?? null, 
          $_POST["categoryarea"] ?? null, 
          $id) ? "OK" : $_CAL->error;

        break;

      // (D) DELETE EVENT
      case "del":
        echo $_CAL->del($_POST["id"])  ? "OK" : $_CAL->error ;
        break;
    }
  }