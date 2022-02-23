<?php
$servername = "localhost";
$username = "root";
$password = "root";

try {
  $pdo = new PDO("mysql:host=$servername;dbname=super_calendar", $username, $password);
  // set the PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
