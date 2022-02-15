<?php
ob_start();
require_once('config.php');
require_once('functions.php');
require_once('services/UserService.php');
require_once('services/CalendarService.php');
global $pdo;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>My Calendar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/jquery-3.5.1.min.js" type="text/javascript"></script>

</head>
<body>



 <div class="container-fluid layout_container_grid" style="height:100%;">
  <!-- Control the column width, and how they should appear on different devices -->
<div class="row header_row text-white" style="height:150px;">
<div class="bg-primary text-center p-4 text-white rounded">
  <h1>Jumbotron Example</h1>
  <p>Lorem ipsum...</p>
</div>
</div>
  <div class="row" style="height:100%;">
    <div class="col-sm-2 bg-light text-black text-center aside_container">

     <div class="nav_cont">
       <div class="nav_item border border-light p-2 text-black" >
         <a class="menu__item" href="#" >
           <i class="menu__icon fa fa-home"></i>
           <span class="menu__text">Home</span>
         </a>
       </div>

       <div class="nav_item border border-light p-2 text-black">
         <a class="menu__item" href="#">
           <i class="menu__icon fa fa-calendar"></i>
           <span class="menu__text">Setup</span>
         </a>
       </div>
       <div class="nav_item border border-light p-2 text-black">
         <a class="menu__item" href="#">
           <i class="menu__icon fa fa-bar-chart"></i>
           <span class="menu__text">Reports</span>
         </a>
       </div>


       <div class="nav_item border border-light p-2 text-black">
         <a class="menu__item" href="#">
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
     <div class="col-sm-4 text-white text-center">
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


<div class="container p-3 mt-2 mb-3 border border-light rounded users_container" id="calendars">
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
        <td>
          <button
          data-user="<?php echo $all_users[$u]->get_id(); ?>"
          data-name="<?php echo $all_users[$u]->get_name(); ?>"
          data-username="<?php echo $all_users[$u]->get_username(); ?>"
          data-email="<?php echo $all_users[$u]->get_email(); ?>"

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
            <label for="calendar_title">Calendar Background</label>
            <input type="file" name="background_image" id="background_image" value="0" class="form-control">
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
                <label for="background_image_edit mb-1">Calendar Background (Optional)</label>
                <input type="file" name="background_image_edit" id="background_image_edit" min="0" value="0" class="form-control">
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
          <input maxlength="30" size="30" type="text" name="fullname" id="fullname" class="form-control" placeholder="Enter User Full Name">
        </div>

        <div class="form-group">
          <label for="email">Email: </label>
          <input maxlength="50" size="50" type="email" name="email" id="email" class="form-control" placeholder="Enter User Email" required>
        </div>

        <div class="form-group">
          <label for="username">Username: </label>
          <input maxlength="34" size="34" type="text" name="username" id="username" class="form-control" placeholder="Enter Username">
        </div>

        <div class="form-group">
          <label for="password">Password: </label>
          <input type="password" name="password" id="password" auto-complete="new-password"  class="form-control" placeholder="Enter User Password">
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
          <input maxlength="34" size="34" type="text" name="username_edit" id="username_edit" class="form-control" placeholder="Enter Username">
        </div>

        <div class="form-group">
          <label for="password_edit">Change Password: </label> <input type="checkbox" id="toggle_edit_pass">
          <input type="password" name="password_edit" value="" id="password_edit"  class="form-control" placeholder="Enter User Password" disabled>
        </div>

        <input type="hidden" name="userid_edit" id="userid_edit" class="form-control" style="display:none;" required>



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

<!-- end models -->
<script src="assets/js/setup_ajax.js" type="text/javascript"></script>

<script>
/* Helpers Functions */
function displayCalendarEditWait(event) {
    event.preventDefault;
    const calWaitBody = document.querySelector("#edit_cal_body");
    const calWaitContainer = document.querySelector("#action_loading_container");
    calWaitBody.style.visibility = "hidden";
    calWaitContainer.style.display = "block";
    event.target.submit();
    return false;
}
const addedYearsForm = document.querySelector("#added_years_form");
addedYearsForm.addEventListener("submit", displayCalendarEditWait);


/* Mange Periods In form start */

const period_input = document.querySelector("#period_per_day");
const periodContainer = document.querySelector("#periods_container");



period_input.addEventListener("input", mange_period_inputs);

