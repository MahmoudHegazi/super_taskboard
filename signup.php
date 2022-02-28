<?php
   ob_start();
   require_once('config.php');
   require_once('controllers/SignupController.php');
   $request_type = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';

   $signup_controler = null;
   $index_controller = null;
   $signup_success = false;
   $signup_error = false;
   $used_calendar = null;
   $error = false;


   try {
     global $pdo;
     $signup_controler = new SignupController($pdo, $request_type);
     $index_controller = new IndexController($pdo);
     $used_calendar = $index_controller->get_used_calendar();
     if (isset($used_calendar) && $used_calendar && !empty($used_calendar)){
       define('Calid', $used_calendar->get_id());
       define('TITLE', $used_calendar->get_title());
       define('DESCRIPTION', $used_calendar->get_description());
       define('THUMBNAIL', $used_calendar->get_background_image());
       define('SIGNBACKGROUND', $used_calendar->get_sign_background());
       define('REQUESTTOKEN', $signup_controler->get_request_token());
       // save the token in session
       if ($request_type == 'GET'){
         $_SESSION['request_token'] = $signup_controler->get_request_token();
       }
     }
   }
   catch( Exception $e ) {
     $used_calendar_emessage = $e->getMessage();
     $error = true;
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
      .max_width_200{
        max-height:200px !important;
      }
      .shadow_sign_title{box-shadow: 0 4px 8px 0 rgba(0, 55, 50, 0.2), 0 6px 20px 0 rgb(80 201 80 / 80%);}
      .shadow_sign_title1{box-shadow: 0 4px 8px 0 rgb(233 52 167 / 61%), 0 6px 20px 0 rgb(80 201 80 / 80%);}
   </style>
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
                  <h3 class="mt-2 mb-3">Sign Up</h3>
                  <div class="border border-light rounded">
                     <img class="aside_bg max_width_200"
                     src="<?php echo defined('SIGNBACKGROUND') ? 'uploads/images/' . SIGNBACKGROUND : 'uploads/images/signup_background.jpg'; ?>"
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
               <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="false">
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
