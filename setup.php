<?php
ob_start();
require_once('config.php');
require_once('functions.php');

require_once('services/UserService.php');
require_once('services/CalendarService.php');
require_once('controllers/IndexController.php');
require_once('controllers/setup_controller.php');
global $pdo;

if (!isset($_SESSION['logged']) || empty($_SESSION['logged']) || !isset($_SESSION['logged_id']) || empty($_SESSION['logged_id']) || !isset($_SESSION['name'])){
  $_SESSION['message_login'] = 'Please Login To acces this page';
  $_SESSION['success_login'] = False;
  header("Location: ./login.php");
  die();
  return False;
}
$logged_userid = test_input($_SESSION['logged_id']);
$logged_uname = test_input($_SESSION['name']);

$request_type = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';
$used_calendar_emessage = '';
$error = false;

$logged_userid = test_input($_SESSION['logged_id']);
$logged_uname = test_input($_SESSION['name']);
$user_role = 'user';
$urole = return_current_logged_role($logged_userid);
if ($urole == 'admin'){
  // write the keywrods used in sql or checks by ur hand
  $user_role = 'admin';
}

$calendar_service = null;
try {
  $calendar_service = new CalendarService($pdo);
  $used_calendar = $calendar_service->get_used_calendar('used', 1);
  if (isset($used_calendar) && $used_calendar && !empty($used_calendar)){
    define('Calid', $used_calendar->get_id());
    define('TITLE', $used_calendar->get_title());
    define('DESCRIPTION', $used_calendar->get_description());
    define('THUMBNAIL', $used_calendar->get_background_image());

  }
  } catch( Exception $e ) {
    $used_calendar_emessage = $e->getMessage();
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
        $used_calendar_emessage
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
  <script src="assets/js/jquery-3.5.1.min.js"></script>
  <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
  <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

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
     $pag_limit = 3;
     $rows_count = intval($total_calendars / $pag_limit);
     $buttons_count = ($total_calendars / $pag_limit) > $rows_count ? $rows_count + 1 : $rows_count;

     $current_row = isset($_GET['offset']) && !empty($_GET['offset']) ? intval(test_input($_GET['offset'])) : 0;
     $offset = $current_row;
     $all_calendars = $calendar_service->get_all_calendars($pag_limit,$current_row);



     for($c=0; $c<count($all_calendars); $c++){
       $cal_id = $all_calendars[$c]->get_id();
       $cal_title = $all_calendars[$c]->get_title();
       $cal_start_year = $all_calendars[$c]->get_start_year();
       $cal_added_years = $all_calendars[$c]->get_added_years();
       $cal_periods_total = $all_calendars[$c]->get_periods_per_day();
       $cal_slots_total = $all_calendars[$c]->get_slots_per_period();
       $cal_description = $all_calendars[$c]->get_description();
       $cal_used = $all_calendars[$c]->get_used();
       $used_style = $cal_used == True ? "position:relative;" : "";
       $cal_background = 'uploads/images/'. $all_calendars[$c]->get_background_image();

   ?>

   <!-- calendar card start -->
     <div class="col-sm-4 text-white text-center calendarcard_css">
       <div class="container  p-3 mt-4 mb-4 m-2 border border-secondary rounded cal-card">
         <p class="badge bg-secondary text-white"><?php echo $cal_id; ?></p>
         <div class="container" style="<?php echo $used_style; ?>">
           <?php if ($cal_used == 1){ ?>
             <div class="ribbon">
               <span class="badge">Used</span>
             </div>
           <?php } ?>
           <h3 class="text-center cal_title badge bg-primary text-white"><?php echo $cal_title; ?></h3>
           <img class="border border-light rounded cal_image mb-2 mt-2" src="<?php echo $cal_background; ?>" width="100%;" style="max-height:150px;">
         </div>
         <div class="container cal_data">
           <p class="text-black card_data_container">
             <span class="badge bg-success">Periods: <strong><?php echo $cal_periods_total; ?></strong></span>
             <span class="badge bg-secondary">years: <strong><?php echo $cal_added_years; ?></strong></span>
             <span class="badge bg-primary">Slots: <strong><?php echo $cal_slots_total; ?></strong></span>
           </p>
         </div>

         <div class="container">
           <button type="button" data-bs-toggle="modal" data-bs-target="#editCalendar"
           class="btn btn-warning mt-2 btn-block edit_calendar"
           data-calendar="<?php echo $cal_id; ?>"
           data-title="<?php echo $cal_title; ?>"
           data-total-periods="<?php echo $cal_periods_total; ?>"
           data-total-slots="<?php echo $cal_slots_total; ?>"
           data-description="<?php echo $cal_description; ?>"
           data-added-years="<?php echo $cal_added_years; ?>"
           >Edit</button>

           <button type="button" data-bs-toggle="modal" data-bs-target="#removeCalendar"
           class="btn btn-danger mt-2 btn-block remove_calendar"
           data-calendar="<?php echo $cal_id; ?>">Remove</button>

           <form style="display:inline;" action="controllers/setup_controller.php" method="POST">
             <input type="hidden" value="<?php echo $cal_id; ?>" name="calendar_used_id" required />
             <button type="submit" class="btn btn-success mt-2 btn-block default_calendar"
             data-calendar="<?php echo $cal_id; ?>">Use</button>
           </form>

           <button data-cal-id="<?php echo $cal_id; ?>" data-bs-toggle="modal" data-bs-target="#advancedCalMange" class="btn btn-danger mt-2 btn-block btn btn-success">Advanced</button>

         </div>
      </div>
    </div>
  <!-- calendar card end -->

<?php }?>


   </div>
    <!-- php pagenation this new pagenation pure php -->
    <?php

    if ($total_calendars > 3){
      $btns_limit = 3;
      $current_page = $offset / $pag_limit;

      for ($pag=0; $pag<$buttons_count; $pag++){
        if ($pag==0){
          $current_offset =0;
        } else {
          $current_offset = $pag_limit * $pag;
        }
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $link_url = addOrReplaceQueryParm($actual_link, 'offset', $current_offset);
      ?>
        <a href="<?php echo $link_url; ?>" class="btn btn-primary"><?php echo $pag+1; ?></a>
      <?php
       }
      }
    ?>
   <div>


   </div>
</div>
<!-- calendars end -->
 <!-- Users -->


<div class="container p-3 mt-2 mb-3 border border-light rounded users_container" id="uses">
   <div class="mt-2 p-3 bg-cornflowerblue shadow1 text-white border border-light rounded">
     <h3>User Manger</h3>
     <div class="container cal_tools">
  <button class="btn btn-light border border-primary rounded hover_btn mb-2" style="width:100%;" data-bs-toggle="modal" data-bs-target="#addUser">Add New User</button>
     </div>
   </div>

   <!-- users table -->
  <table class="table table-dark table-striped table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Firstname</th>
        <th>username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Active</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $user_service = new UserService($pdo);
        $all_users = $user_service->get_all_users();
        for($u=0; $u<count($all_users); $u++){
       ?>
      <tr>
        <td><?php echo $all_users[$u]->get_id(); ?></td>
        <td><?php echo $all_users[$u]->get_name(); ?></td>
        <td><?php echo $all_users[$u]->get_username(); ?></td>
        <td><?php echo $all_users[$u]->get_email(); ?></td>
        <td><?php echo $all_users[$u]->get_role(); ?></td>
        <td><?php echo $all_users[$u]->get_active() == 1 ? 'Yes' : 'No'; ?></td>
        <td>
          <button
          data-user="<?php echo $all_users[$u]->get_id(); ?>"
          data-name="<?php echo $all_users[$u]->get_name(); ?>"
          data-username="<?php echo $all_users[$u]->get_username(); ?>"
          data-email="<?php echo $all_users[$u]->get_email(); ?>"
          data-role="<?php echo $all_users[$u]->get_role(); ?>"
          data-active="<?php echo $all_users[$u]->get_active() == 1 ? 'yes' : 'no'; ?>"

          data-bs-toggle="modal"
          data-bs-target="#editUser"
          class="btn btn-warning edit_user"
          >Edit</button>
          <button data-user="<?php echo $all_users[$u]->get_id(); ?>" data-bs-toggle="modal" data-bs-target="#removeUser" class="btn btn-danger delete_user">Delete</button>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
   <!-- users table end -->
 </div>
<!-- users end -->



    </div>
  </div>
</div>
<!-- Models -->
<!-- Add Calendar Model -->
<div class="modal " id="addCalendar">
  <div class="modal-dialog modal-lg">
    <form onsubmit="addCalWait(event)" action="controllers/setup_controller.php" method="POST" enctype="multipart/form-data" id="calnew_id_form">
    <div class="modal-content">


      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title edit_title">Add New Calendar</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div id="action_loading_container2" style="display:none;">
        <h3>Please wait...</h3>
        <img src="assets/images/gif-preloader.gif" id="loading_circle2" style="width:100%;">
      </div>
      <div id="add_newcal_container">

        <!-- Modal body -->
        <div class="modal-body">
          <div class="form-group">
            <label for="calendar_title">Calendar Title: </label>
            <input maxlength="30" size="30" type="text" name="calendar_title" id="calendar_title" class="form-control" placeholder="Enter Calendar Title" required>
          </div>

          <div class="form-group mt-2">
           <label for="calendar_title" for="calendar_description">Calendar Description: </label>
           <textarea maxlength="100" size="100" placeholder="Enter Calendar Description It will apears in the calendar home page after title" class="form-control" name="calendar_description" id="calendar_description"></textarea>
          </div>



          <div class="form-group mt-2">
            <label for="start_year">Start Year: </label>
            <input type="number" name="start_year" id="start_year"  min="1900" value="2022" class="form-control" required>
          </div>

          <div class="form-group mt-2">
            <label for="add_new_year">Years Added: </label>
            <input type="number" name="add_new_year" id="add_new_year" min="1" value="1" class="form-control" required>
          </div>

          <!-- controlls -->
          <div class="container text-center mt-2">
            <h3 class="badge bg-info p-2" style="font-size: 18px;">Periods</h3>
          </div>

           <div class="form-group mt-2">
            <label for="period_per_day">Periods Per Day: </label>
            <input type="number" name="period_per_day" id="period_per_day" min="0" value="3" class="form-control" required>
          </div>


           <div class="form-group mt-2" style="display:none;" id="periods_container">
           </div>

           <hr />

           <div class="container mt-2 text-center">
             <h3 class="badge bg-primary p-2" style="font-size: 18px;">Slots</h3>
           </div>

          <div class="form-group mt-2">
            <label for="slots_per_period">Slots Per Period: </label>
            <input type="number" name="slots_per_period" id="slots_per_period" min="0" value="3" class="form-control" required>
          </div>

           <div class="form-group mt-2" style="display:none;" id="slots_container">
          </div>
          <hr />


          <div class="form-group mt-2">
            <label for="calendar_title">Calendar Logo And Icon</label>
            <input type="file" name="background_image" id="background_image" value="0" class="form-control">
          </div>

          <div class="form-group mt-2">
            <label for="background_image_edit mb-1">Login and Signup background-image (Optional)</label>
            <input type="file" name="sign_background" id="sign_background" class="form-control">
          </div>

        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>

      </div>
    </div>
  </form>
  </div>
</div>
<!-- Add Calendar Model End -->

<!-- Edit Calendar Model -->
<div class="modal" id="editCalendar">
  <div class="modal-dialog modal-xl">

    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-center">Mange Calendar [<span id="selected_page">1</span>]</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>


     <!-- Modal body -->
     <div class="modal-body">
        <!-- loading circle -->
        <div id="action_loading_container" style="display:none;">
          <h3>Please wait...</h3>
          <img src="assets/images/gif-preloader.gif" id="loading_circle" style="width:100%;">
        </div>

        <!-- edit body -->
        <div id="edit_cal_body">
          <div class="container">
            <button  class="btn btn-outline-primary edit_cal_level" data-level="1">1</button>
            <button class="btn btn-outline-primary edit_cal_level" data-level="2">2</button>
          </div>


          <!-- main data container Level 1 -->
          <div class="level_container" data-content-level="1" id="main_data_level1">
          <div class="container border p-4 m-2 border-secondary">
            <h4 class="text-center p-2 edit_title">Main Info</h4>
            <form action="controllers/setup_controller.php" method="POST"  enctype="multipart/form-data">
              <div class="form-group">
                <label for="calendar_title_edit mb-1">Calendar Title: (Optional)</label>
                <input maxlength="30" size="30" type="text" name="calendar_title_edit" id="calendar_title_edit" class="form-control" placeholder="Enter Calendar Title" required>
              </div>

              <div class="form-group mt-2">
               <label for="calendar_description_edit mb-1">Calendar Description: (Optional)</label>
               <textarea maxlength="100" placeholder="Enter Calendar Description" class="form-control" name="calendar_description_edit" id="calendar_description_edit"></textarea>
              </div>

              <div class="form-group mt-2">
                <label for="background_image_edit mb-1">Calendar Logo And Icon (Optional)</label>
                <input type="file" name="background_image_edit" id="background_image_edit" class="form-control">
              </div>

              <div class="form-group mt-2">
                <label for="background_image_edit mb-1">Login and Signup background-image (Optional)</label>
                <input type="file" name="sign_background_edit" id="sign_background_edit" class="form-control">
              </div>


              <div class="form-group text-center">
                <input type="hidden" value="" name="calendar_userid_edit" id="calendar_userid_edit" style="display:none;" />

                <button type="submit" class="btn btn-success btn-block mt-2">Edit Calendar Data</button>
              </div>
            </form>
          </div>

          <div class="container border p-4 m-2 border-secondary">
            <h4 class="text-center mt-2 p-2 edit_title">Add More Years</h4>
            <form action="controllers/setup_controller.php" method="POST" id="added_years_form"s>
              <div class="form-group">
                <label for="add_new_year_edit">Years Added: </label>
                <input type="number" name="add_new_year_edit" id="add_new_year_edit" min="1" value="" class="form-control" title="if you leave this input will not effect the original years added" required>
              </div>
              <div class="form-group text-center">
                <input type="hidden" value="" name="years_added_calid" id="years_added_calid" style="display:none;" />
                <input type="submit"  class="btn btn-primary text-white mt-2" value="Add Years"/>
              </div>
            </form>
          </div>

          <!-- Periods Edit -->
          <div class="container border p-4 m-2 border-secondary">
            <div class="container">
              <h4 class="text-center p-2 edit_title">Mange Periods <span id="periods_edit_title"></span></h4>
              <div class="text-center mt-2 p-2 " class="form-group" id="modal_periods_container">
             </div>
            </div>
          </div>
          <!-- Periods Edit -->

          <!-- Periods Edit -->
          <div class="container border p-4 m-2 border-secondary">
            <div class="container">
              <h4 class="text-center p-2 edit_title bg-dark">Mange Slots <span id="slots_edit_title"></span></h4>
              <div class="text-center mt-2 p-2 " class="form-group" id="modal_slots_container">
             </div>
            </div>
          </div>
          <!-- Slots Edit -->
          <!-- main data container Level 1 end -->
        </div>
          <!-- Add Periods and slots container Level 2 -->
          <div class="level_container" data-content-level="2" id="periods_slots_level2" style="display:none;">
            <!-- add periods -->
            <div class="container">
              <h3 class="text-center p-2 m-2">Add New Periods</h3>
              <div class="container text-center">
                <h5 class="mt-2 mb-3 badge bg-success">Total Periods: <strong id="total_periods_strong">0</strong></h5>
              </div>
              <form action="controllers/setup_controller.php" method="POST">
                <div class="form-group">
                  <label for="added_periods">Added periods</label>
                  <input name="added_periods" id="period_per_day_page2" type="number" min="1" class="form-control" required>
                  <input id="add_periods_cal_id" name="add_periods_cal_id" type="hidden"  class="form-control" style="display:none;" required>
                </div>
                <div id="periods_container_page2" class="container">
                </div>
                <div class="form-group text-center mt-2 d-grid">
                  <input type="submit" class="btn btn-outline-success btn-block" value="Add Periods">
                </div>
             </form>
            </div>


            <!-- add slots -->
            <div class="container">
              <h3 class="text-center p-2 m-2">Add New Slots</h3>
              <div class="container text-center">
                <h5 class="mt-2 mb-3 badge bg-primary text-white">Total Slots: <strong id="total_slots_strong">0</strong></h5>
              </div>
              <form action="controllers/setup_controller.php" method="POST">
                <div class="form-group">
                  <label for="added_slots">Added Slots</label>
                  <input id="slots_per_day_page2" name="added_slots" type="number" min="1" class="form-control" required>
                  <input id="add_slots_cal_id" name="add_slots_cal_id" type="hidden"  class="form-control" style="display:none;" required>
                </div>
                <div id="slots_container_page2" class="container">
                </div>
                <div class="form-group text-center mt-2 d-grid">
                  <input type="submit" class="btn btn-outline-primary btn-block" value="Add Slots">
                </div>
             </form>
            </div>

          </div>
        <!-- Add Periods and slots container Level 2  end-->
       </div>
        <!-- edit body end -->
        <!-- Modal footer -->
        <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<!-- Edit Calendar Model End -->

<!-- Delete Calendar Model -->
<div class="modal" id="removeCalendar">
  <div class="modal-dialog">

    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-center">Delete Calendar</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="alert alert-danger" style="text-align:justify;">
          <h5>Are You Sure You want Delete [calendar title] click Remove Calendar.</h5>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        <form action="controllers/setup_controller.php" method="POST">
          <input type="hidden" id="remove_calendar_id" name="remove_calendar_id" style="display:none" required>
          <button type="submit" class="btn btn-info" data-bs-dismiss="modal">Remove Calendar</button>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Delete Calendar Model End -->


<!-- Add User Model -->
<div class="modal" id="addUser">
  <div class="modal-dialog">
    <form action="controllers/setup_controller.php" method="POST">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Add New User</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
        <div class="form-group">
          <label for="fullname">Full Name: </label>
          <input maxlength="30" size="30" type="text" name="fullname" id="fullname" class="form-control" placeholder="Enter User Full Name" required>
        </div>

        <div class="form-group">
          <label for="email">Email: </label>
          <input maxlength="50" size="50" type="email" name="email" id="email" class="form-control" placeholder="Enter User Email" required>
        </div>

        <div class="form-group">
          <label for="username">Username: </label>
          <input maxlength="34" size="34" type="text" name="username" id="username" autocomplete="new-username" class="form-control" placeholder="Enter Username" required>
        </div>

        <div class="form-group">
          <label for="password">Password: </label>
          <input type="password" name="password" id="password" autocomplete="new-password"  class="form-control"
          placeholder="Enter User Password"
          pattern="{8,12}" title="(Password must contains 8 characters up to 255 must and 3 of characters must be unqiue )"
          required>
        </div>


        <div class="form-group mt-2">
          <h5 class="text-center">System Data</h5>
          <label for="role">Role:</label>
            <select class="form-control" id="role" name="role" required>
              <option name="role" value="user" selected>User</option>
              <option name="role" value="admin">Admin</option>
            </select>
          <label  for="active"
            title="if you set this to No the user will not able to login also this used for protected user from invalid passwords accessed limits">
            Active:
          </label>
          <select class="form-control" id="active" name="active" required>
            <option name="active" value="yes" selected>Yes</option>
            <option name="active" value="no">No</option>
          </select>
        </div>


        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>

      </div>
    </form>
  </div>
</div>
<!-- Edit User Model -->
<div class="modal" id="editUser">
  <div class="modal-dialog">
    <form action="controllers/setup_controller.php" method="POST" autocomplete="off">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title text-center">Edit User</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">

        <div class="form-group">
          <label for="fullname_edit">Full Name: </label>
          <input maxlength="30" size="30" type="text" name="fullname_edit" id="fullname_edit" class="form-control" placeholder="Enter User Full Name">
        </div>

        <div class="form-group">
          <label for="email_edit">Email: </label>
          <input maxlength="50" size="50" type="email" name="email_edit" id="email_edit" class="form-control" placeholder="Enter User Email" required>
        </div>

        <div class="form-group">
          <label for="username_edit">Username: </label>
          <input maxlength="34" size="34" type="text" name="username_edit" id="username_edit" autocomplete="username" class="form-control" placeholder="Enter Username">
        </div>

        <div class="form-group">
          <label for="password_edit">Change Password: </label> <input type="checkbox" id="toggle_edit_pass">
          <input type="password" name="password_edit" value="" id="password_edit"  class="form-control"
          placeholder="Enter User Password" autocomplete="current-password"
          disabled>
        </div>

        <div class="form-group mt-2">
          <h5 class="text-center">System Data</h5>
          <label for="role_edit">Role:</label>
            <select class="form-control" id="role_edit" name="role_edit" required>
              <option name="role_edit" value="user" selected>User</option>
              <option name="role_edit" value="admin">Admin</option>
            </select>
          <label  for="active_edit"
            title="if you set this to No the user will not able to login also this used for protected user from invalid passwords accessed limits">
            Active:
          </label>
          <select class="form-control" id="active_edit" name="active_edit" required>
            <option name="active_edit" value="yes" selected>Yes</option>
            <option name="active_edit" value="no">No</option>
          </select>
        </div>


        <input type="hidden" name="userid_edit" id="userid_edit" class="form-control" style="display:none;" required>



        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button id="close_edit_user" type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>

      </div>
    </form>
  </div>
</div>
<!-- Delete Calendar Model End -->



<!-- Delete User Model -->
<div class="modal" id="removeUser">
  <div class="modal-dialog">

    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-center">Delete User</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

        <div class="alert alert-danger" style="text-align:justify;">
          <h5>Are You Sure You want Delete User [user name] Click on "Remove User".</h5>
        </div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        <form action="controllers/setup_controller.php" method="POST">
          <input type="hidden" value="" name="remove_user_id" id="remove_user_id" style="display:none;" />
          <button type="submit" class="btn btn-info" data-bs-dismiss="modal">Remove User</button>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- Delete Calendar Model End -->

<!-- Edit Period To slot data -->
<div class="modal fade asp_modal" id="advancedCalMange">
  <div class="modal-dialog modal-fullscreen">

    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Advanced Edits</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body" >
        <div id="asp_error_cont"></div>
        <p>Here You can edit every slot index start and end date (1,2,3),(4,5,6),(7,8,9) or even old type (1,2,3),(1,2,3),(1,2,3) so it the core dynamic u can have any order</p>
        <div id="modal_body_advanced" class="d-flex flex-row flex-wrap justify-content-center align-items-center align-content-center bg-light">
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- Edit Period To slot data end -->


<!-- end models -->
<script src="assets/js/setup_ajax.js" type="text/javascript"></script>
<script src="assets/js/setup.js" type="text/javascript"></script>


</body>
</html>