function create_period_inputs(count, start_index = 1, type='add') {
    if (count < 1) {
        return false;
    }
    let index = start_index;
    let html_inputs = '';

    let period_date = type == 'add' ? 'time' : 'time';
    for (let i = 0; i < count; i++) {
        html_inputs += `
  <div class="form-group border border-dark mt-1 p-2 bg-light text-black">
     <h5 class="text-center badge bg-success text-white">Period ${index}</h5>
     <div class="row">
        <div class="col-sm-6">
           <label for="period_date_${index}">Period DateTime: </label>
           <input type="hidden" name="slot_add_index_${index}" value="${index}">
           <input required name="period_date_${index}" data-index="${index}" type="${period_date}" class="form-control period-date period_input" />
        </div>
        <div class="col-sm-6">
           <label for="period_description_${index}">Period Description</label>
           <input maxlength="50" size="50" name="period_description_${index}" type="text" class="form-control period-description period_input" data-index="${index}" placeholder="Enter Period Description" />
        </div>
     </div>
     <!-- styles -->
     <div class="row">
        <div class="col-sm-3">
           <label for="period_color_${index}">Font Color</label>
           <input required name="period_color_${index}" id="period_color_${index}"
              data-index="${index}" type="color" value="#ffffff" class="form-control period_style_color1" />
        </div>
        <div class="col-sm-3">
           <label for="period_background_${index}">Background color</label>
           <input required name="period_background_${index}" id="period_background_${index}"
              data-index="${index}" type="color" value="#3190d8" class="form-control period_style_bgcolor1" />
        </div>
        <div class="col-sm-3">
           <label for="period_fontfamily_${index}">Font Family</label>
           <select name="period_fontfamily_${index}" data-index="${index}" class="form-control period_font_family1">
              <option value="">Default</option>
              <option style="font-family:Georgia;" value="font-family:Georgia;" checked>Georgia</option>
              <option style="font-family:Palatino Linotype;" value="font-family:Palatino Linotype;">Palatino Linotype</option>
              <option style="font-family:Book Antiqua;" value="font-family:Book Antiqua;">Book Antiqua</option>
              <option style="font-family:Times New Roman;" value="font-family:Times New Roman;">Times New Roman</option>
              <option style="font-family:Arial;" value="font-family:Arial;">Arial</option>
              <option style="font-family:Helvetica;" value="font-family:Helvetica;">Helvetica</option>
              <option style="font-family:Impact;" value="font-family:Impact;">Impact</option>
              <option style="font-family:Lucida Sans Unicode;" value="font-family:Lucida Sans Unicode;">Lucida Sans Unicode</option>
              <option style="font-family:Tahoma;" value="font-family:Tahoma;">Tahoma</option>
              <option style="font-family:Verdana;" value="font-family:Verdana;">Verdana</option>
              <option style="font-family:Courier New;" value="font-family:Courier New;">Courier New</option>
              <option style="font-family:Lucida Console;" value="font-family:Lucida Console;">Lucida Console</option>
              <option style="font-family:initial;" value="font-family:initial;">initial</option>
           </select>
        </div>
        <div class="col-sm-3">
           <label for="period_fontsize_${index}">Font Size</label>
           <select id="period_fontsize_${index}" name="period_fontsize_${index}" data-index="${index}"
            class="form-control period_fontsize1">
              <option value="" checked>Default</option>
              <option style="font-size: 8px;" value="font-size: 8px;">8px</option>
              <option style="font-size: 10px;" value="font-size: 10px;">10px</option>
              <option style="font-size: 12px;" value="font-size: 12px;">12px</option>
              <option style="font-size: 14px;" value="font-size: 14px;">14px</option>
              <option style="font-size: 16px;" value="font-size: 16px;">16px</option>
              <option style="font-size: 18px;" value="font-size: 18px;">18px</option>
              <option style="font-size: 1rem;" value="font-size: 1rem;">1 rem</option>
              <option style="font-size: 1rem;" value="font-size: 1rem;">1.5 rem</option>
              <option style="font-size: 0.825em;" value="font-size: 0.825em;">0.825 em</option>
              <option style="font-size: 0.925em;" value="font-size: 0.925em;">0.925 em</option>
              <option style="font-size: 1em;" value="font-size: 1em;">1 em</option>
              <option style="font-size: 1.5em;" value="font-size: 1.5em;">1.5 em</option>
              <option style="font-size: 2em;" value="font-size: 2em;">2 em</option>
              <option style="font-size: 1rem;" value="font-size: 1rem;">2 rem</option>
           </select>
        </div>
     </div>
     <div class="row">
        <div class="col-sm-3">
           <label for="period_border_color_${index}">Border Color</label>
           <select id="period_border_color_${index}" name="period_border_color_${index}"
            data-index="${index}" class="form-control period_border_part1_1 period_border1">
              <option value="" checked>No Border</option>
              <option value="black;">Black</option>
              <option value="white;">White</option>
              <option value="red;">Red</option>
              <option value="green;">Green</option>
              <option value="gold;">Gold</option>
              <option value="blue;">Blue</option>
           </select>
        </div>
        <div class="col-sm-3">
           <label for="period_border_size_${index}">Border Size</label>
           <select id="period_border_size_${index}" name="period_border_size_${index}"
           data-index="${index}" class="form-control period_border_part2_1 period_border1">
              <option value="" checked>No Border</option>
              <option value="border:1px">1px</option>
              <option value="border:1px">2px</option>
              <option value="border:1px">3px</option>
              <option value="border:1px">4px</option>
              <option value="border:1px">5px</option>
           </select>
        </div>
        <div class="col-sm-3">
           <label for="period_border_type_${index}">Border Type</label>
           <select id="period_border_type_${index}" name="period_border_type_${index}"
           data-index="${index}" class="form-control period_border_part3_1 period_border1">
               <option value="" checked>No Border</option>
               <option value="solid">solid</option>
               <option value="dotted">dotted</option>
               <option value="dashed">dashed</option>
               <option value="double">double</option>
               <option value="groove">groove</option>
               <option value="ridge">ridge</option>
               <option value="inset">inset</option>
               <option value="outset">outset</option>
               <option value="mix">mix</option>
           </select>
        </div>
     </div>
     <div class="container mt-3" id="accordion_period1_${index}">
        <div class="card">
           <div class="card-header">
              <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwoPeriod${index}">
              Advanced Style
              </a>
           </div>
           <div id="collapseTwoPeriod${index}" class="collapse" data-bs-parent="#accordion_period1_${index}">
              <div class="card-body">
                 <div class="row">
                    <div class="col-sm-12">
                       <label for="period_customcss_${index}">Custom CSS</label>
                       <input id="period_customcss_${index}" placeholder="Custom CSS" name="period_customcss_${index}"
                          data-index="${index}" type="text" class="form-control" />
                    </div>
                    <div class="col-sm-12 mt-2">
                       <div class="d-grid">
                         <button type="button" class="btn btn-primary btn-block" data-bs-toggle="collapse"
                          data-bs-target="#pread_more_${index}">
                          What Is Custom CSS and How I use it
                         </button>
                       </div>

                       <div id="pread_more_${index}" class="collapse">
                          <p>custom css will change the style of specific period or slot you can add any css you need but it has two uses first you can group some css rules together in this case no need to use comma so all activated will be one unit , second use , whenever it is It's better to create your own css rules with a comma so that you can only delete the given rule, note that the maximum length of one unit without a comma is 250 characters. So adding a comma after each css rule would be better to avoid that and have more control</p>
                       </div>

                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
<hr />
  `;
        index += 1;
    }
    return html_inputs;

}

