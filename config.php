<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "super_calendar";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESION)){
  session_start();
}
