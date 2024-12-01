<?php
class Calendar {
  // (A) CONSTRUCTOR - CONNECT TO DATABASE
  private $pdo = null;
  private $stmt = null;
  public $error = "";
  function __construct () {
    try {
      $this->pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }

  // (B) DESTRUCTOR - CLOSE DATABASE CONNECTION
  function __destruct () {
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  // (C) HELPER - RUN SQL QUERY
  public function query($sql, $data=null): PDOStatement {
    $this->stmt = $this->pdo->prepare($sql);
    $this->stmt->execute($data);
    return $this->stmt;
  }

  // (D) SAVE EVENT
  function save ($start, $end, $txt, $participants, $color, $bg, $coursecategory, $division,
  $venue, $duration, $categoryarea, $id=null) {
    // (D1) START & END DATE CHECK
    if (strtotime($end) < strtotime($start)) {
      $this->error = "End date cannot be earlier than start date";
      return false;
    }
    
    // (D2) VALIDATE VALUES
    if ($start === null || $end === null || $txt === null || $color === null || $bg === null || $coursecategory === null
      || $division === null || $venue === null || $duration === null || $categoryarea === null) {
      throw new Exception('All values are required');
    }

    // (D3) CHECK FOR DUPLICATE EVENT
    $sql = "SELECT * FROM `events` WHERE ((`evt_start` <= ? AND `evt_end` >= ?) 
        OR (`evt_start` >= ? AND `evt_start` <= ?)
        OR (`evt_end` >= ? AND `evt_end` <= ?))
        AND `evt_id` <> ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$start, $end, $start, $end, $start, $end, $id]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
      $this->error = "An event already exists during this time period";
      return false;
    }