function mange_period_inputs(event) {
    let period_inputs = create_period_inputs(event.target.value, 1, 'add');
    if (period_inputs) {
        periodContainer.innerHTML = period_inputs;
        periodContainer.style.display = "block";
    } else {
        periodContainer.innerHTML = '';
        periodContainer.style.display = "none";
    }
}

let period_inputs = create_period_inputs(period_input.value, 1, 'add');
if (period_inputs) {
    periodContainer.innerHTML = period_inputs;
    periodContainer.style.display = "block";
}


/* Mange Periods In form end */




/* Mange Slots In form start */

const slot_input = document.querySelector("#slots_per_period");
const slotContainer = document.querySelector("#slots_container");


slot_input.addEventListener("input", mange_slot_inputs);

function create_slot_inputs(count, start_index = 1) {
    if (count < 1) {
        return false;
    }
    let index = start_index;
    let html_inputs = '';
    for (let i = 0; i < count; i++) {
        html_inputs += `
<div class="form-group border border-dark mt-1 p-2 bg-light text-black">
  <h5 class="text-center badge bg-success text-white">Slot ${index}</h5>
  <div class="row">
        <div class="col-sm-6">
      <label for="start_at_slot">Start At: </label>
      <input required name="start_at_slot_${index}" data-index="${index}" type="time" class="form-control slot-start-at slot_input" />
    </div>
    <div class="col-sm-6">
      <label for="end_at_slot">End At: </label>
      <input required name="end_at_slot_${index}" data-index="${index}" type="time" class="form-control slot-end-at slot_input" />
    </div>
  </div>

  <!-- styles -->
  <div class="row">
     <div class="col-sm-3">
        <label for="slot_color_${index}">Font Color</label>
        <input required name="slot_color_${index}" id="slot_color_${index}"
           data-index="${index}" type="color" value="#000000" class="form-control slot_style_color1" />
           <input value="${index}" type="hidden" style="display:none;" name="slot_add_index_${index}">
     </div>
     <div class="col-sm-3">
        <label for="slot_background_${index}">Background color</label>
        <input required name="slot_background_${index}" id="slot_background_${index}"
           data-index="${index}" type="color" value="#ffffff" class="form-control slot_style_bgcolor1" />
     </div>
     <div class="col-sm-3">
        <label for="slot_fontfamily_${index}">Font Family</label>
        <select name="slot_fontfamily_${index}" data-index="${index}" class="form-control slot_font_family1">
           <option value="">Default</option>
           <option style="font-family:Georgia;" value="font-family:Georgia;" checked>Georgia</option>
           <option style="font-family:Palatino Linotype;" value="font-family:Palatino Linotype;">Palatino Linotype</option>
           <option style="font-family:Book Antiqua;" value="font-family:Book Antiqua;">Book Antiqua</option>
           <option style="font-family:Times New Roman;" value="font-family:Times New Roman;">Times New Roman</option>
           <option style="font-family:Arial;" value="font-family:Arial;">Arial</option>
           <option style="font-family:Helvetica;" value="font-family:Helvetica;">Helvetica</option>
           <option style="font-family:Impact;" value="font-family:Impact;">Impact</option>
           <option style="font-family:Lucida Sans Unicode;" value="font-family:Lucida Sans Unicode;">Lucida Sans Unicode</option>
           <option style="font-family:Tahoma;" value="font-family:Tahoma;">Tahoma</option>
           <option style="font-family:Verdana;" value="font-family:Verdana;">Verdana</option>
           <option style="font-family:Courier New;" value="font-family:Courier New;">Courier New</option>
           <option style="font-family:Lucida Console;" value="font-family:Lucida Console;">Lucida Console</option>
           <option style="font-family:initial;" value="font-family:initial;">initial</option>
        </select>
     </div>
     <div class="col-sm-3">
        <label for="slot_fontsize_${index}">Font Size</label>
        <select id="slot_fontsize_${index}" name="slot_fontsize_${index}" data-index="${index}"
         class="form-control slot_fontsize1">
           <option value="" checked>Default</option>
           <option style="font-size: 8px;" value="font-size: 8px;">8px</option>
           <option style="font-size: 10px;" value="font-size: 10px;">10px</option>
           <option style="font-size: 12px;" value="font-size: 12px;">12px</option>
           <option style="font-size: 14px;" value="font-size: 14px;">14px</option>
           <option style="font-size: 16px;" value="font-size: 16px;">16px</option>
           <option style="font-size: 18px;" value="font-size: 18px;">18px</option>
           <option style="font-size: 1rem;" value="font-size: 1rem;">1 rem</option>
           <option style="font-size: 1rem;" value="font-size: 1rem;">1.5 rem</option>
           <option style="font-size: 0.825em;" value="font-size: 0.825em;">0.825 em</option>
           <option style="font-size: 0.925em;" value="font-size: 0.925em;">0.925 em</option>
           <option style="font-size: 1em;" value="font-size: 1em;">1 em</option>
           <option style="font-size: 1.5em;" value="font-size: 1.5em;">1.5 em</option>
           <option style="font-size: 2em;" value="font-size: 2em;">2 em</option>
           <option style="font-size: 1rem;" value="font-size: 1rem;">2 rem</option>
        </select>
     </div>
     <div class="col-sm-3">
        <label for="slot_border_color_${index}">Border Color</label>
        <select id="slot_border_color_${index}" name="slot_border_color_${index}"
         data-index="${index}" class="form-control slot_border_part1_1 slot_border1">
           <option value="" checked>No Border</option>
           <option value="black;">Black</option>
           <option value="white;">White</option>
           <option value="red;">Red</option>
           <option value="green;">Green</option>
           <option value="gold;">Gold</option>
           <option value="blue;">Blue</option>
        </select>
     </div>
     <div class="col-sm-3">
        <label for="slot_border_size_${index}">Border Size</label>
        <select id="slot_border_size_${index}" name="slot_border_size_${index}"
        data-index="${index}" class="form-control slot_border_part2_1 slot_border1">
           <option value="" checked>No Border</option>
           <option value="border:1px">1px</option>
           <option value="border:1px">2px</option>
           <option value="border:1px">3px</option>
           <option value="border:1px">4px</option>
           <option value="border:1px">5px</option>
        </select>
     </div>
     <div class="col-sm-3">
        <label for="slot_border_type_${index}">Border Type</label>
        <select id="slot_border_type_${index}" name="slot_border_type_${index}"
        data-index="${index}" class="form-control slot_border_part3_1 slot_border1">
           <option value="" checked>No Border</option>
           <option value="solid">solid</option>
           <option value="dotted">dotted</option>
           <option value="dashed">dashed</option>
           <option value="double">double</option>
           <option value="groove">groove</option>
           <option value="ridge">ridge</option>
           <option value="inset">inset</option>
           <option value="outset">outset</option>
           <option value="mix">mix</option>
        </select>
     </div>

  </div>
  <div class="container mt-3" id="accordion_slot1_${index}">
     <div class="card">
        <div class="card-header">
           <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwoSlot${index}">
           Advanced Style
           </a>
        </div>
        <div id="collapseTwoSlot${index}" class="collapse" data-bs-parent="#accordion_slot1_${index}">
           <div class="card-body">
              <div class="row">
                 <div class="col-sm-12">
                    <label for="slot_customcss_${index}">Custom CSS</label>
                    <input id="slot_customcss_${index}" placeholder="Custom CSS" name="slot_customcss_${index}"
                       data-index="${index}" type="text" class="form-control" />

                 </div>
                 <div class="col-sm-12 mt-2">
                    <div class="d-grid">
                      <button type="button" class="btn btn-primary btn-block" data-bs-toggle="collapse"
                       data-bs-target="#slot_more_${index}">
                       What Is Custom CSS and How I use it
                      </button>
                    </div>



                    <div id="slot_more_${index}" class="collapse">
                       <p>custom css will change the style of specific period or slot you can add any css
                        you need but it has two uses first you can group
                        some css rules together in this case no need
                        vertical bar example <code>font-weight:bold;text-align:center;</code>
                        or make each rule as single unit to easy control it
                        <code>font-weight:bold;|text-align:center;</code>
                        or make mix <code>font-weight:bold;text-align:center;|background:red !important;</code>
                        in last example we have 2 group of rules one has 2 rules together and the second spreated rule
                        not the app can deatect some invalid css and ignore it but make sure to avoid wrong css
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
</div>
    `;
        index += 1;
    }
    return html_inputs;

}

