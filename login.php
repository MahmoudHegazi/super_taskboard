<?php
   ob_start();
   require_once('config.php');
   require_once('controllers/LoginController.php');
   require_once('controllers/IndexController.php');
   $request_type = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';

   $login_controller = null;
   $index_controller = null;
   $used_calendar = null;
   $login_error = false;
   $error = false;

   try {
     global $pdo;
     $login_controller = new LoginController($pdo, $request_type);
     $index_controller = new IndexController($pdo);
     $used_calendar = $index_controller->get_used_calendar();
     if (isset($used_calendar) && $used_calendar && !empty($used_calendar)){
       define('Calid', $used_calendar->get_id());
       define('TITLE', $used_calendar->get_title());
       define('DESCRIPTION', $used_calendar->get_description());
       define('THUMBNAIL', $used_calendar->get_background_image());
       define('SIGNBACKGROUND', $used_calendar->get_sign_background());
       define('REQUESTTOKEN', $login_controller->get_request_token());
       // save the token in session
       if ($request_type == 'GET'){
         $_SESSION['request_token'] = $login_controller->get_request_token();
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
     $login_controller->postHandler($login_controller, $_POST, $_SESSION, $redirect_url, $error);
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
                  <h3 class="mt-2 mb-3">Login</h3>
                  <div class="border border-light rounded">
                     <img class="aside_bg max_width_200"
                     src="<?php echo defined('SIGNBACKGROUND') ? 'uploads/images/' . SIGNBACKGROUND : 'uploads/images/signup_background.jpg'; ?>"
                     height="200" width="100%"
                     >
                  </div>
                  <!-- display error message -->
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
               <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="true">
                  <div class="mb-3 mt-3">
                     <label for="email">Username:</label>
                     <input type="text" autocomplete="current-username" class="form-control" id="login_username"
                      placeholder="Enter Username" name="login_username" maxlength="34" required>
                  </div>
                  <div class="mb-3">
                     <label for="pwd">Password:</label>
                     <input type="password" autocomplete="current-password" class="form-control" id="login_pwd" placeholder="Enter password" name="login_pwd"
                     pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$" title="(Password must contains 8 characters and contains at least 1 number, 1 small letter, 1 capital letter, and symobol [!@#$%^*_=+-]) EG: 1aaqQ@dd"
                     maxlength="320" required>
                  </div>

                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me" title="Click Here And system will remeber you for 7 days"> Remember me
                  </label>

                  <input type="hidden" style="display:none;"
                        name="request_token" value="<?php echo defined('REQUESTTOKEN') ? REQUESTTOKEN : '' ?>" required>

                  <div class="mt-3 d-flex justify-content-between align-items-center btns_container flex-wrap">
                    <button type="button" class="btn btn-outline-light sign_up_btn">Register</button>
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

   <script>
   /* secuirty js some code for secuirty and logins details */
   function detectBrowser(userAgent) {
       // fast way to get choice from user agent
       if((userAgent.indexOf("Opera") || user_agent.indexOf('OPR')) != -1 ) {
           return 'Opera';
       } else if(userAgent.indexOf("Chrome") != -1 ) {
           return 'Chrome';
       } else if(user_agent.indexOf("Safari") != -1) {
           return 'Safari';
       } else if(user_agent.indexOf("Firefox") != -1 ){
           return 'Firefox';
       } else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) {
           return 'IE';//crap
       } else {
           return 'Unknown';
       }
   }

   function detectOS(appVersion){
       if (appVersion.indexOf("Win") != -1){
         return "Windows OS";
       } else if (appVersion.indexOf("Mac") != -1) {
         return "MacOS";
       } else if (appVersion.indexOf("X11") != -1){
         return "UNIX OS";
       }
       else if (appVersion.indexOf("Linux") != -1){
         return "Linux OS";
       } else {
         return "Unknown";
       }
   }


   async function getLoginData(){
     const secuirtyData = await getLogSecuirties();
     const userLogObj = {
       'browser': detectBrowser(navigator.userAgent),
       'os': detectOS(navigator.appVersion),
       'cookies_enabled': navigator.cookieEnabled,
       'browser_language': navigator.language,
       'loc': secuirtyData['loc'],
       'login_ip': secuirtyData['login_ip']
     }
     return userLogObj;
   }

   async function sendLoginData(){

     getLoginData().then( (data)=>{
       console.log(data);
     });
     return true;
   }


   async function getLogSecuirties(){
     const result = {loc: 'Unknown', login_ip: 'Unknown'};
     const res = await fetch('https://www.cloudflare.com/cdn-cgi/trace');


       const resText = await res.text();
       const data = getSecuirtiesJSON(resText);
       result['loc'] = !data.loc || data.loc == '' ? 'Unknown' : data.loc;
       result['login_ip'] = !data.ip || data.ip == '' ? 'Unknown' : data.ip;
     return result;
   }

   function getSecuirtiesJSON(data){
     /* create js object from array accoriding to rule new line sperated */
     data = data.trim().split('\n').reduce(function(obj, pair) {
       pair = pair.split('=');
       return obj[pair[0]] = pair[1], obj;
     }, {});
     return data;
   }


   sendLoginData();
   </script>
</html>
