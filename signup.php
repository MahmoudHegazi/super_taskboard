<?php
   ob_start();
   require_once('config.php');
   require_once('controllers/SignupController.php');
   $request_type = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';

   $signup_controler = null;
   $index_controller = null;
   $signup_success = false;
   $signup_error = false;
   $used_calendar = false;
   $error_message = '';
   $error = false;
   $default_logo = 'uploads/images/default_logo.png';
   $default_signupbg = 'uploads/images/signup_background.jpg';


   try {
     global $pdo;
     $signup_controler = new SignupController($pdo, $request_type);
     $used_calendar = $signup_controler->get_used_calendar();
     if (isset($used_calendar) && $used_calendar && !empty($used_calendar)){
       define('TITLE', $used_calendar->get_title());
       define('DESCRIPTION', $used_calendar->get_description());
       define('REQUESTTOKEN', $signup_controler->get_request_token());
       $default_logo = 'uploads/images/' . $used_calendar->get_background_image();
       $default_signupbg = 'uploads/images/' . $used_calendar->get_sign_background();
       // save the token in session
       if ($request_type == 'GET'){
         $_SESSION['request_token'] = $signup_controler->get_request_token();
       }
     }
   }
   catch( Exception $e ) {
     $error_message = $e->getMessage();
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
         $error_message
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
     $redirect_url = $_SERVER['HTTP_REFERER'];
     $signup_controler->postHandler($signup_controler, $_POST, $_SESSION, $redirect_url, $error);
     die();
   }

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title><?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></title>
      <link rel="icon" href="<?php echo $default_logo; ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <script src="assets/js/jquery-3.5.1.min.js"></script>
      <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
      <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
      <link href="assets/css/signup.css" rel="stylesheet">
   </head>
   <body>
      <!-- Control the column width, and how they should appear on different devices -->

      <div class="row main_page" style="margin-left:auto; margin-right:auto;">
         <!-- aside start -->
         <div class="d-flex justify-content-center align-items-center aside_bg col-sm-9 bg-primary text-white aside_menu_class" style="background-image: url('<?php echo $default_signupbg; ?>');">
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
                  <h3 class="mt-2 mb-3">Sign Up</h3>
                  <div class="border border-light rounded">
                     <img class="aside_bg max_width_200"
                     src="<?php echo $default_signupbg; ?>"
                     height="200" width="100%"
                     >
                  </div>
                  <!-- display error message -->
                  <!-- in case no used were calendar found -->
                  <?php if(isset($_SESSION['message_signup']) && isset($_SESSION['success_signup']) && !empty($_SESSION['message_signup'])){ ?>
                    <div class="mt-2 mb-2 alert alert-<?php echo $_SESSION['success_signup'] ? 'success' : 'danger'; ?> alert-dismissible fade show">
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      <p class="text-center">
                        <?php echo $_SESSION['message_signup']; ?>
                      </p>
                    </div>
                  <?php unset($_SESSION['message_signup']);unset($_SESSION['success_signup']);} ?>
               </div>
               <!-- note is this JPHPMVC sent the data to controller interninaly so it sent the requests to it self and pass to controller instead of let controller get request and then redirect to view-->
               <form method="POST" action="signup.php" autocomplete="false">
                  <div class="mb-3 mt-3">
                     <label for="uname_signup">Username:</label>
                     <input type="text" autocomplete="new-username" class="form-control" id="uname_signup"
                     placeholder="Enter Username" name="uname_signup" maxlength="34" required>
                  </div>
                  <div class="mb-3">
                     <label for="pwd_signup">Password:</label>
                     <!-- notice maxlenth not for the length of user selected password as it will at end have limited size encyption but this for request fast -->
                     <input type="password" autocomplete="new-password" class="form-control" id="pwd_signup"
                     placeholder="Enter password" name="pwd_signup" maxlength="320"
                     pattern="{8,255}" title="(Password must contains 8 characters up to 255 must and 3 of characters must be unqiue )"
                     required>
                  </div>
                  <div class="mb-3 mt-3">
                     <label for="email_signup">Email:</label>
                     <input type="email" maxlength="80" title="maxLength is 80 letters" autocomplete="new-email"
                      class="form-control" id="email_signup" placeholder="Enter email" name="email"
                      required>
                  </div>
                  <div class="mb-3">
                     <label for="singup_name">Name:</label>
                     <input type="text" class="form-control" id="singup_name" placeholder="Enter Full Name"
                     name="singup_name" maxlength="30" required>
                  </div>

                  <input type="hidden" style="display:none;"
                        name="request_token" value="<?php echo defined('REQUESTTOKEN') ? REQUESTTOKEN : '' ?>" required>

                  <div class="d-flex justify-content-between align-items-center btns_container flex-wrap">
                     <button type="submit" class="btn btn-primary sign_up_btn">Register</button>
                     <a href="./login.php" type="button" class="btn btn-outline-light login_btn">Login</a>
                  </div>
               </form>
            </div>

         </div>
         <!-- signup form end -->
      </div>
   </body>
</html>