function mange_slot_inputs(event) {
    let slots_inputs = create_slot_inputs(event.target.value);
    if (slots_inputs) {
        slotContainer.innerHTML = slots_inputs;
        slotContainer.style.display = "block";
    } else {
        slotContainer.innerHTML = '';
        slotContainer.style.display = "none";
    }

}

let slots_inputs = create_slot_inputs(period_input.value);
if (slots_inputs) {
    slotContainer.innerHTML = slots_inputs;
    slotContainer.style.display = "block";
}


/* Mange Slots In form end */

/* remove calendar */
const removeCalendarBtns = document.querySelectorAll(".remove_calendar");
const calendarIdDelete = document.querySelector("#remove_calendar_id");
removeCalendarBtns.forEach((removeBtn) => {
    removeBtn.addEventListener("click", (event) => {
        calendarIdDelete.value = event.target.getAttribute("data-calendar");
    });
});

/* remove user */
const removeUserBtns = document.querySelectorAll(".delete_user");
const removeUserId = document.querySelector("#remove_user_id");
removeUserBtns.forEach((removeBtn) => {
    removeBtn.addEventListener("click", (event) => {
        removeUserId.value = event.target.getAttribute("data-user");
    });
});



/* edit user */
const editUserBtns = document.querySelectorAll(".edit_user");
const fullnameEdit = document.querySelector("#fullname_edit");
const usernameEdit = document.querySelector("#username_edit");
const emailEdit = document.querySelector("#email_edit");
const useridEdit = document.querySelector("#userid_edit");
const passwordInputEdit = document.querySelector("#password_edit");
editUserBtns.forEach((editBtn) => {
    editBtn.addEventListener("click", (event) => {
        fullnameEdit.value = event.target.getAttribute("data-name");
        usernameEdit.value = event.target.getAttribute("data-username");
        emailEdit.value = event.target.getAttribute("data-email");
        useridEdit.value = event.target.getAttribute("data-user");
        passwordInputEdit.value = "";
    });
});
/* edit calendar */
const editCalendarBtns = document.querySelectorAll(".edit_calendar");
const calendarTitleEdit = document.querySelector("#calendar_title_edit");
const calendarDescriptionEdit = document.querySelector("#calendar_description_edit");
const calendarUseridEdit = document.querySelector("#calendar_userid_edit");
const addedYearCalId = document.querySelector("#years_added_calid");
const addNewYearEdit = document.querySelector("#add_new_year_edit");

