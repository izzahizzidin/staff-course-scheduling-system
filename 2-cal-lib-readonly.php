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
  function query ($sql, $data=null) {
    $this->stmt = $this->pdo->prepare($sql);
    $this->stmt->execute($data);
  }

  // (D) GET EVENTS FOR SELECTED PERIOD
  function get($month, $year) {
    // (D1) DATE RANGE CALCULATIONS
    $month = $month < 10 ? "0$month" : $month;
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $dateYM = "{$year}-{$month}-";
    $start = $dateYM . "01 00:00:00";
    $end = $dateYM . $daysInMonth . " 23:59:59";
  
    // (D2) GET EVENTS
    $this->query('SELECT * FROM `events` WHERE (
      (`evt_start` BETWEEN :start AND :end)
      OR (`evt_end` BETWEEN :start AND :end)
      OR (`evt_start` <= :start AND `evt_end` >= :end)
      OR (`evt_start` < :start AND `evt_end` > :end)
    )', [':start' => $start, ':end' => $end]);
    $events = [];
    while ($r = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
      if (!empty($r['evt_participants'])) {
        $participants = explode(',', $r['evt_participants']); //cannot use implode
      } else {
        $participants = [];
      }
      if ($this->stmt !== null) {
        $events[$r["evt_id"]] = [
          "s" => $r["evt_start"], "e" => $r["evt_end"], "c" => $r["evt_color"], "b" => $r["evt_bg"], "t" => $r["evt_text"], 
          "cc" => $r["evt_coursecategory"], "d" => $r["evt_division"], 
          "v" => $r["evt_venue"], "dur" => $r["evt_duration"], "ca" => $r["evt_categoryarea"], "participants" => $participants
        ];
      }
    }
  
    // (D3) RESULTS
    return $events;
  }
  
}

// (E) DATABASE SETTINGS - CHANGE TO YOUR OWN!
define("DB_HOST", "localhost");
define("DB_NAME", "adatabase");
define("DB_CHARSET", "utf8mb4");
define("DB_USER", "root");
define("DB_PASSWORD", "");

// (F) NEW CALENDAR OBJECT
$_CAL = new Calendar();

// (G) READONLY VERSION - ONLY LOAD EVENTS FOR LOGGED IN USER
session_start();
if (isset($_SESSION["user"])) {
  $user = $_SESSION["user"];
  $sql = "SELECT evt_id, evt_start, evt_end, evt_color, evt_bg, evt_text, evt_coursecategory, evt_division,
          evt_venue, evt_duration, evt_categoryarea, evt_participants FROM events 
          WHERE (evt_participants LIKE :user)";
  $data = [":user" => "%$user%"];
  $_CAL->query($sql, $data);
  } else {
  // (H) READONLY VERSION - NO LOGGED IN USER, LOAD ALL EVENTS
  $_CAL->query("SELECT evt_id, evt_start, evt_end, evt_color, evt_bg, evt_text, evt_coursecategory, evt_division,
                evt_venue, evt_duration, evt_categoryarea, evt_participants FROM events");
}