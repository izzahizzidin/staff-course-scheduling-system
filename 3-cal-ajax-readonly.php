<?php
  if (isset($_POST["req"])) {
    // (A) LOAD LIBRARY
    require "2-cal-lib-readonly.php";
    switch ($_POST["req"]) {
      // (B) GET DATES & EVENTS FOR SELECTED PERIOD
      case "get":
        echo json_encode($_CAL->get($_POST["month"], $_POST["year"]));
        break;

      // (C) SAVE EVENT - READ-ONLY VERSION
      case "save":
        echo "Sorry, this is a read-only version.";
        break;

      // (D) DELETE EVENT - READ-ONLY VERSION
      case "del":
        echo "Sorry, this is a read-only version.";
        break;
    }
  }