/* page 2 vars*/
const addPeriodsCalId = document.querySelector("#add_periods_cal_id");
const addSlotsCalId = document.querySelector("#add_slots_cal_id");

const t_periodsInputPage2 = document.querySelector("#period_per_day_page2");
const t_slotsInputPage2 = document.querySelector("#slots_per_day_page2");


editCalendarBtns.forEach((editBtn) => {
    editBtn.addEventListener("click", (event) => {
      /*

      */

        /* page 1 */
        calendarTitleEdit.value = event.target.getAttribute("data-title");
        calendarDescriptionEdit.value = event.target.getAttribute("data-description");
        calendarUseridEdit.value = event.target.getAttribute("data-calendar");
        addedYearCalId.value = event.target.getAttribute("data-calendar");
        addNewYearEdit.setAttribute("placeholder", event.target.getAttribute("data-added-years"));

        /* page 2*/
        addPeriodsCalId.value = event.target.getAttribute("data-calendar");
        addSlotsCalId.value = event.target.getAttribute("data-calendar");
        t_periodsInputPage2.setAttribute("data-total-periods", event.target.getAttribute("data-total-periods"));
        t_slotsInputPage2.setAttribute("data-total-slots", event.target.getAttribute("data-total-slots"));
        t_slotsInputPage2.setAttribute("data-total-periods", event.target.getAttribute("data-total-periods"));

    });
});
/* edit calendar end */


