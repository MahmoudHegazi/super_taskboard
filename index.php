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


<div class="wrapper">
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
        <h3 class="text-center">Calendars</h3>
        <button class="btn btn-primary">Add New Calendar</button>
      </div>

    </div>
    <div class="p-2" style="">
    <div class="setup">
      <!-- Calendar Cards -->

        <!-- calendar card -->
          <div class="container p-2 cal-card" style="width:45%;height:200px;border: 2px solid black; height:fit-content;">
          <div class="mt-2 p-2 bg-primary text-white rounded">
              <h3 class="text-center">Calendar Titlte</h3>
              <img class="border border-light"
                  src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrlwdqUEbVMAXpTTe8yqVtITJMsFQegR1WiA&usqp=CAU" width="100%;" height="150px">
          </div>
            <button class="btn btn-warning mt-2 btn-block edit_calendar" data-calendar="1">Edit</button>
            <button class="btn btn-danger mt-2 btn-block remove_calendar" data-calendar="1" >Remove</button>
            <button style="float:right;" class="btn btn-success mt-2 btn-block default_calendar" data-calendar="1">Use</button>
          </div>

        <!-- calendar card end -->

        <!-- calendar card -->
          <div class="container p-2 cal-card" style="width:45%;height:200px;border: 2px solid black; height:fit-content;">
          <div class="mt-2 p-2 bg-primary text-white rounded">
              <h3 class="text-center">Calendar Titlte</h3>
              <img class="border border-light"
                  src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQoPlJAfYVhKHizib8ocpS9N774AhuoYYzmlg&usqp=CAU" width="100%;" height="150px">
          </div>
            <button class="btn btn-warning mt-2 btn-block edit_calendar">Edit</button>
            <button class="btn btn-danger mt-2 btn-block remove_calendar">Remove</button>
            <button style="float:right;" class="btn btn-success mt-2 btn-block default_calendar">Use</button>
          </div>

        <!-- calendar card end -->



      <!-- Calendar Cards End -->
  </div>
      <hr >
  <div  class="users_container">
   <!-- users -->

   <div class="container mt-3">
  <h3 class="text-center mb-3">User Manger</h3>
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
          <button data-user-id="1" class="btn btn-warning edit_user">Edit</button>
          <button class="btn btn-danger delete_user">Delete</button>
        </td>
      </tr>
      <tr>
        <td>Mary</td>
        <td>Moe</td>
        <td>mary@example.com</td>
        <td>
          <button data-user-id="2" class="btn btn-warning edit_user">Edit</button>
          <button class="btn btn-danger delete_user">Delete</button>
        </td>
      </tr>
      <tr>
        <td>July</td>
        <td>Dooley</td>
        <td>july@example.com</td>
        <td>
          <button data-user-id="3" class="btn btn-warning edit_user">Edit</button>
          <button class="btn btn-danger delete_user">Delete</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>

   <!-- users end -->
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


</body>
</html>
