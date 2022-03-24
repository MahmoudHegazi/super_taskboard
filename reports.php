<?php
ob_start();
require_once('config.php');
require_once('functions.php');

if (
  !isset($_SESSION['logged']) || empty($_SESSION['logged']) ||
  !isset($_SESSION['logged_id']) || empty($_SESSION['logged_id']) ||
  !is_numeric($_SESSION['logged_id']) || !isset($_SESSION['name'])
  ){
  $_SESSION['message_login'] = 'Please Login To acces this page';
  $_SESSION['success_login'] = False;
  header("Location: ./login.php");
  die();
  return False;
}

require_once('services/UserService.php');
require_once('services/CalendarService.php');
require_once('controllers/IndexController.php');
require_once('controllers/setup_controller.php');
require_once('controllers\ReportController.php');
global $pdo;

$reports_error_message = '';
$error = false;

$logged_userid = test_input($_SESSION['logged_id']);
$logged_uname = test_input($_SESSION['name']);
$user_role = 'admin';
$urole = return_current_logged_role($logged_userid);
if ($urole != 'admin'){
  // write the keywrods used in sql or checks by ur hand
  $reports_error_message = "You Have No Premssion To access This Page";
  $error = true;
}


$calendar_service = null;
$report_controller = null;
try {
  $calendar_service = new CalendarService($pdo);
  $report_controller = new ReportController($pdo, $logged_userid=$logged_userid, $logged_role='admin');
  $used_calendar = $calendar_service->get_used_calendar('used', 1);

  // direct post requests to postHandler
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // This How The SuperMVC handle all view with all needed given post requests
    $redirect_url = $_SERVER['HTTP_REFERER'];
    $report_controller->postHandler($report_controller, $_POST, $_SESSION, $redirect_url, $error, $user_role);
    die();
  }

  if (isset($used_calendar) && $used_calendar && !empty($used_calendar)){
    define('Calid', $used_calendar->get_id());
    define('TITLE', $used_calendar->get_title());
    define('DESCRIPTION', $used_calendar->get_description());
    define('THUMBNAIL', $used_calendar->get_background_image());

  }
  } catch( Exception $e ) {
    $reports_error_message = $e->getMessage();
    $error = true;
    echo '
      <!DOCTYPE html>
      <html>
      <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
      <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
      <title>No Calendar Found</title>
      </head>
      <body>
      <div class="alert alert-danger">
        <p class="text-center"><strong>Warning!</strong> '.
        $reports_error_message
        .
        '
          <!-- check if admin and display link to setup -->
          <div class="d-flex justify-content-center align-items-center">
            <a href="setup.php" class="btn btn-outline-primary">Go To Setup</a>
            <a href="index.php" class="btn btn-outline-primary">Go To Home</a>
            <a href="reports.php" class="btn btn-outline-primary">Go To Reports</a>
            <a href="signup.php" class="btn btn-outline-primary">Go To Signup</a>
            <a href="login.php" class="btn btn-outline-primary">Go To Login</a>
          </div>
        </p>
     </div>
     </body>
     </html>
     ';
     die();
  }

  if ($error){
    display_error($error, $reports_error_message);
    die();
  }


  //generateAppPeriodsAndIndex();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></title>
  <link rel="icon" href="<?php echo defined('THUMBNAIL') ? 'uploads/images/' . THUMBNAIL : 'uploads/images/default_logo.png'; ?>">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/jquery-3.5.1.min.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>



 <div class="container-fluid layout_container_grid" style="height:100%;">
  <!-- Control the column width, and how they should appear on different devices -->
<div class="row header_row text-white" style="height:150px;">
<div class="bg-primary text-center p-4 text-white rounded">
  <h1><?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></h1>
  <p><?php echo defined('DESCRIPTION') ? DESCRIPTION : 'Just Another Calendar...'; ?></p>
</div>
</div>
  <div class="row" style="height:100%;">
    <div class="col-sm-2 bg-light text-black text-center aside_container">

     <div class="nav_cont d-flex flex-column flex-nowrap">
       <div class="nav_item border border-light p-2 text-black d-flex flex-row flex-nowrap justify-content-center align-items-center" >
         <a class="menu__item flex-fill" href="./index.php" >
           <i class="menu__icon fa fa-home"></i>
           <span class="menu__text">Home</span>
         </a>
       </div>

       <div class="nav_item border border-light p-2 text-black d-flex flex-row flex-nowrap justify-content-center align-items-center">
         <a class="menu__item flex-fill" href="./setup.php">
           <i class="menu__icon fa fa-calendar"></i>
           <span class="menu__text">Setup</span>
         </a>
       </div>
       <div class="nav_item border border-light p-2 text-black d-flex flex-row flex-nowrap justify-content-center align-items-center">
         <a class="menu__item flex-fill" href="./reports.php" style="width:100%;">
           <i class="menu__icon fa fa-bar-chart"></i>
           <span class="menu__text">Reports</span>
         </a>
       </div>


       <div class="nav_item border border-light p-2 text-black d-flex flex-row flex-nowrap justify-content-center align-items-center">
         <a class="menu__item flex-fill" href="./logout.php">
           <i class="menu__icon fa fa-sign-out"></i>
           <span class="menu__text">Logout</span>
         </a>
       </div>


     </div>


    </div>
    <div class="col-sm-9 m-1 bg-light text-white text-center main_container" style="height:100%;">


    <!-- calendars -->