const toggleEditPass = document.querySelector("#toggle_edit_pass");

toggleEditPass.addEventListener("change", (event) => {
    console.log(event.target);
    if (event.target.checked == true) {
        if (passwordInputEdit.hasAttribute("disabled")) {
            passwordInputEdit.removeAttribute("disabled");
        }
        if (!passwordInputEdit.hasAttribute("required")) {
            passwordInputEdit.setAttribute("required", true);
        }
    } else {
        if (!passwordInputEdit.hasAttribute("disabled")) {
            passwordInputEdit.setAttribute("disabled", true);
        }
        if (passwordInputEdit.hasAttribute("required")) {
            passwordInputEdit.removeAttribute("required");
        }
    }



});

/* change edit calendar content modal */
function goToLevel(event) {
    const levelSpan = document.querySelector("#selected_page");
    const selectedLevel = event.target.getAttribute("data-level");
    if (selectedLevel) {
        const selectedContainer = document.querySelector(`div[data-content-level='${selectedLevel}']`);
        if (selectedContainer) {
            const allLevelsContainers = document.querySelectorAll(".level_container");
            allLevelsContainers.forEach((levelContainer) => {
                levelContainer.style.display = "none";
            });
            selectedContainer.style.display = "block";
            levelSpan.innerText = selectedLevel;
        }
    }
}

const editCalLevelBtns = document.querySelectorAll(".edit_cal_level");
editCalLevelBtns.forEach((levelBtn) => {
    levelBtn.addEventListener("click", goToLevel);
});


/* display periods in add periods */
const periodInputPage2 = document.querySelector("#period_per_day_page2");
const periodContainerPage2 = document.querySelector("#periods_container_page2");
periodInputPage2.addEventListener("input", display_periods_page2);


