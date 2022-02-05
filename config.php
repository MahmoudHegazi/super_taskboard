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
  session_start();
}
