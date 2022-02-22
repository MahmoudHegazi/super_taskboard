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
  protected $request_type;
  protected $signup_success;
  public function __construct(PDO $pdo, $request_type='GET')
  {
    $this->pdo = $pdo;
    $this->user_service = new UserService($pdo);
    $this->index_controller = new IndexController($pdo);
    $this->set_request_type($request_type);
    $this->set_signup_success(false);
    $this->set_used_calendar($this->index_controller->get_used_calendar());
  }

  public function set_request_type($request_type){
    $this->request_type = $request_type;
  }

  public function get_request_type(){
    return $this->request_type;
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

  public function is_user_exist($email, $username){
  }

  public function generate_user_obj($email, $pass, $name){
  }

  public function add_new_user($user){
  }

  public function redirect_user_with_message($redirect_url, $message, $status){
  }

}

?>
