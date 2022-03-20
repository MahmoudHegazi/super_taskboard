<?php
   ob_start();
   $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
   require_once('config.php');
   require_once('controllers/LoginController.php');
   $request_type = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';

   $login_controller = null;
   $used_calendar = null;
   $login_error = false;
   $error = false;
   $login_error_message = '';
   $remember_medata = array();

   try {
     global $pdo;
     $login_controller = new LoginController($pdo, $request_type, $redirect_url);
     $used_calendar = $login_controller->get_used_calendar();
     if (isset($used_calendar) && $used_calendar && !empty($used_calendar)){
       define('TITLE', $used_calendar->get_title());
       define('DESCRIPTION', $used_calendar->get_description());
       define('THUMBNAIL', $used_calendar->get_background_image());
       define('SIGNBACKGROUND', $used_calendar->get_sign_background());

       $remember_medata = $login_controller->remember_me_handle($_COOKIE);
       // save the token in session
     }

      define('REQUESTTOKEN', $login_controller->get_request_token());
      if ($request_type == 'GET'){
       $_SESSION['request_token'] = $login_controller->get_request_token();
      }
   }
   catch( Exception $e ) {
     $login_error = true;
     $login_error_message = $e->getMessage();
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
         $login_error_message
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

   // send internal post request to signup Controller class
   if ($_SERVER['REQUEST_METHOD'] === 'POST'){
     // This How The SuperMVC handle all view with all needed given post requests
     $login_controller->postHandler($login_controller, $_POST, $_SESSION, $redirect_url, $error);
     die();
   } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($login_controller)){
     $_SESSION['ajax_token'] = $login_controller->get_request_token();
   } else {
     die();
   }

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title><?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></title>
      <link rel="icon" href="<?php echo defined('THUMBNAIL') ? 'uploads/images/' . THUMBNAIL : 'uploads/images/default_logo.png'; ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <script src="assets/js/jquery-3.5.1.min.js"></script>
      <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
      <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
      <?php

      if (isset($_SESSION['ajax_token'])){
        echo '<script>const ajax_token="' . $login_controller->get_request_token() . '";</script>';
      }
      ?>
   </head>
   <link href="assets/css/login.css" rel="stylesheet">
   <body>
      <!-- Control the column width, and how they should appear on different devices -->

      <div class="row main_page" style="margin-left:auto; margin-right:auto;">
         <!-- aside start -->
         <div class="d-flex justify-content-center align-items-center aside_bg col-sm-9 bg-primary text-white aside_menu_class" style="background-image: url('<?php echo defined('SIGNBACKGROUND') ? 'uploads/images/' . SIGNBACKGROUND : 'uploads/images/signup_background.jpg'; ?>');">
           <h3 class="mt-2 mb-3 badge bg-dark p-2 shadow_sign_title1">
              <!-- calendar title -->
              <span class="display-6 p-3"><?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></span>
           </h3>
         </div>


         <!-- aside end -->
         <!-- sign up form start -->
         <div class="col-sm-3 bg-dark text-white main_content_class">
            <div class="container mt-3">
               <div class="text-center">
                  <h3 class="mt-2 mb-3">Login</h3>
                  <div class="border border-light rounded">
                     <img class="aside_bg max_width_200" src="<?php echo defined('SIGNBACKGROUND') ? 'uploads/images/' . SIGNBACKGROUND : 'uploads/images/signup_background.jpg'; ?>" height="200" width="100%">
                  </div>
                  <!-- display system error messages -->
                  <?php if($login_error){ ?>
                    <div class="alert alert-danger">
                      <p class="text-center"><strong>Warning!</strong> <?php echo $login_error_message; ?>
                        <!-- check if admin and display link to setup -->
                        <div class="d-flex justify-content-center align-items-center">
                          <a href="setup.php" class="btn btn-outline-primary">Go To Setup</a>
                        </div>
                      </p>
                    </div>
                  <?php die();} ?>
                  <!-- display user error message -->
                  <!-- in case no used were calendar found -->
                  <?php if(isset($_SESSION['message_login']) && isset($_SESSION['success_login']) && !empty($_SESSION['message_login'])){ ?>
                    <div class="mt-2 mb-2 alert alert-<?php echo $_SESSION['success_login'] ? 'success' : 'danger'; ?> alert-dismissible fade show">
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      <p class="text-center">
                        <?php echo $_SESSION['message_login']; ?>
                      </p>
                    </div>
                  <?php unset($_SESSION['message_login']);unset($_SESSION['success_login']);} ?>
               </div>

               <!-- note is this JPHPMVC sent the data to controller interninaly so it sent the requests to it self and pass to controller instead of let controller get request and then redirect to view-->
               <form id="login_form" method="POST" action="login.php" autocomplete="true">
                  <div class="mb-3 mt-3">
                     <label for="email">Username:</label>
                     <input type="text" autocomplete="current-username" class="form-control" id="login_username"
                      placeholder="Enter Username" name="login_username" maxlength="34" required>
                  </div>
                  <div class="mb-3">
                     <label for="pwd">Password:</label>
                     <input type="password" autocomplete="current-password" class="form-control" id="login_pwd" placeholder="Enter password" name="login_pwd"
                     pattern="{8,255}$" title="(Password must contains 8 characters up to 255 must and 3 of characters must be unqiue )"
                     maxlength="320" required>
                  </div>

                  <div id="display_remember_me" class="form-check mb-3 dis" style="display:none;">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" name="remember"> Remember me
                    </label>
                  </div>

                  <input type="hidden" style="display:none;"
                        name="request_token" value="<?php echo defined('REQUESTTOKEN') ? REQUESTTOKEN : '' ?>" required>

                  <div class="mt-3 d-flex justify-content-between align-items-center btns_container flex-wrap">
                    <a href="./signup.php" class="btn btn-outline-light sign_up_btn">Register</a>
                    <button type="submit" class="btn btn-primary login_btn">Login</button>
                  </div>
               </form>

               <div class="d-flex justify-content-center align-items-center mt-3">
                 <a href="reset_password" title="Forget Your Password do not worry Click here to reset your password" class="text-primary badge badge-light">Forget Your Password ?</a>
               </div>

            </div>

         </div>
         <!-- signup form end -->
      </div>
   </body>

   <?php
     if (isset($remember_medata) && !empty($remember_medata)  && count($remember_medata) == 5){

       if (
           isset($remember_medata['remember_me']) && !empty($remember_medata['remember_me'])  &&
           isset($remember_medata['username']) && !empty($remember_medata['username'])  &&
           isset($remember_medata['password']) && !empty($remember_medata['password']) &&
           $remember_medata['remember_me'] === True
         ){
             echo '<script>
             if ('.json_encode($remember_medata['remember_me']).' === true){
               const loginForm = document.getElementById("login_form");
               const loginUsername = document.getElementById("login_username");
               const loginPassword = document.getElementById("login_pwd");
               if ('.json_encode($remember_medata['username']).' != loginUsername.value){
                 loginUsername.value = '.json_encode($remember_medata['username']).';
               }
               if (!loginPassword.value){
                 loginPassword.value = "You Did not saved any passwords";
               }

               loginForm.submit();
             }
             </script>';

       }
     }
   ?>

   <script src="login.js" type="text/javascript"></script>
</html>
