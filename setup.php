<?php
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
           data-description="<?php echo $cal_description; ?>">Edit</button>

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
    <form action="controllers/setup_controller.php" method="POST" enctype="multipart/form-data">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add New Calendar</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

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
        <div class="container mt-3 mb-2 text-center"><h4 class="badge bg-info p-2">Control Periods And Slots (Additonal)</h4></div>

         <div class="form-group mt-2">
          <label for="period_per_day">Periods Per Day: </label>
          <input type="number" name="period_per_day" id="period_per_day" min="0" value="3" class="form-control" required>
        </div>

         <div class="form-group mt-2" style="display:none;" id="periods_container">
        </div>

        <hr />

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
          <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
      </div>

    </div>
  </form>
  </div>
</div>
<!-- Add Calendar Model End -->

<!-- Edit Calendar Model -->
<div class="modal" id="editCalendar">
  <div class="modal-dialog">

    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Mange Calendar</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="container border p-4 m-2 border-secondary">
          <h4 class="text-center">Main Info</h4>
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

              <button type="submit" class="btn btn-success btn-block mt-2" data-bs-dismiss="modal">Edit Calendar Data</button>
            </div>
          </form>
        </div>

        <div class="container border p-4 m-2 border-secondary">
          <h4 class="text-center mt-2">Add More Years</h4>
          <form action="controllers/setup_controller.php" method="POST">
            <div class="form-group">
              <label for="add_new_year_edit">Years Added: </label>
              <input type="number" name="add_new_year_edit" id="add_new_year_edit" min="1" value="1" class="form-control" title="if you leave this input will not effect the original years added" required>
            </div>
            <div class="form-group text-center">
              <input type="hidden" value="" name="years_added_calid" id="years_added_calid" style="display:none;" />
              <input type="submit" class="btn btn-primary text-white mt-2" value="Add Years"/>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
        <h4 class="modal-title">Delete Calendar</h4>
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
          <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
        </div>

      </div>
    </form>
  </div>
</div>
<!-- Delete Calendar Model End -->

<!-- Edit User Model -->
<div class="modal" id="editUser">
  <div class="modal-dialog">
    <form action="controllers/setup_controller.php" method="POST" autocomplete="off">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Edit User</h4>
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
        <h4 class="modal-title">Delete User</h4>
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

<script>

/* Mange Periods In form start */

const period_input = document.querySelector("#period_per_day");
const periodContainer = document.querySelector("#periods_container");


period_input.addEventListener( "input", mange_period_inputs );

function create_period_inputs(count){
  if (count < 1){return false;}
  let index = 1;
  let html_inputs = '';
  for (let i=0; i<count; i++){
  html_inputs += `
<div class="form-group border border-dark mt-1 p-2 bg-light text-black">
  <h5 class="text-center badge bg-success text-white">Period ${index}</h5>
  <div class="row">
    <div class="col-sm-6">
      <label for="period_date_${index}">Period DateTime: </label>
      <input required name="period_date_${index}" data-index="${index}" type="datetime-local" class="form-control period-date period_input" />
    </div>
    <div class="col-sm-6">
      <label for="period_description_${index}">Period Description</label>
      <input maxlength="50" size="50" name="period_description_${index}" type="text" class="form-control period-description period_input" data-index="${index}" placeholder="Enter Period Description" />
    </div>
  </div>
</div>
<hr />
  `;
  index += 1;
  }
 return html_inputs;

}

function mange_period_inputs(event){
  let period_inputs = create_period_inputs(event.target.value);
  if (period_inputs){
    periodContainer.innerHTML = period_inputs;
    periodContainer.style.display = "block";
  } else {
    periodContainer.innerHTML = '';
    periodContainer.style.display = "none";
  }

}

let period_inputs = create_period_inputs(period_input.value);
if (period_inputs){
  periodContainer.innerHTML = period_inputs;
  periodContainer.style.display = "block";
}


/* Mange Periods In form end */




/* Mange Slots In form start */

const slot_input = document.querySelector("#slots_per_period");
const slotContainer = document.querySelector("#slots_container");


slot_input.addEventListener( "input", mange_slot_inputs );

function create_slot_inputs(count){
  if (count < 1){return false;}
    let index = 1;
    let html_inputs = '';
    for (let i=0; i<count; i++){
    html_inputs += `
<div class="form-group border border-dark mt-1 p-2 bg-light text-black">
  <h5 class="text-center badge bg-success text-white">Slot ${index}</h5>
  <div class="row">
        <div class="col-sm-6">
      <label for="start_at_slot">Start At: </label>
      <input required name="start_at_slot_${index}" data-index="a${index}" type="time" class="form-control slot-start-at slot_input" />
    </div>
    <div class="col-sm-6">
      <label for="end_at_slot">End At: </label>
      <input required name="end_at_slot_${index}" data-index="${index}" type="time" class="form-control slot-end-at slot_input" />
    </div>
  </div>
</div>
    `;
    index += 1;
  }
 return html_inputs;

}

function mange_slot_inputs(event){
  let slots_inputs = create_slot_inputs(event.target.value);
  if (slots_inputs){
    slotContainer.innerHTML = slots_inputs;
    slotContainer.style.display = "block";
  } else {
    slotContainer.innerHTML = '';
    slotContainer.style.display = "none";
  }

}

let slots_inputs = create_slot_inputs(period_input.value);
if (slots_inputs){
  slotContainer.innerHTML = slots_inputs;
  slotContainer.style.display = "block";
}


/* Mange Slots In form end */

/* remove calendar */
const removeCalendarBtns = document.querySelectorAll(".remove_calendar");
const calendarIdDelete = document.querySelector("#remove_calendar_id");
removeCalendarBtns.forEach( (removeBtn)=>{
  removeBtn.addEventListener("click", (event)=>{
    calendarIdDelete.value = event.target.getAttribute("data-calendar");
  });
});

/* remove user */
const removeUserBtns = document.querySelectorAll(".delete_user");
const removeUserId = document.querySelector("#remove_user_id");
removeUserBtns.forEach( (removeBtn)=>{
  removeBtn.addEventListener("click", (event)=>{
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
editUserBtns.forEach( (editBtn)=>{
  editBtn.addEventListener("click", (event)=>{
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

editCalendarBtns.forEach( (editBtn)=>{
  editBtn.addEventListener("click", (event)=>{
    calendarTitleEdit.value = event.target.getAttribute("data-title");
    calendarDescriptionEdit.value = event.target.getAttribute("data-description");
    calendarUseridEdit.value = event.target.getAttribute("data-calendar");
    addedYearCalId.value = event.target.getAttribute("data-calendar");
  });
});
/* edit calendar end */


const toggleEditPass = document.querySelector("#toggle_edit_pass");

toggleEditPass.addEventListener("change", (event)=>{
  console.log(event.target);
  if (event.target.checked == true){
    if (passwordInputEdit.hasAttribute("disabled")){
      passwordInputEdit.removeAttribute("disabled");
    }
    if (!passwordInputEdit.hasAttribute("required")){
      passwordInputEdit.setAttribute("required", true);
    }
  } else {
    if (!passwordInputEdit.hasAttribute("disabled")){
      passwordInputEdit.setAttribute("disabled", true);
    }
    if (passwordInputEdit.hasAttribute("required")){
      passwordInputEdit.removeAttribute("required");
    }
  }
});


</script>


</body>
</html>
