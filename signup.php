<?php
   ob_start();
   require_once('config.php');
   require_once('controllers/SignupController.php');
   $request_type = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';


   $signup_success = false;
   $signup_error = false;
   $used_calendar = array();
   try {
     global $pdo;
     $index_controller = new IndexController($pdo);
     $used_calendar = $index_controller->get_used_calendar();
     if (isset($used_calendar) && $used_calendar && !empty($used_calendar)){
       define('Calid', $used_calendar->get_id());
       define('TITLE', $used_calendar->get_title());
       define('DESCRIPTION', $used_calendar->get_description());
       define('THUMBNAIL', $used_calendar->get_background_image());
     }
   }
   catch( Exception $e ) {
     $used_calendar_emessage = $e->getMessage();
     $error = true;
   }


?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title><?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></title>
      <link rel="icon" href="<?php echo defined('THUMBNAIL') ? 'uploads/images/' . THUMBNAIL : 'uploads/images/default_logo.png'; ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
   </head>
   <style>
      body,html{height:100%;width:auto;margin:auto;padding:auto;}
      .main_page{height:100%;width:100%;max-width: 100% !important;}
      .aside_bg{z-index:-1;}
      .sign_up_btn{min-width: 46% !important;}
      .login_btn{min-width: 46% !important;}
      div.aside_menu_class{overflow: hidden !important;}
      @media only screen and (max-width: 600px) {
      .aside_menu_class {display:none !important;}
      .main_content_class {width:100% !important;}
      .btns_container{display:flex !important; flex-flow:column;}
      /* .sign_up_btn{display:block !mportant;margin-bottom:5px;width:100% !important;} */
      .sign_up_btn{width:100% !important;margin-bottom:10px !important;}
      .login_btn{width:100% !important;}
      }

      .aside_bg{
          /* Full height */
          height: 100%;
          /* Center and scale the image nicely */
          background-position: center;
          background-repeat: no-repeat;
          background-size: cover;
      }
   </style>
   <body>
      <!-- Control the column width, and how they should appear on different devices -->
      <div class="row main_page" style="margin-left:auto; margin-right:auto;">
         <!-- aside start -->
         <div class="aside_bg col-sm-9 bg-primary text-white aside_menu_class" style="background-image: url('uploads/images/signup_background.jpg');">
         </div>
         <!-- aside end -->
         <!-- sign up form start -->
         <div class="col-sm-3 bg-dark text-white main_content_class">
            <div class="container mt-3">
               <div class="text-center">
                  <h3 class="mt-2 mb-3">Sign Up</h3>
                  <div class="border border-light rounded">
                     <img class="aside_bg" src="uploads/images/signup_background.jpg" height="150" width="100%" >
                  </div>
               </div>
               <!-- note is this JPHPMVC sent the data to controller interninaly so it sent the requests to it self and pass to controller instead of let controller get request and then redirect to view-->
               <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="false">
                  <div class="mb-3 mt-3">
                     <label for="uname_signup">Username:</label>
                     <input type="text" autocomplete="new-username" class="form-control" id="uname_signup" placeholder="Enter Username" name="email" required>
                  </div>
                  <div class="mb-3">
                     <label for="pwd_signup">Password:</label>
                     <input type="password" autocomplete="new-password" class="form-control" id="pwd_signup" placeholder="Enter password" name="pswd" required>
                  </div>
                  <div class="mb-3 mt-3">
                     <label for="email_signup">Email:</label>
                     <input type="email" autocomplete="new-email" class="form-control" id="email_signup" placeholder="Enter email" name="email" required>
                  </div>
                  <div class="mb-3">
                     <label for="singup_name">Name:</label>
                     <input type="text" class="form-control" id="singup_name" placeholder="Enter Full Name" name="singup_name" required>
                  </div>
                  <div class="d-flex justify-content-between align-items-center btns_container flex-wrap">
                     <button type="submit" class="btn btn-outline-primary sign_up_btn">Register</button>
                     <button type="button" class="btn btn-outline-light login_btn">Login</button>
                  </div>
               </form>
            </div>
         </div>
         <!-- signup form end -->
      </div>
   </body>

   <style>

   </style>
</html>
