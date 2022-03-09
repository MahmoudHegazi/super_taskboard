<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\mappers\LogsMapper.php');
require_once(dirname(__FILE__, 2) . '\models\Logs.php');

class LogsService {
  protected $pdo;
  protected $logs_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->logs_mapper = new LogsMapper($pdo);
  }
  // Add New user
  function add(
      $user_id, $user_email, $valid, $admin_user, $cal_id, $form_token, $class_token, $hash_password,
      $cookies_enabled=0, $ip=NULL, $loc=NULL, $os_type=NULL, $browser_type=NULL, $browser_language=NULL,
      $blocked=NULL, $block_end=NULL, $banned=0, $completed=1, $remember_me=0, $notes='', $remember_me_token=NULL
    ){
    if ($form_token !== $class_token){
      return false;
    }

    $log_obj = new Logs();
    $log_obj->init($user_id, $user_email, $valid, $admin_user, $cal_id, $form_token, $class_token, $hash_password, $cookies_enabled, $ip, $loc, $os_type, $browser_type, $browser_language, $blocked, $block_end, $banned, $completed, $remember_me, $notes, $remember_me_token);
    return $this->logs_mapper->insert($log_obj);
  }

  // Remove  log
  function remove($log_id){
    return $this->logs_mapper->delete($log_id);
  }

  // Get log Using it's id
  function get_log_by_id($log_id){

    $log_row = $this->logs_mapper->read_one($log_id);
    // if element not found
    if (!isset($log_row['id']) || empty($log_row['id'])){return array();}
    $log = new Logs();
    $log->init(
      $log_row['user_id'], $log_row['user_email'],
      $log_row['valid'], $log_row['admin_user'],
      $log_row['cal_id'],
      $log_row['form_token'], $log_row['class_token'],
      $log_row['hash_password'], $log_row['cookies_enabled'],
      $log_row['ip'], $log_row['loc'], $log_row['os_type'],
      $log_row['browser_type'], $log_row['browser_language'],
      $log_row['blocked'], $log_row['block_end'],
      $log_row['banned'], $log_row['completed'],
      $log_row['remember_me'], $log_row['notes'], $log_row['remember_me_token']
    );
    $log->set_id($log_row['id']);
    return $log;
  }


  function get_user_lastblock_log($user_id){

    $log_row = $this->logs_mapper->get_user_lastblock_log($user_id);
    // if element not found
    if (!isset($log_row) || empty($log_row) || !isset($log_row['id']) || empty($log_row['id'])){return array();}
    $log = new Logs();
    $log->init(
      $log_row['user_id'], $log_row['user_email'],
      $log_row['valid'], $log_row['admin_user'],
      $log_row['cal_id'],
      $log_row['form_token'], $log_row['class_token'],
      $log_row['hash_password'], $log_row['cookies_enabled'],
      $log_row['ip'], $log_row['loc'], $log_row['os_type'],
      $log_row['browser_type'], $log_row['browser_language'],
      $log_row['blocked'], $log_row['block_end'],
      $log_row['banned'], $log_row['completed'],
      $log_row['remember_me'], $log_row['notes'], $log_row['remember_me_token']
    );
    $log->set_id($log_row['id']);
    return $log;
  }


  function convertDataToLog($user_id, $user_email, $valid, $admin_user, $cal_id, $form_token, $class_token, $hash_password,
  $cookies_enabled=0, $ip=NULL, $loc=NULL, $os_type=NULL, $browser_type=NULL, $browser_language=NULL,
  $blocked=NULL, $block_end=NULL, $banned=0, $completed=1, $remember_me=0, $notes='', $remember_me_token=NULL){
    $log = new Logs();
    $log->init($user_id, $user_email, $valid, $admin_user, $cal_id, $form_token, $class_token, $hash_password, $cookies_enabled, $ip, $loc, $os_type, $browser_type, $browser_language, $blocked, $block_end, $banned, $completed, $remember_me, $notes, $remember_me_token);
    $log->set_id(null);
    return $log;
  }

  // get All user
  function get_all_users(){

    $log_list = array();
    $log_rows = $this->logs_mapper->read_all();
    if (count($log_rows) == 0){return array();}

    for ($i=0; $i<count($log_rows); $i++){
        $log = new Logs();
        $log->init(
          $log_rows[$i]['user_id'], $log_rows[$i]['user_email'],
          $log_rows[$i]['valid'], $log_rows[$i]['admin_user'],
          $log_rows[$i]['cal_id'],
          $log_rows[$i]['form_token'], $log_rows[$i]['class_token'],
          $log_rows[$i]['hash_password'], $log_rows[$i]['cookies_enabled'],
          $log_rows[$i]['ip'], $log_rows[$i]['loc'], $log_rows[$i]['os_type'],
          $log_rows[$i]['browser_type'], $log_rows[$i]['blocked'],
          $log_rows[$i]['browser_language'], $log_rows[$i]['blocked'],
          $log_rows[$i]['block_end'], $log_rows[$i]['banned'],
          $log_rows[$i]['completed'], $log_rows[$i]['remember_me'],
          $log_rows['notes'], $log_rows[$i]['remember_me_token']
        );
        $log->set_id($log_rows[$i]['id']);
        array_push($log_list, $log);
    }
    return $log_list;
  }



  function delete_logs($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  log
  function update_one_column($column, $value, $id){
    return $this->logs_mapper->update_column($column, $value, $id);
  }

  function update_user_log($column, $value, $user_id, $log_id){
    return $this->logs_mapper->update_user_log($column, $value, $user_id, $log_id);
  }



  function get_total_logs(){
    return $this->logs_mapper->get_total_logs();
  }

  // used for get log id by str value
  function get_log_where_str($column, $value){
    $data = $this->logs_mapper->get_log_where_str($column, $value);
    return $data;
  }

  // used for get log id by init value
  function get_log_where_int($column, $value){
    $data = $this->logs_mapper->get_log_where_int($column, $value);
    return $data;
  }

  // get all user logs within date time
  public function get_user_invalid_logs($log_time_before, $user_id){
    $data = $this->logs_mapper->get_user_invalid_logs($log_time_before, $user_id);
    if (isset($data['total']) && !empty($data['total'])){
      return intval($data['total']);
    } else {
      return 0;
    }
  }


  // used for get full log data by str value
  function get_logs_data_where_str($column, $value){
    $log_data = $this->logs_mapper->get_logs_data_where_str($column, $value);
    if (!isset($log_data) || empty($log_data)){return array();}
    $log = new Logs();
    $log->init(
      $log_data['user_id'], $log_data['user_email'],
      $log_data['valid'], $log_data['admin_user'],
      $log_data['cal_id'],
      $log_data['form_token'], $log_data['class_token'],
      $log_data['hash_password'], $log_data['cookies_enabled'],
      $log_data['ip'], $log_data['loc'], $log_data['os_type'],
      $log_data['browser_type'],
      $log_data['browser_language'], $log_data['blocked'],
      $log_data['block_end'], $log_data['banned'],
      $log_data['completed'], $log_data['remember_me'], $log_data['notes'], $log_data['remember_me_token']
    );
    $log->set_id($log_row['id']);
    return $log;
  }

  function get_logs_data_where_init($column, $value){
    $log_data = $this->logs_mapper->get_logs_data_where_init($column, $value);
    if (!isset($log_data) || empty($log_data)){return array();}
    $log = new Logs();
    $log->init(
      $log_data['user_id'], $log_data['user_email'],
      $log_data['valid'], $log_data['admin_user'],
      $log_data['cal_id'],
      $log_data['form_token'], $log_data['class_token'],
      $log_data['hash_password'], $log_data['cookies_enabled'],
      $log_data['ip'], $log_data['loc'], $log_data['os_type'],
      $log_data['browser_type'],
      $log_data['browser_language'], $log_data['blocked'],
      $log_data['block_end'], $log_data['banned'],
      $log_data['completed'], $log_data['remember_me'],
      $log_data['notes'], $log_data['remember_me_token']
    );
    $log->set_id($log_row['id']);
    return $log;
  }
  /* UX */

  function get_possible_tokens(){
    return $this->logs_mapper->get_possible_tokens();
  }


  function get_last_user_log($user_id){
    return $this->logs_mapper->get_last_user_log($user_id);
  }

  function is_cookies_enabled($user_id){
    $cookies_enabled_obj = $this->logs_mapper->get_user_cookes_enabled($user_id);
    if (isset($cookies_enabled_obj['cookies_enabled']) && !empty($cookies_enabled_obj['cookies_enabled'])){
      return $cookies_enabled_obj['cookies_enabled'];
    } else {
      return False;
    }
  }



  /* Secuirty Alerts */
  function get_user_banned_status($user_id){
    $banedObj = $this->logs_mapper->get_user_banned_status($user_id);
    if (isset($banedObj['banned']) && !empty($banedObj['banned'])){
      return $banedObj['banned'] == True ? True : False;
    } else{
      return False;
    }


  }

  function get_user_blocked_status($user_id){
    return $this->logs_mapper->get_user_blocked_status($user_id);
  }

  /* Advanced */

  function get_user_login_hack_alerts($user_id){
    return $this->logs_mapper->get_user_login_hack_alerts($user_id);
  }



}
