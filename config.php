<?php
$servername = "localhost";
$username = "root";
$password = "root";

// this appname usally it's the root of the site for example folder contains website name supercalendar
define('APPNAME', 'supercalendar');

// set default time zone 
define('TIMEZONE', 'Europe/Rome');
date_default_timezone_set(TIMEZONE);

try {

  $now = new DateTime();
  $mins = $now->getOffset() / 60;
  $sgn = ($mins < 0 ? -1 : 1);
  $mins = abs($mins);
  $hrs = floor($mins / 60);
  $mins -= $hrs * 60;
  $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);

  $pdo = new PDO("mysql:host=$servername;dbname=super_calendar", $username, $password);
  // set the PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec("SET time_zone='$offset';");
  //echo "Connected successfully";
} catch(PDOException $e) {
  echo "DB Connection failed: " . $e->getMessage();
}

if (!isset($_SESSION)){
  // XSS  Secuirty for secure my created cookies to be accessed only with http no javaScript document.cookie and updated as init must be before session start we configure first then start
  ini_set("session.cookie_httponly", True);
  session_start();
}

// block unwanted requests types
if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'GET'){
  echo 'App Created By Python King';
  die();
}
