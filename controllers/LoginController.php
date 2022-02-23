<?php
ob_start();
require_once (dirname(__FILE__, 2) . '\config.php');
require_once (dirname(__FILE__, 2) . '\functions.php');
require_once (dirname(__FILE__, 2) . '\services\UserService.php');
require_once (dirname(__FILE__, 2) . '\models\User.php');

$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';


class LoginController {
  protected $pdo;
  protected $user_service;
  protected $request_type;
  protected $login_success;
  protected $wrong_logins;
  private $request_token;



  public function __construct(PDO $pdo, $request_type='GET')
  {
    $this->pdo = $pdo;
    $this->user_service = new UserService($pdo);
    $this->set_request_type($request_type);
    $this->set_login_success(false);
    //$this->set_wrong_logins($this->return_user_wrong_logins($username));
    if ($request_type == 'GET'){
      $this->set_request_token($this->getnerate_request_secert());
    }
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

  public function generate_user_obj($username, $pass){

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
  public function postHandler($signup_controler, $post_obj, $session_obj, $redirect_url, $error){

     // Secure Any POST request to the login controller from remote attack (Required Form only) when use AJAX better to get secuirty token with ajax first and store it with session then add it to your request else no AJAX will made this is JMVCPHP rules
     if (!isset($session_obj['request_token']) || !isset($post_obj['request_token']) || empty($post_obj['request_token']) || empty($session_obj['request_token']) || ($session_obj['request_token'] != $post_obj['request_token'])){
       //return error 00;
       $this->redirect_user_with_message($redirect_url, 'Your request is denied Noob.', false);
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
      $valid_login = $this->is_valid_credentials($user_obj, $username, $password, $type);

      if ($valid_login){
        // redirect cal
        $this->redirect_user_with_message($redirect_url, 'Successful Login', $valid_login);
        die();
      } else {
        $this->redirect_user_with_message($redirect_url, 'Invalid Username Or Password', $valid_login);
        die();
      }
      die();
    }

    $ajax_request = false;

  }

  /* ################## Login section ##################### */

  public function is_valid_credentials($user_obj, $username, $password, $type){
    $valid_password = password_verify($password, $user_obj->get_hashed_password());
    $selected_db_uname = $type == 'username' ? $user_obj->get_username() : $user_obj->get_email();
    $valid_user_check = $selected_db_uname == $username;
    if ($valid_password && $valid_user_check){
      return true;
    } else {
      return false;
    }
  }

  public function loguser($user){

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


}

?>
