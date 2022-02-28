<?php
require_once('config.php');
if (isset($_SESSION['logged']) && !empty($_SESSION['logged'])){
  unset($_SESSION['logged']);

  if (isset($_SESSION['logged_id']) && !empty($_SESSION['logged_id'])){
    unset($_SESSION['logged_id']);
  }
  if (isset($_SESSION['log_id']) && !empty($_SESSION['log_id'])){
    unset($_SESSION['log_id']);
  }
  if (isset($_SESSION['login_message']) && !empty($_SESSION['login_message'])){
    unset($_SESSION['login_message']);
  }
  if (isset($_SESSION['login_date']) && !empty($_SESSION['login_date'])){
    unset($_SESSION['login_date']);
  }
  $_SESSION['message_login'] = 'You Logged out successfully';
  $_SESSION['success_login'] = True;
  header("Location: ./login.php");
  die();
} else {
  $_SESSION['message_login'] = 'Please Login First';
  $_SESSION['success_login'] = False;
  header("Location: ./login.php");
  die();
}



?>
