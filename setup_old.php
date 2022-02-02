<?php
$target_dir = "uploads/images";



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



<div class="container-fluid p-5 bg-primary text-white text-center">
  <h1>Calendar Title</h1>
  <p>Calendar Description...</p>
</div>


<div class="container-fluid wrapper">
  <main>

    <div class="toolbar">
      <!--
      <div class="toggle">
        <div class="toggle__option">week</div>
        <div class="toggle__option toggle__option--selected">month</div>
      </div>
      <div class="current-month">June 2016</div>
      <div class="search-input">
        <input class="form-control" type="text" value="What are you looking for?">
        <i class="fa fa-search btn btn-primary"></i>
      </div>
      -->
      <div class="container">
        <div class="container">
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <strong>Success!</strong> This alert box could indicate a successful or positive action.
          </div>
        </div>
        <h3 class="text-center">Calendars</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCalendar">Add New Calendar</button>
      </div>

    </div>
    <div class="p-2">
    <div class="setup p-3">
      <!-- Calendar Cards -->

        <!-- calendar card -->
          <div class="cal container p-2 cal-card border border-secondary rounded-start" style="width:45%;height:200px;height:fit-content;">
          <div class="mt-2 p-2 bg-primary text-white rounded cal_content">
              <h3 class="text-center cal_title">Calendar Titlte</h3>
              <img class="border border-light"
                  src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrlwdqUEbVMAXpTTe8yqVtITJMsFQegR1WiA&usqp=CAU" width="100%;" height="150px">
          </div>
            <button data-bs-toggle="modal" data-bs-target="#editCalendar" class="btn btn-warning mt-2 btn-block edit_calendar" data-calendar="1">Edit</button>
            <button data-bs-toggle="modal" data-bs-target="#removeCalendar" class="btn btn-danger mt-2 btn-block remove_calendar" data-calendar="1" >Remove</button>
            <button style="float:right;" class="btn btn-success mt-2 btn-block default_calendar" data-calendar="1">Use</button>
          </div>

        <!-- calendar card end -->

      <!-- Calendar Cards End -->
  </div>
      <hr >
  <div  class="users_container">
   <!-- users -->

   <div class="container mt-3">
  <h3 class="text-center mb-3">User Manger</h3>
  <button class="btn btn-block btn-primary mb-2" style="width:100%;" data-bs-toggle="modal" data-bs-target="#addUser">Add New User</button>
  <table class="table table-dark table-striped table-hover">
    <thead>
      <tr>
        <th>Firstname</th>
        <th>username</th>
        <th>Email</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>John</td>
        <td>Doe</td>
        <td>john@example.com</td>
        <td>
          <button data-user="1" data-bs-toggle="modal" data-bs-target="#editUser"  class="btn btn-warning edit_user">Edit</button>
          <button data-user="1" data-bs-toggle="modal" data-bs-target="#removeUser" class="btn btn-danger delete_user">Delete</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>

   <!-- users end -->
  </div>
  </div>
  </main>

    <sidebar>
    <div class="logo">calendar title</div>
    <div class="avatar">
      <div class="avatar__img">
        <img src="https://picsum.photos/70" alt="avatar">
      </div>
      <div class="avatar__name">Menu</div>
    </div>
    <nav class="menu">
      <a class="menu__item p-3" href="#">
        <i class="menu__icon fa fa-home"></i>
        <span class="menu__text">Home</span>
      </a>
      <a class="menu__item menu__item--active p-3" href="#">
        <i class="menu__icon fa fa-calendar"></i>
        <span class="menu__text">Setup</span>
      </a>
      <a class="menu__item" href="#">
        <i class="menu__icon fa fa-bar-chart"></i>
        <span class="menu__text">Reports</span>
      </a>
      <a class="menu__item" href="#">
        <i class="menu__icon fa fa-sign-out"></i>
        <span class="menu__text">Logout</span>
      </a>
    </nav>
  </sidebar>


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
          <input type="number" name="period_per_day" id="period_per_day" min="1" value="3" class="form-control" required>
        </div>

         <div class="form-group mt-2" style="display:none;" id="periods_container">
        </div>

        <hr />

        <div class="form-group mt-2">
          <label for="slots_per_period">Slots Per Period: </label>
          <input type="number" name="slots_per_period" id="slots_per_period" min="1" value="3" class="form-control" required>
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
  </form>
  </div>
</div>
<!-- Add Calendar Model End -->

<!-- Edit Calendar Model -->
<div class="modal" id="editCalendar">
  <div class="modal-dialog">
  <form>
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Calendar</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="form-group">
          <label for="calendar_title_edit">Calendar Title: </label>
          <input maxlength="30" size="30" type="text" name="calendar_title_edit" id="calendar_title_edit" class="form-control" placeholder="Enter Calendar Title" required>
        </div>

        <div class="form-group mt-2">
         <label for="calendar_description_edit">Calendar Description: </label>
         <textarea maxlength="100" placeholder="Enter Calendar Description" class="form-control" name="calendar_description_edit" id="calendar_description_edit"></textarea>
        </div>

        <div class="form-group mt-2">
          <label for="background_image_edit">Calendar Background</label>
          <input type="file" name="background_image" id="background_image_edit" min="0" value="0" class="form-control">
        </div>

        <div class="container mt-2">
          <h3 class="text-center mt-2">Add More Years</h3>
        </div>
        <div class="form-group">
          <label for="add_new_year_edit">Years Added: </label>
          <input type="number" name="add_new_year_edit" id="add_new_year_edit" min="0" value="0" class="form-control" title="if you leave this input will not effect the original years added">
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
        <form>
          <button type="submit" class="btn btn-info">Remove Calendar</button>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Delete Calendar Model End -->


<!-- Add User Model -->
<div class="modal" id="addUser">
  <div class="modal-dialog">
    <form>
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
<!-- Delete Calendar Model End -->

<!-- Edit User Model -->
<div class="modal" id="editUser">
  <div class="modal-dialog">
    <form>
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
          <label for="password_edit">Password: </label>
          <input type="password" name="password_edit" id="password_edit" auto-complete="current-password"  class="form-control" placeholder="Enter User Password">
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
        <form>
          <button type="button" class="btn btn-info" data-bs-dismiss="modal">Remove User</button>
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
</script>


</body>
</html>
