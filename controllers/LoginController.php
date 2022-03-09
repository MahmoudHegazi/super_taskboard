<?php
ob_start();
require_once (dirname(__FILE__, 2) . '\config.php');
require_once (dirname(__FILE__, 2) . '\functions.php');
require_once (dirname(__FILE__, 2) . '\services\UserService.php');
require_once (dirname(__FILE__, 2) . '\services\LogsService.php');
require_once (dirname(__FILE__, 2) . '\services\CalendarService.php');
require_once (dirname(__FILE__, 2) . '\models\User.php');

class LoginController {
  protected $pdo;
  protected $calendar_service;
  protected $user_service;
  protected $request_type;
  protected $login_success;
  protected $wrong_logins;
  protected $cal_id;
  private $request_token;
  protected $redirect_url;
  protected $appname;
  protected $used_calendar;



  public function __construct(PDO $pdo, $request_type='GET', $cal_id=NULL, $redirect_url=NULL)
  {

    $this->pdo = $pdo;
    $this->user_service = new UserService($pdo);
    $this->logs_service = new LogsService($pdo);
    $this->calendar_service = new CalendarService($pdo);



    $used_cal = $this->set_used_calendar($this->assign_used_calendar());
    $used_cal = $this->get_used_calendar();

    if (!isset($used_cal) || empty($used_cal)){
      throw new Exception( "No Calendars Created Please Create Calendar First and it will marked as used automatic Erro 01" );
    }

    if (!isset($this->calendar_service) || empty($this->calendar_service)){
      throw new Exception( "Can not Get used calendar data please create calendar from admin setup page and try again" );
    }


    $cal_id= $used_cal->get_id();

    $appname = defined('APPNAME') ? APPNAME : 'supercalendar';
    $this->set_app_name($appname);

    $this->set_request_type($request_type);
    $this->set_login_success(false);
    $this->set_cal_id($cal_id);
    $this->set_redirect_url(!is_null($redirect_url) ? $redirect_url : isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

    if ($request_type == 'GET'){
      $this->set_request_token($this->getnerate_request_secert());
    }

  }

  public function set_app_name($app_name){
    $this->app_name = $app_name;
  }

  public function get_app_name(){
    return $this->app_name;
  }

  public function set_cal_id($cal_id){
    $this->cal_id = $cal_id;
  }

  public function get_cal_id(){
    return $this->cal_id;
  }

  public function set_used_calendar($used_calendar){
    $this->used_calendar = $used_calendar;
  }

  public function get_used_calendar(){
    return $this->used_calendar;
  }


  public function set_redirect_url($redirect_url){
    $this->redirect_url = $redirect_url;
  }

  public function get_redirect_url(){
    return $this->redirect_url;
  }

  public function set_request_type($request_type){
    $this->request_type = $request_type;
  }

  public function get_request_type(){
    return $this->request_type;
  }

  public function set_request_token($request_token){
    $this->request_token = $request_token;
  }

  public function get_request_token(){
    return $this->request_token;
  }

  public function get_user($user){
    $this->user = $user;
  }

  public function set_user(){
    return $this->user;
  }

  public function get_login_success($login_success){
    $this->login_success = $login_success;
  }

  public function set_login_success(){
    return $this->login_success;
  }

  public function get_wrong_logins($wrong_logins){
    $this->wrong_logins = $wrong_logins;
  }

  public function set_wrong_logins(){
    return $this->wrong_logins;
  }

  // this fast check for password also make sure if some one accessed db
  /* ################## Setup and checks section ##################### */
  public function is_user_exist($username, $email){
    // for better UX make login with username or email
    $message = '';
    $exist = true;
    $type = 'Unkown';

    $is_email_exist = $this->user_service->get_user_where('email', $email);
    $is_username_exist = $this->user_service->get_user_where('username', $username);

    if (empty($is_username_exist) && empty($is_email_exist)){
      $exist = false;
      $type = 'Unkown';
    }


    if (!empty($is_username_exist)){
      $exist = true;
      $type = 'username';
    }

    if (!empty($is_email_exist)){
      $exist = true;
      $type = 'email';
    }

    $message = $exist ?  'User Found On System' : 'User not exist Invalid Username/Email';
    return array('exist'=>$exist, 'message'=>$message, 'type'=>$type);
  }

  public function return_user($username){
    return array();
  }

  /* ################## Redirection section ##################### */

  public function redirect_user_with_message($redirect_url, $message, $status){
    $_SESSION['message_login'] = $message;
    $_SESSION['success_login'] = $status;
    header("Location: " . $redirect_url);
  }



  ###################### Post Handler ############################
  public function postHandler($login_controler, $post_obj, $session_obj, $redirect_url, $error){
     $post_request = false;
     $ajax_request = false;
     $ajax_data = array();
     // this bridge for direct post AJAX requests to AJAX handler
     try{
       $data = json_decode(file_get_contents('php://input'), true);
       $ajax_request = isset($data) && !empty($data);
       $ajax_data = $ajax_request ? $data : $ajax_data;
     }catch(Exception $ex){
        $post_request = true;
        $ajax_request = false;
     }

     if ($ajax_request){
       $login_controler->ajaxHandler($login_controler, $data, $session_obj);
     } else {
       // Secure Any POST request to the login controller from remote attack (Required Form only) when use AJAX better to get secuirty token with ajax first and store it with session then add it to your request else no AJAX will made this is JMVCPHP rules
       if (!isset($session_obj['request_token']) || !isset($post_obj['request_token']) || empty($post_obj['request_token']) || empty($session_obj['request_token']) || ($session_obj['request_token'] != $post_obj['request_token'])){
         //return error 00;
         $this->redirect_user_with_message($redirect_url, 'Your request is denied.', false);
         die();
       }

      // handle if invalid setup due to code error
      if (empty($this->user_service)){
        //return error 01;
        $this->redirect_user_with_message($redirect_url, 'a fatal error occurred while inital system Code:001', false);
        die();
      }

      /* Login Form POST request */
      if (
          isset($_POST['login_username']) && isset($_POST['login_pwd'])
      ) {
        $username = test_input($_POST['login_username']);
        $password = test_input($_POST['login_pwd']);
        $rememberme_input = isset($_POST['remember']) && !empty(isset($_POST['remember'])) ? 1 : 0;
        $remember_me_token= $rememberme_input == 1 ? $this->generate_remeber_token() : NULL;
        $rememberme = False;
        $logid = 0;

        /* !!!!########## Cookies Handle secured and browser friendly  ###########!!! */
        $remember_me_date = $this->remember_me_handle($_COOKIE);
        if (
          $remember_me_date['remember_me'] === True &&
          isset($remember_me_date['username']) && isset($remember_me_date['password']) &&
          !empty($remember_me_date['username']) && !empty($remember_me_date['password'])
        ) {
          $rememberme = True;
          $username = $remember_me_date['username'];
          $password = $remember_me_date['password'];
          $logid = $remember_me_date['logid'];
        }



        // check if he remember me user

        // here -------------------------------->>>>>>>>>>>>>>>>>>>>>>

        // check user exist and get type of submited username or email
        $user_exist_check = $this->is_user_exist($username, $username);
        $type = '';
        if (!$user_exist_check['exist']){
          $this->redirect_user_with_message($redirect_url, $user_exist_check['message'], $user_exist_check['exist']);
          die();
        }

        // identify which user submited email or username
        $type = $user_exist_check['type'];

        // get user Obj private
        $user_obj = $this->user_service->get_user_data_where($type, $username);

        /* valiate pass and user  */
        $valid_login = $this->is_valid_credentials($user_obj, $username, $password, $type, $rememberme);



        $encrypted_userid =  $this->encrypt_remeber_me_token($user_obj->get_id(), $remember_me_token);

        $user_id = test_input($user_obj->get_id());
        $user_username = test_input($user_obj->get_username());
        $user_email = test_input($user_obj->get_email());
        $user_hashed_password = test_input($user_obj->get_hashed_password());
        $is_admin = test_input($user_obj->get_role()) == 1 ? True : False;


        /* check if user banned */
        $is_user_banned = $this->logs_service->get_user_banned_status($user_id);
        if ($is_user_banned && (isset($is_user_banned['banned']) && !empty($is_user_banned['banned']))){
          if ($is_user_banned['banned'] == 1){
            $this->redirect_user_with_message($redirect_url, 'Sorry, your account has been banned due to unusual behavior, please contact the administrator for more information.' , False);
            die();
          }
        }

        /* check if user blocked */
        $userblocked = False;
        $removeblock = False;
        $log_id = 0;
        $is_user_banned = $this->logs_service->get_user_blocked_status($user_id);
        if (isset($is_user_banned['blocked']) && !empty($is_user_banned['blocked'])){
          $userblocked = True;
          $log_id = $is_user_banned['id'];
          if ($is_user_banned['blocked'] == True){
            $block_end = $is_user_banned['block_end'];

            $currenttime = strtotime((new DateTime())->format("Y-m-d H:i:s"));
            $block_endtime = strtotime($is_user_banned['block_end']);
            $can_remove_block = $currenttime > $block_endtime;
            if ($can_remove_block){
              // if user passed the block end time remove block automatic
              $removeblock = True;
            } else {
              $removeblock = False;
            }
          }
        }

        // if user still blocked tell him and redirect
        if ($userblocked == True && $removeblock == False){
          $this->redirect_user_with_message($redirect_url, 'Sorry, your account has been blocked until: ' . $block_end , False);
          die();
        }

        if ($userblocked == True && $removeblock == True && $log_id != 0){
          // unblock user
          $unblockuser = $this->unblockUser($user_obj->get_id(), $log_id);
        }


          $invalid_limit = 60;
          $time = new DateTime();
          $request_date = $time->format("Y-m-d H:i:s");


          $get_time_before = $this->returnNowBefore($invalid_limit);
          $get_time_after = $this->returnNowAfter($invalid_limit);
          $invalids_before_hour = $this->logs_service->get_user_invalid_logs($get_time_before, $user_obj->get_id());

          // check if 10 invalid within 1 hour block him so when he log he had 10 invalid logs before one hour from now
          $before2days = $this->returnNowBefore(((60*24)*2));
          $invalid_before_2days = $this->logs_service->get_user_invalid_logs($before2days, $user_obj->get_id());

          // check ban case and ban if needed
          if ($invalid_before_2days >= 150) {
            // this user has invalid passwords for 150 times within 2 days with block 1 hour per 10 invalid so he recived 15 block within 2 days
            $added_log = $this->addLoginLog($user_id, $user_username, $user_email, $user_hashed_password, $is_admin, $post_obj, $session_obj, False, 'banned user', NULL, NULL, 1, $completed=0, $remember_me=0, $remember_me_token=$remember_me_token);
            $this->banUserBadLogin($user_id, $log_id);
            $this->redirect_user_with_message($redirect_url, 'Your Account Has Been Banned for unusual behavior Please contact admin', False);
            die();
          }




          // check invalid and block if needed
          if ($invalids_before_hour > 10){

            $last_user_blockedlog = $this->logs_service->get_user_lastblock_log($user_obj->get_id());

            if (isset($last_user_blockedlog) && !empty($last_user_blockedlog)){
              if ($userblocked == True && $removeblock == True && $log_id != 0){
                // unblock user
                $unblockuser = $this->unblockUser($user_obj->get_id(), $log_id);
                $this->redirect_user_with_message($redirect_url, $message, False);
                die();
              } else {
                $this->redirect_user_with_message($redirect_url, 'Your Account Blocked', False);
                die();
              }
            } else {
              $message = "You Have Enter invalid password for 10 times Your account has been blocked for 1 Hour";
              $new_block_end = $this->returnNowAfter($invalid_limit);
              $log_notes = 'blocked user';
              $added_log = $this->addLoginLog($user_id, $user_username, $user_email, $user_hashed_password, $is_admin, $post_obj, $session_obj, $valid_login, $log_notes, 1, $new_block_end, 0, $completed=0, $remember_me=0, $remember_me_token=$remember_me_token);
              $this->redirect_user_with_message($redirect_url, $message, False);
              die();
            }
          }

        if ($valid_login && $valid_login == True){

          // check if remeber me on

          $cookies_enabled = isset($session_obj['cookies_enabled']) && !empty($session_obj['cookies_enabled']) && !is_null($session_obj['cookies_enabled']) ? test_input($session_obj['cookies_enabled']) : 0;
          $cooke_ready = False;
          $completed = 1;
          if ($cookies_enabled == 1 && $rememberme_input == 1){

            if ($rememberme == False){
              $cooke_ready = setcookie('uid', $encrypted_userid, time() + (86400 * 30), $httponly=True);
            }

          }
          // if no remember me make token null if remember me make completed 0 and keep token
          if (!$cooke_ready){
            $remember_me_token = NULL;
          } else {
            $completed = 0;
          }
          // if logged with remember me  not add log like complete 0 but this better
          if (isset($rememberme) && !empty($rememberme) && isset($logid) && !empty($logid)){
            if ($rememberme === True && $logid !== 0){
              $index_url = getDynamicBaseUrl();
              $this->loguser($user_obj, $logid);
              header("Location: ./index.php");
              die();
            }
          }

          $added_log = $this->addLoginLog($user_id, $user_username, $user_email, $user_hashed_password, $is_admin, $post_obj, $session_obj, $valid_login, 'valid login', NULL, NULL, 0, 1, $rememberme_input, $remember_me_token);
          $index_url = getDynamicBaseUrl();
          $this->loguser($user_obj, $added_log);
          header("Location: ./index.php");
          die();
        } else {
          // invalid login but not passed limit within hour
          $message = 'Invalid User or Password Remaining: ' . (10 - intval($invalids_before_hour)) . ' Before Blocked';
          $added_log = $this->addLoginLog($user_id, $user_username, $user_email, $user_hashed_password, $is_admin, $post_obj, $session_obj, $valid_login, 'invalid login', NULL, NULL, 0, 1, 0, NULL);
          $this->redirect_user_with_message($redirect_url, $message , False);
          die();
        }

        }

        // post handler [POST NON AJAX] end
     }

    }

  /* AJAX handler */
  public function ajaxHandler($login_controler, $data, $session_obj){
    $ajax_request_token = test_input($data['ajax_token']);
    $secure_request = $ajax_request_token === $session_obj['request_token'];
    if (!$secure_request){
      return json_encode(array('code'=> 403, 'message'=> 'Access Deined'));
      die();
    }


    // get client info and store in session
    if (
      isset($data['browser']) && isset($data['os']) &&
      isset($data['cookies_enabled']) && isset($data['browser_language']) &&
      isset($data['loc']) &&
      isset($data['login_ip'])
    ){
      $r_browser = test_input($data['browser']);
      $r_os = test_input($data['os']);
      $_cookies_enabled = test_input($data['cookies_enabled']);
      $_browser_language = test_input($data['browser_language']);
      $_loc = test_input($data['loc']);
      $_login_ip = test_input($data['login_ip']);

      $_SESSION['browser'] = isset($r_browser) && !empty($r_browser) ? $r_browser : '';
      $_SESSION['os'] = isset($r_os) && !empty($r_os) ? $r_os : '';
      $_SESSION['cookies_enabled'] = isset($_cookies_enabled) && !empty($_cookies_enabled) ? $_cookies_enabled : '';
      $_SESSION['browser_language'] = isset($_browser_language) && !empty($_browser_language) ? $_browser_language : '';
      $_SESSION['loc'] = isset($_loc) && !empty($_loc) ? $_loc : '';
      $_SESSION['login_ip'] = isset($_login_ip) && !empty($_login_ip) ? $_login_ip : '';
      print_r(json_encode(array('code'=>200, 'cookies_enabled'=>$_SESSION['cookies_enabled'] ? true : false)));;
      die();
    }


  }

  /* Add Logs */
  public function addLoginLog($user_id, $user_username, $user_email, $user_hashed_password, $is_admin, $post_obj, $session_obj, $valid_login, $notes, $blocked=NULL, $block_end=NULL, $banned=NULL, $completed=1, $remember_me=0, $rmme_token=NULL){
    $rmme_token = !is_null($rmme_token) ? $rmme_token : $remember_me_token;
    $browser = isset($session_obj['browser']) && !empty($session_obj['browser']) ? test_input($session_obj['browser']) : '';
    $os = isset($session_obj['os']) && !empty($session_obj['os']) ? test_input($session_obj['os']) : '';
    $cookies_enabled = isset($session_obj['cookies_enabled']) && !empty($session_obj['cookies_enabled']) && !is_null($session_obj['cookies_enabled']) ? test_input($session_obj['cookies_enabled']) : 0;
    $browser_language = isset($session_obj['browser_language']) && !empty($session_obj['browser_language']) ? test_input($session_obj['browser_language']) : '';
    $loc = isset($session_obj['loc']) && !empty($session_obj['loc']) ? test_input($session_obj['loc']) : '';
    $login_ip = isset($session_obj['login_ip']) && !empty($session_obj['login_ip']) ? test_input($session_obj['login_ip']) : '';

    $notes = empty($notes) && !empty($valid_login) && $valid_login != 0 ? 'valid login' : '';
    $notes = $notes == '' && !empty($banned) && $banned == 1 ? 'banned user' : '';
    $notes = $notes == '' && !empty($blocked) && $blocked == 1 ? 'blocked user' : '';
    $notes = empty($valid_login) || $valid_login == 0 ? 'invalid login' : $notes;

    $added_log = $this->logs_service->add(
      $user_id, $user_email, intval($valid_login),
      intval($is_admin), $this->get_cal_id(),
      $post_obj['request_token'], $session_obj['request_token'],
      $user_hashed_password, intval($cookies_enabled) , $login_ip, $loc, $os, $browser, $browser_language,
      $blocked, $block_end, $banned, $completed, $remember_me, $notes=$notes, $remember_me_token=$rmme_token
    );
    if (isset($added_log) && !empty($added_log)){
      return $added_log;
    } else {
      return false;
    }
  }


  public function banUserBadLogin($user_id, $log_id){
    $update1 = $this->logs_service->update_user_log('completed', 0, $user_id, $log_id);
    $udapte2 = $this->logs_service->update_user_log('banned', 1, $user_id, $log_id);
    return $update1 && $udapte2;
  }
  public function blockUserBadLogin($user_id, $log_id, $time_to_block=60){
    $block_end_at = $this->returnNowAfter($minutes_to_add=$time_to_block);
    $update1 = $this->logs_service->update_user_log('completed', 0, $user_id, $log_id);
    $udapte2 = $this->logs_service->update_user_log('blocked', 1, $user_id, $log_id);
    $udpate3 = $this->logs_service->update_user_log('block_end', $block_end_at, $user_id, $log_id);
    return $update1 && $udapte2 && $udpate3;
  }


  public function unblockUser($user_id, $log_id){
    if(!isset($this->logs_service)){
      $this->redirect_user_with_message($redirect_url, 'System Error Please Contact Admin', False);
      die();
    }
    $update1 = $this->logs_service->update_user_log('completed', 1, $user_id, $log_id);
    echo $update1;
    return $update1;
  }

  public function returnNowAfter($minutes_to_add=60){
    //$time->setTimezone(new DateTimeZone('America/Toronto')); set time zone in setup in config
    $time = new DateTime();
    $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
    $timestamp = $time->format("Y-m-d H:i:s");
    return $timestamp;
  }

  public function returnNowBefore($minutes_to_add=60){
    //$time->setTimezone(new DateTimeZone('America/Toronto')); set time zone in setup in config
    $time = new DateTime();
    $time->sub(new DateInterval('PT' . $minutes_to_add . 'M'));
    $timestamp = $time->format("Y-m-d H:i:s");
    return $timestamp;
  }


  /* ################## Login section ##################### */

  public function is_valid_credentials($user_obj, $username, $password, $type, $rememberme=False){
    $valid_password = False;
    if ($rememberme === True){
      // if remember me on and everything secure I need make everything smoot like upwork but without show pass in form so I just compare the passwords
      $valid_password = $password === $user_obj->get_hashed_password();
    } else {
      $valid_password = password_verify($password, $user_obj->get_hashed_password());
    }

    $selected_db_uname = $type == 'username' ? $user_obj->get_username() : $user_obj->get_email();
    $valid_user_check = $selected_db_uname == $username;
    if ($valid_password && $valid_user_check){
      return 1;
    } else {
      return 0;
    }
  }

  public function loguser($user_obj, $log_id){
    $time = new DateTime();
    $login_date = $time->format("Y-m-d H:i:s");
    $_SESSION['logged'] = True;
    $_SESSION['logged_id'] = $user_obj->get_id();
    $_SESSION['name'] = $user_obj->get_name();
    $_SESSION['log_id'] = $log_id;
    $_SESSION['login_message'] = 'Welcome Back: ' . $user_obj->get_name();
    $_SESSION['login_date'] = $login_date;
  }

  /* ################## Secuirty section ##################### */
  public function return_user_wrong_logins($username){
    return 0;
  }

  public function update_user_wrong_logins($username){
  }

  // prevent Brute-force attack and dicteinary attack
  public function block_user($user){
  }

  public function is_user_blocked($user){
  }

  /* secure from strong type which is send login request not from my website for example use bot or remote reuqest it wont success even if provide a key */
  public function getnerate_request_secert(){
    $token = bin2hex(openssl_random_pseudo_bytes(16));
    $token = isset($token) && !empty($token) ? $token : $token = bin2hex(random_bytes(16));
    return $token;
  }
  // secure remember me data important hack easy point



  public function encrypt_remeber_me_token($string_to_encrypt, $token){
    $encrypted_string=openssl_encrypt($string_to_encrypt,"AES-128-ECB",$token);
    return $encrypted_string;
  }

  public function decrypt_remeber_me_token($encrypted_string, $token){
    $decrypted_string=openssl_decrypt($encrypted_string,"AES-128-ECB",$token);
    return $decrypted_string;
  }

  public function generate_remeber_token(){
    $salt = bin2hex(openssl_random_pseudo_bytes(16));
    $token = bin2hex(openssl_random_pseudo_bytes(16));
    $token = isset($token) && !empty($token) ? $token : $token = bin2hex(random_bytes(16));
    $salt = isset($salt) && !empty($salt) ? $salt : $salt = bin2hex(random_bytes(16));
    $encrypted_string=openssl_encrypt($token,"AES-128-ECB",$salt);
    return $encrypted_string;
  }

  public function assign_used_calendar(){
    $cal = $this->calendar_service->get_used_calendar("used", 1);
    return $cal;
  }


  public function pick_selected_remember_meuid($uid){
    $result = array();
    // hard function to securly cost preformance for secure it read the cookies and get encrypted id that id need very hard token also this token has salt I do not even know it so it hard finaly it search all users who selected remember me in their last log
    // and get the token generatod from the log db and check per one if the encrypted is uid and for good encryption used return false if not true not value also the sql used very spacfic so it get the target data only and make the secuirty harder guess now less and preformance 2 also it break the loop for best pf
    if (isset($uid) && !empty($uid)){
      $encrypted_uid = test_input($uid);
      $possible_tokens = $this->logs_service->get_possible_tokens();

      for ($t=0; $t<count($possible_tokens); $t++){
        if (!$possible_tokens || empty($possible_tokens)){
          continue;
        }
        if (!isset($possible_tokens[$t]['uid']) || empty($possible_tokens[$t]['uid'])){
          continue;
        }
        if (empty($possible_tokens[$t]['uid']) || empty($possible_tokens[$t]['uid'])){
          continue;
        }
        $current_token = $possible_tokens[$t]['remember_me_token'];
        $current_userid = $possible_tokens[$t]['uid'];
        $check_id = $this->decrypt_remeber_me_token($encrypted_uid, $current_token);
        if ($check_id && $check_id == $current_userid){
          $result['uid'] = $current_userid;
          $result['id'] = $possible_tokens[$t]['id'];
          return $result;
          break;
        }
      }

    }
    return $result;
  }


  public function remember_me_handle($cookie_object){
    $data = array('username'=>NULL, 'password'=>NULL, 'remember_me'=>False, 'logid'=>0);
    if (isset($cookie_object['uid']) && !empty($cookie_object['uid'])){
      $encrypted_uid = test_input($cookie_object['uid']);
      $selected_log = $this->pick_selected_remember_meuid($encrypted_uid);
      if (empty($selected_log) || !isset($selected_log['uid']) || !isset($selected_log['id'])){
        return $data;
      }
      $uid = $selected_log['uid'];
      $logid = $selected_log['id'];
      if (isset($uid) && !empty($uid) && $uid != 0){
        $selected_user = $this->user_service->get_user_by_id($uid);
        if (isset($selected_user) && !empty($selected_user) && !is_null($selected_user)){
          $user_id = $selected_user->get_id();
          if (is_numeric($user_id) && $user_id > 0){
            $data['username'] = $selected_user->get_username();
            $data['password'] = $selected_user->get_hashed_password();
            $data['remember_me'] = True;
            $data['user_id'] = $uid;
            $data['logid'] = $logid;

          }
        }
      }
    }
    return $data;
  }


}

?>