function display_periods_page2(event) {
    let start_index = Number(event.target.getAttribute("data-total-periods")) + 1;
    let period_inputs = create_period_inputs(event.target.value, start_index, 'edit');
    if (period_inputs) {
        periodContainerPage2.innerHTML = period_inputs;
        periodContainerPage2.style.display = "block";
    } else {
        periodContainerPage2.innerHTML = '';
        periodContainerPage2.style.display = "none";
    }
}


/* add slots page 2*/

const slotsInputPage2 = document.querySelector("#slots_per_day_page2");
const slotsContainerPage2 = document.querySelector("#slots_container_page2");
slotsInputPage2.addEventListener("input", display_slots_page2);

function display_slots_page2(event) {
    let totalPeriods = Number(event.target.getAttribute("data-total-periods"));
    slotsContainerPage2.innerHTML = "<div class='alert alert-info mt-2'>Please Add Periods First</div>";
    if (totalPeriods == 0) {
        return false;
    }

    let start_index = Number(event.target.getAttribute("data-total-slots")) + 1;

    // add the slots start from new index
    let slots_inputs = create_slot_inputs(event.target.value, start_index);
    if (slots_inputs) {
        slotsContainerPage2.innerHTML = slots_inputs;
        slotsContainerPage2.style.display = "block";
    } else {
        slotsContainerPage2.innerHTML = '';
        slotsContainerPage2.style.display = "none";
    }

}

/* */

// set inital input for add periods and slots to not let user add empty input by wrong
const allEditCalbtns = document.querySelectorAll(".edit_calendar");
allEditCalbtns.forEach((calbtn) => {
    periodInputPage2.value = 1;
    slotsInputPage2.value = 1;

    calbtn.addEventListener("click", () => {


        let start_index = Number(calbtn.getAttribute("data-total-periods")) + 1;
        let start_index_slots = Number(calbtn.getAttribute("data-total-slots")) + 1;
        let period_inputs = create_period_inputs(1, start_index, 'edit');
        if (period_inputs) {
            periodContainerPage2.innerHTML = period_inputs;
            periodContainerPage2.style.display = "block";
        } else {
            periodContainerPage2.innerHTML = '';
            periodContainerPage2.style.display = "none";
        }

        let totalPeriods = Number(calbtn.getAttribute("data-total-periods"));

        if (totalPeriods < 1) {
            slotsContainerPage2.innerHTML = "<div class='alert alert-info mt-2'>Please Add Periods First</div>";
        } else {
            if (Number(calbtn.getAttribute("data-total-periods")) > 0) {

                let slots_inputs = create_slot_inputs(1, start_index_slots);
                if (slots_inputs) {
                    slotsContainerPage2.innerHTML = slots_inputs;
                    slotsContainerPage2.style.display = "block";
                } else {
                    slotsContainerPage2.innerHTML = '';
                    slotsContainerPage2.style.display = "none";
                }
            }
        }


    });

});

/*  delete periods toggle */

function startDeletePeriod(event) {
    event.preventDefault();
    const deletePeriodForm = document.querySelector("#delete_period_form");
    deletePeriodForm.addEventListener("submit", displayCalendarEditWait);
    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);
    container1.style.display = "none";
    container2.style.display = "block";

    container2.setAttribute("data-level-1", container1ID);

    container2.classList.add('active-delete-period');

}


function backDefaultDelete() {
    const deletePeriods1 = document.querySelectorAll(".delete_periods_s1");
    const deletePeriods2 = document.querySelectorAll(".delete_periods_s2");
    const deleteSlots1 = document.querySelectorAll(".delete_slots_s1");
    const deleteSlots2 = document.querySelectorAll(".delete_slots_s2");
    deletePeriods1.forEach((period1) => {
        period1.style.display = "block";
    });
    deletePeriods2.forEach((period2) => {
        period2.style.display = "none";
        if (period2.classList.add('active-delete-period')) {
            period2.classList.remove('active-delete-period');
        }
    });
    deleteSlots1.forEach((slot1) => {
        slot1.style.display = "block";
    });
    deleteSlots2.forEach((slot2) => {
        if (slot2.classList.add('active-delete-slot')) {
            slot2.classList.remove('active-delete-slot');
        }
        slot2.style.display = "none";
    });
}

function endDeletePeriod(event) {
    event.preventDefault();
    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);
    container1.style.display = "block";
    container2.style.display = "none";
    backDefaultDelete();
}

/*  delete slots toggle */

