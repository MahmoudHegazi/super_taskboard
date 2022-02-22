<?php
ob_start();
require_once (dirname(__FILE__, 2) . '\config.php');
require_once (dirname(__FILE__, 2) . '\functions.php');
require_once (dirname(__FILE__, 2) . '\services\UserService.php');
require_once (dirname(__FILE__, 2) . '\models\User.php');

$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';


class SignupController {
  protected $pdo;
  protected $user_service;
  protected $request_type;
  protected $login_success;
  protected $wrong_logins;

  public function __construct(PDO $pdo, $request_type='GET')
  {
    $this->pdo = $pdo;
    $this->user_service = new UserService($pdo);
    $this->set_request_type($request_type);
    $this->set_login_success(false);
    $this->set_wrong_logins($this->return_user_wrong_logins($username));
  }

  public function set_request_type($request_type){
    $this->request_type = $request_type;
  }

  public function get_request_type(){
    return $this->request_type;
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
  public function is_user_exist($username){
  }

  public function generate_user_obj($username, $pass){

  }

  public function return_user($username){
    return array();
  }

  /* ################## Login section ##################### */

  public function is_valid_credentials($exist_username, $username, $password){
  }

  public function loguser($user){

  }

  public function redirect_user_with_message($redirect_url, $message, $status){

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


}

?>