    // Check if the event_id value is valid
    if ($id != null) {
      $sql = "SELECT COUNT(*) FROM `events` WHERE `evt_id`=?";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$id]);
      $count = $stmt->fetchColumn();
      if ($count == 0) {
        $this->error = "Invalid event ID";
        return false;
      }
    }

    // (D4) RUN SQL WITH TRANSACTION
    try {
      // Begin transaction
      $this->pdo->beginTransaction();
  
      if ($id == null) {
        $sql = "INSERT INTO `events` (`evt_start`, `evt_end`, `evt_text`, `evt_participants`, `evt_color`, `evt_bg`, `evt_coursecategory`, `evt_division`, 
                `evt_venue`, `evt_duration`, `evt_categoryarea`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $data = [$start, $end, $txt, $participants, $color, $bg, $coursecategory, $division, $venue,
                $duration, $categoryarea];
        $this->query($sql, $data);

         // Get the ID of the newly inserted event
         $id = $this->pdo->lastInsertId();

         // Insert new participants into event_participants table
         $participants_array = explode(',', $participants);
         foreach ($participants_array as $participant) {
             $participant = trim($participant);
             $sql = "INSERT INTO `event_participants` (`event_id`, `Participant_Name`) VALUES (?, ?)";
             $data = [$id, $participant];
             $this->query($sql, $data);
         }
      } else {
          $sql = "UPDATE `events` SET `evt_start`=?, `evt_end`=?, `evt_text`=?, `evt_participants`=?, `evt_color`=?, `evt_bg`=?, `evt_coursecategory`=?, 
                  `evt_division`=?, `evt_venue`=?, `evt_duration`=?, `evt_categoryarea`=? WHERE `evt_id`=?";
          $data = [$start, $end, $txt, $participants, $color, $bg, $coursecategory, $division, $venue,
          $duration, $categoryarea, $id];
          $this->query($sql, $data);
  
          // Get the current participants for the event
          $sql = "SELECT `Participant_Name` FROM `event_participants` WHERE `event_id`=?";
          $data = [$id];
          $stmt = $this->query($sql, $data);
          $current_participants = array();
          
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $current_participants[] = $row['Participant_Name'];
          }
  
          // Update event participants in event_participants table
          $participants_array = explode(',', $participants);
          foreach ($participants_array as $participant) {
              $participant = trim($participant);
              if (!in_array($participant, $current_participants)) {
                  // Insert new participant into event_participants table
                  $sql = "INSERT INTO `event_participants` (`event_id`, `Participant_Name`) VALUES (?, ?)";
                  $data = [$id, $participant];
                  $this->query($sql, $data);
              }
          }

          // Delete event participants that are no longer selected
          foreach ($current_participants as $participant) {
              if (!in_array($participant, $participants_array)) {
                  // Delete participant from event_participants table
                  $sql = "DELETE FROM `event_participants` WHERE `event_id`=? AND `Participant_Name`=?";
                  $data = [$id, $participant];
                  $this->query($sql, $data);
              }
          }

          // Update event participants that have been modified
          foreach ($participants_array as $participant) {
              $participant = trim($participant);
              if (in_array($participant, $current_participants)) {
                  // Update existing participant record in event_participants table
                  $sql = "UPDATE `event_participants` SET `Participant_Name`=? WHERE `event_id`=? AND `Participant_Name`=?";
                  $data = [$participant, $id, $participant];
                  $this->query($sql, $data);
              }
          }
      }
  
      // Commit transaction if all queries succeed
      $this->pdo->commit();
  
      return true;
    } catch (Exception $e) {
        // Rollback transaction if any query fails
        $this->pdo->rollBack();
    
        // Handle the exception or log the error message
        echo "Error: " . $e->getMessage();
        return false;
    }
  
  }

  // (E) DELETE EVENT
  function del ($id) {
    // Delete event participants from event_participants table
    $this->query("DELETE FROM `event_participants` WHERE `event_id`=?", [$id]);

    // Delete event from events table
    $this->query("DELETE FROM `events` WHERE `evt_id`=?", [$id]);
    return true;
  }

  // (F) GET EVENTS FOR SELECTED PERIOD
  function get($month, $year) {
    // (F1) DATE RANGE CALCULATIONS
    $month = $month < 10 ? "0$month" : $month;
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $dateYM = "{$year}-{$month}-";
    $start = $dateYM . "01 00:00:00";
    $end = $dateYM . $daysInMonth . " 23:59:59";
  
    // (F2) GET EVENTS
    $participants = $_GET['participants'] ?? []; // Get the selected participants from the query string
    $participantsCount = count($participants);
    $sql = 'SELECT * FROM `events` WHERE (
      (`evt_start` BETWEEN :start AND :end)
      OR (`evt_end` BETWEEN :start AND :end)
      OR (`evt_start` <= :start AND `evt_end` >= :end)
      OR (`evt_start` < :start AND `evt_end` > :end)
    )';
    if ($participantsCount > 0) { // If there are selected participants, add the WHERE clause
      $in = implode(',', array_fill(0, $participantsCount, '?'));
      $sql .= ' AND `evt_participants` IN (' . $in . ')';
    }
    $this->query($sql, [':start' => $start, ':end' => $end, ...$participants]); // Pass the selected participants as parameters
    $events = [];
    while ($r = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
      if ($this->stmt !== null) {
        $events[$r["evt_id"]] = [
          "s" => $r["evt_start"], "e" => $r["evt_end"], "c" => $r["evt_color"], "b" => $r["evt_bg"], "t" => $r["evt_text"], 
          "cc" => $r["evt_coursecategory"], "d" => $r["evt_division"], 
          "v" => $r["evt_venue"], "dur" => $r["evt_duration"], "ca" => $r["evt_categoryarea"], "par" => $r["evt_participants"]
        ];
      }
    }
  
    // (F3) RESULTS
    return $events;
  }
  
}

// (G) DATABASE SETTINGS - CHANGE TO YOUR OWN!
define("DB_HOST", "localhost");
define("DB_NAME", "adatabase");
define("DB_CHARSET", "utf8mb4");
define("DB_USER", "root");
define("DB_PASSWORD", "");

// (H) NEW CALENDAR OBJECT
$_CAL = new Calendar();