<div class="container p-3 mt-2 mb-3 border border-light rounded cals_container" id="calendars">
   <div class="mt-2 p-3 bg-cornflowerblue shadow1 text-white border border-light rounded">
     <h3>Calendars</h3>
     <div class="container cal_tools">
     <button class="btn btn-block btn-light border border-primary rounded hover_btn" data-bs-toggle="modal" data-bs-target="#addCalendar" style="width:100%;">Add New Calendar</button>
     </div>
   </div>
   <!-- Calendar Alert Messages Dynamic-->
   <?php
       if (!isset($_SESSION['error_displayed']) || $_SESSION['error_displayed'] == False){
         display_html_erro($_GET);
         $_SESSION['error_displayed'] = True;
       }
       //echo $_SESION['error_displayed'];;;
    ?>
  <!-- Calendar Alert Messages end -->


   <!-- Control the column width, and how they should appear on different devices -->
   <div class="row">
   <?php
     $calendar_service = new CalendarService($pdo);
     // custoimzed get all calendar make pagenation easy
     $total_calendars = $calendar_service->get_total_calendars();



   ?>
   <!-- Reservations Per Users start -->
     <div class="col-sm-6 text-white text-center calendarcard_css">
       <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
         <p class="badge bg-secondary text-white">1</p>
         <div data-error-id="reserv_per_users"></div>
         <div class="container chart_container">
           <h3 class="text-center chart_title badge bg-primary text-white">Reservations Per Users</h3>
           <div id="reserv_per_users_cont">
             <canvas id="reserv_per_users"></canvas>
           </div>
         </div>
      </div>
    </div>
   <!-- Reservations Per Users end -->


   <!-- Calendars Reservations start
     <div class="col-sm-6 text-white text-center calendarcard_css">
       <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
         <p class="badge bg-secondary text-white">2</p>
         <div data-error-id="calendars_performance_review"></div>
         <div class="container chart_container">
           <h3 class="text-center chart_title badge bg-primary text-white">Calendars Reservations</h3>
           <div id="calendars_performance_review_cont">
             <canvas id="calendars_performance_review"></canvas>
           </div>
         </div>
      </div>
    </div>
   -->

  <!-- Reservations Per Year start -->
    <div class="col-sm-6 text-white text-center calendarcard_css">
      <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
        <p class="badge bg-secondary text-white">3</p>
        <div data-error-id="reserv_per_year"></div>
        <div class="container chart_container">
          <h3 class="text-center chart_title badge bg-primary text-white">Reservations Per Year</h3>
          <div id="reserv_per_year_cont">
            <canvas id="reserv_per_year"></canvas>
          </div>
        </div>
     </div>
   </div>
 <!-- Reservations Per Year end -->

 <!-- Reservations Per Month start -->
   <div class="col-sm-6 text-white text-center calendarcard_css">
     <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
       <p class="badge bg-secondary text-white">4</p>
       <div data-error-id="reserv_per_month"></div>
       <div class="container chart_container">
         <h3 class="text-center chart_title badge bg-primary text-white">Reservations Per Month</h3>
         <div id="reserv_per_month_cont">
          <canvas id="reserv_per_month"></canvas>
        </div>
       </div>
    </div>
  </div>
<!-- Reservations Per Month end -->

<!-- Reservations Per Periods start -->
  <div class="col-sm-6 text-white text-center calendarcard_css">
    <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
      <p class="badge bg-secondary text-white">5</p>
      <div data-error-id="reserv_per_periods"></div>
      <div class="container chart_container">
        <h3 class="text-center chart_title badge bg-primary text-white">Reservations Per Periods</h3>
        <div id="doughnut_periods">
          <canvas id="reserv_per_periods"></canvas>
        </div>
      </div>
   </div>
 </div>
<!-- Reservations Per Periods end -->


<!-- Repeated Users Periods start -->
  <div class="col-sm-6 text-white text-center calendarcard_css">
    <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
      <p class="badge bg-secondary text-white">6</p>
      <div data-error-id="perfered_user_period"></div>
      <div class="container chart_container">
        <h3 class="text-center chart_title badge bg-primary text-white">Perfered Users Periods</h3>
        <div id="doughnut_perfered_period">
          <canvas id="perfered_user_period"></canvas>
        </div>
      </div>
   </div>
 </div>
<!-- Repeated Users Periods end -->

<!-- Repeated Users Slots start -->
  <div class="col-sm-6 text-white text-center calendarcard_css">
    <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
      <p class="badge bg-secondary text-white">7</p>
      <div data-error-id="perfered_user_slot"></div>
      <div class="container chart_container">
        <h3 class="text-center chart_title badge bg-primary text-white">Perfered Users Slots</h3>
        <div id="perfered_user_slot_cont">
          <canvas id="perfered_user_slot"></canvas>
        </div>
      </div>
   </div>
 </div>
<!-- Repeated Users Slots end -->


<!-- Top Periods start -->
  <div class="col-sm-6 text-white text-center calendarcard_css">
    <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
      <p class="badge bg-secondary text-white">8</p>
      <div data-error-id="best_period_and_slot"></div>
      <div class="container chart_container">
        <h3 class="text-center chart_title badge bg-primary text-white" title="this help you to know the best period and slot in your system merge them if not already merged">Best Period And Slot</h3>
        <div id="doughnut_best_period_and_slot">
          <canvas id="best_period_and_slot"></canvas>
        </div>
      </div>
   </div>
 </div>

   </div>


</div>
    </div>
  </div>
</div>

<script src="assets/js/reports.js" type="text/javascript"></script>
</body>
</html>