function startDeleteSlot(event) {
    event.preventDefault();
    const deletePeriodForm = document.querySelector("#delete_period_form");
    deletePeriodForm.addEventListener("submit", displayCalendarEditWait);
    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);
    container1.style.display = "none";
    container2.style.display = "block";

    container2.setAttribute("data-level-1", container1ID);
    container2.classList.add('active-delete-period');

}


function endDeleteSlot(event) {
    event.preventDefault();
    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);
    container1.style.display = "block";
    container2.style.display = "none";
    backDefaultDelete();
}

function startDeleteSlot(event) {
    event.preventDefault();

    const deleteSlotForm = document.querySelector("#delete_slot_form");
    deleteSlotForm.addEventListener("submit", displayCalendarEditWait);

    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);

    container1.style.display = "none";
    container2.style.display = "block";

    container2.setAttribute("data-level-1", container1ID);
    container2.classList.add('active-delete-period');
}

// toggle show and hide custom css style period
function toggleEditCustomCSS(event){

  let dataIndex = event.target.getAttribute("data-index");
  dataIndex = !dataIndex ? event.target.parentElement.getAttribute("data-index") : dataIndex;
  const currentForm = document.querySelector(`form.edit_customcss_form[data-index='${dataIndex}']`);
  if (currentForm){
    const currentStatus  = currentForm.getAttribute("data-visible");
    if (currentStatus == 'false'){
      currentForm.style.display = "block";
      currentForm.setAttribute("data-visible", "true");
    } else {
      currentForm.style.display = "none";
      currentForm.setAttribute("data-visible", "false");
    }
  }
}


function toggle_show_css(event){

  const currentBtn = event.target;
  if (!currentBtn.hasAttribute("href")){
    return false;
  }
  const btnHrefSplited = currentBtn.getAttribute("href").split("#");
  if (btnHrefSplited.length < 2){
    return false;
  } else {
    event.preventDefault();
  }
  const currentContainerId = btnHrefSplited[1];
  const currentContainer = document.getElementById(currentContainerId);
  if (currentContainer){
    if (currentBtn.getAttribute("data-status") == 'show'){
      if (!currentBtn.classList.contains("active_shadow")){
        currentBtn.classList.add("active_shadow");
      }
      if (!currentContainer.classList.contains("active_shadow")){
        currentContainer.classList.add("active_shadow");
      }
      currentBtn.setAttribute("data-status", "hide");
    } else {
      if (currentBtn.classList.contains("active_shadow")){
        currentBtn.classList.remove("active_shadow");
      }
      if (currentContainer.classList.contains("active_shadow")){
        currentContainer.classList.remove("active_shadow");
      }
      currentBtn.setAttribute("data-status", "show");
    }

  }
}


// handle main css edit
function handleMainCss(event, code_id){

  const cssIntialValuesElm = document.querySelector(`#${code_id}`);
  const errorElm = document.querySelector(`p[data-code='${code_id}']`);

  if (!cssIntialValuesElm){
    alert("Unexcpted Error");
    event.preventDefault();
    return false;
  }
  event.preventDefault();
  const intialValues = cssIntialValuesElm.innerText.split(",");
  console.log(intialValues);

  const allMainCSSInputs = document.querySelectorAll(".main_css");
  let currentDisabled = 0;
  allMainCSSInputs.forEach( (inp)=>{
    const inputPeviousValue = intialValues[Number(inp.getAttribute("data-i"))];
    const inputCurentValue = inp.value;
    if (inputPeviousValue == inputCurentValue){
      inp.setAttribute("disabled", "disabled");
      inp.classList.add("disabled_inp");
      currentDisabled += 1;
    }
  });



  if (currentDisabled < allMainCSSInputs.length){
    errorElm.innerText = "";
    errorElm.style.display = "none";
    event.target.submit();
  } else {
     errorElm.style.display = "block";
     errorElm.innerText = "No Changes Were detected.";
     errorElm.style.border = "2px solid red";
     setTimeout(()=>{errorElm.style.border = "";},300);
     const allDisabled = document.querySelectorAll(".disabled_inp");
     allDisabled.forEach( (dis)=>{
       dis.classList.remove("disabled_inp");
       dis.removeAttribute("disabled");
     });
  }
}

function addCalWait(event){
  event.preventDefault();
  const currentWait = document.querySelector("#action_loading_container2");
  const formActionsContainer = document.querySelector("#add_newcal_container");
  formActionsContainer.style.display = "none";
  currentWait.style.display = "block";
  event.target.submit();
}

</script>


</body>
</html>
