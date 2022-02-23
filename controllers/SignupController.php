<?php
ob_start();
require_once (dirname(__FILE__, 2) . '\config.php');
require_once (dirname(__FILE__, 2) . '\functions.php');
require_once (dirname(__FILE__, 2) . '\services\UserService.php');
require_once (dirname(__FILE__, 2) . '\models\User.php');

require_once (dirname(__FILE__, 2) . '\controllers\IndexController.php');

$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';


class SignupController {
  protected $pdo;
  protected $user_service;
  protected $index_controller;
  protected $request_type;
  protected $signup_success;
  private $request_token;





  public function __construct(PDO $pdo, $request_type='GET')
  {
    $this->pdo = $pdo;
    $this->user_service = new UserService($pdo);
    $this->index_controller = new IndexController($pdo);
    $this->set_request_type($request_type);
    $this->set_signup_success(false);
    $this->set_used_calendar($this->index_controller->get_used_calendar());

    $this->set_request_token($this->getnerate_request_secert());

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



  public function set_used_calendar($used_calendar){
    $this->used_calendar = $used_calendar;
  }

  public function get_used_calendar(){
    return $this->used_calendar;
  }

  public function set_signup_success($signup_success){
    $this->signup_success = $signup_success;
  }

  public function get_signup_success(){
    return $this->signup_success;
  }

  public function all_required_exist($username, $email, $password, $name){
    $empty_username = trim($username," ");
    $empty_email = trim($email," ");
    $empty_password = trim($password," ");
    $empty_name = trim($name," ");
    return !empty($empty_username) && !empty($empty_email) && !empty($empty_password) && !empty($empty_name);
  }

  public function is_user_exist($email, $username){
    $message = '';
    $exist = 0;
    $is_email_exist = $this->user_service->get_user_where('email', $email);
    $is_username_exist = $this->user_service->get_user_where('username', $username);


    if (!empty($is_email_exist)){
      $message .= empty($message) ? '' : ', ';
      $message .= 'Email';
      $exist = true;
    }
    if (!empty($is_username_exist)){
      $message .= empty($message) ? '' : ', ';
      $message .= 'username';
      $exist = true;
    }

    $message .= !empty($message) ? ' exist on the system' : 'Unique User Data';
    return array('exist'=>$exist, 'message'=>$message);
  }

  public function generate_user_obj($username, $password, $email, $name){
    return $this->user_service->convertDataToUser($username, $password, $email, $name);
  }

  public function add_new_user($user){
  }

  public function redirect_user_with_message($redirect_url, $message, $status){
    $_SESSION['message_signup'] = $message;
    $_SESSION['success_signup'] = $status;
    header("Location: " . $redirect_url);
  }

  public function secure_pass($pass, $username, $email){
    $message = 'Secure Password';
    $secure = true;

    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$/i";
    $secure = preg_match($pattern, $pass) ? true : false;
    $message = $secure ? $message : 'Invalid Password format, password must contains 8 characters and contains at least 1 number, 1 small letter, 1 capital letter, and symobol [!@#$%^*_=+-]) EG: 1aaqQ@dd';

    $secure = strtolower($pass) != strtolower($username) ? true : false;
    $message = $secure ? $message : 'The password cannot be the same as the username';

    $secure = strtolower($pass) != strtolower($email) ? true : false;
    $message = $secure ? $message : 'The password cannot be the same as the Email';

    return array('secure'=> $secure, 'message'=>$message);

  }

  ###################### Post Handler ############################
  public function postHandler($signup_controler, $post_obj, $session_obj, $redirect_url, $error){

     // Secure form from remote attack
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
    $ajax_request = false;
    if ($error){
      //return error 02;
      $this->redirect_user_with_message($redirect_url, 'a fatal error occurred in setup Code:002', false);
      die();
    }
    /* Signup Request Start */
    if (
        isset($_POST['uname_signup']) && isset($_POST['pwd_signup']) &&
        isset($_POST['email']) && isset($_POST['singup_name'])
    ) {
      $username = test_input($_POST['uname_signup']);
      $password = test_input($_POST['pwd_signup']);
      $email = test_input($_POST['email']);
      $name = test_input($_POST['singup_name']);

      // check if data empty
      if (
          empty($_POST['uname_signup']) || empty($_POST['pwd_signup']) &&
          empty($_POST['email']) || empty($_POST['singup_name'])
        ){
          // error 0'
          $this->redirect_user_with_message($redirect_url, 'Missing Required User Data', false);
          die();
        }

      // check if user submited empty strings
      $all_required_exist = $this->all_required_exist($username, $email, $password, $name);
      if (!$all_required_exist){
        // error 04
        $this->redirect_user_with_message($redirect_url, 'Missing or empty User Required Data', false);
        die();
      }

      // validate pass
      $secure_pass_check = $this->secure_pass($password, $username, $email);
      if (!isset($secure_pass_check['secure']) || !$secure_pass_check['secure']){
        //return error 03;
        $issecure = $secure_pass_check['secure'] ? true : false;
        $this->redirect_user_with_message($redirect_url, $secure_pass_check['message'], $issecure);
        die();
      }

      // check if user or email exist
      $user_exist_check = $this->is_user_exist($email, $username);
      if (!isset($user_exist_check['exist']) || $user_exist_check['exist']){
        //return error 04;
        $isexist = $user_exist_check['exist'] ? false : true;
        $this->redirect_user_with_message($redirect_url, $user_exist_check['message'], $isexist);
        die();
      }

      // create user data object and secure pass
      $hashed_password = password_hash($password, PASSWORD_DEFAULT, array(
          'cost' => 9
      ));

      $added_user_id = $this->user_service->add($name, $username, $hashed_password, $email, $role='user', $active=1);

      if (!$added_user_id){
        // error 05
        $this->redirect_user_with_message($redirect_url, 'User Could not created system error please contact support', false);
        die();
      } else {
        // redirect to login
        $this->redirect_user_with_message($redirect_url, 'You Signup Successfully Please Login', true);
        die();
      }
    }
    /* Signup Request end */
  }

  /* secure from strong type which is send login request not from my website for example use bot or remote reuqest it wont success even if provide a key */
  public function getnerate_request_secert(){
    $token = bin2hex(openssl_random_pseudo_bytes(16));
    $token = isset($token) && !empty($token) ? $token : $token = bin2hex(random_bytes(16));
    return $token;
  }

}

?>
