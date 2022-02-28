<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\mappers\UserMapper.php');
require_once(dirname(__FILE__, 2) . '\models\User.php');

class UserService {
  protected $pdo;
  protected $user_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->user_mapper = new UserMapper($pdo);
  }
  // Add New user
  function add($name, $username, $hashed_password, $email, $role='user', $active=1){
    $role = $role == 'admin' ? 'admin' : 'user';
    $active = $active == 1 ? 1 : 0;
    $user_obj = new User();
    $user_obj->init($name, $username, $hashed_password, $email);
    $user_obj->set_role($role);
    $user_obj->set_active($active);
    return $this->user_mapper->insert($user_obj);
  }

  public function secure_pass_boolean($pass, $username, $email){
    $secure = true;
    $passs_checke = str_replace(" ", "", $pass);
    $secure = !empty($passs_checke) ? true : false;
    $unique_check = count(array_unique(str_split($pass))) >= 3;
    $secure = $unique_check  ? true : false;
    $secure = strtolower($pass) != strtolower($username) ? true : false;
    $secure = strtolower($pass) != strtolower($email) ? true : false;
    return $secure;
  }

  public function secure_pass_array($pass, $username, $email){
    $message = 'Secure Password';
    $secure = true;
    $passs_checke = str_replace(" ", "", $pass);
    $secure = !empty($passs_checke) ? true : false;
    $message = $secure ? $message : 'Password Can not be empty or spaces';

    $unique_check = count(array_unique(str_split($pass))) >= 3;
    $secure = $unique_check  ? true : false;
    $message = $secure ? $message : 'Password Must contains at least 3 unqiue characters';

    $secure = strtolower($pass) != strtolower($username) ? true : false;
    $message = $secure ? $message : 'The password cannot be the same as the username';

    $secure = strtolower($pass) != strtolower($email) ? true : false;
    $message = $secure ? $message : 'The password cannot be the same as the Email';

    return array('secure'=> $secure, 'message'=>$message);

  }



  // Remove  user
  function remove($user_id){
    return $this->user_mapper->delete($user_id);
  }

  // Get user Using it's id
  function get_user_by_id($user_id){

    $user_row = $this->user_mapper->read_one($user_id);
    // if element not found
    if (!isset($user_row['id']) || empty($user_row['id'])){return array();}
    $user = new User();
    $user->init(
      $user_row['name'],
      $user_row['username'],
      $user_row['hashed_password'],
      $user_row['email']
    );

    $user->set_id($user_row['id']);
    $user->set_role($user_row['role']);
    $user->set_active($user_row['active']);
    return $user;
  }

  function convertDataToUser($username, $password, $email, $name){
    $user = new User();
    $user->init($username, $password, $email, $name);
    $user->set_id(null);
    return $user;
  }

  // get All user
  function get_all_users(){

    $user_list = array();
    $user_rows = $this->user_mapper->read_all();
    if (count($user_rows) == 0){return array();}

    for ($i=0; $i<count($user_rows); $i++){
        $user = new User();
        $user->init(
          $user_rows[$i]['name'],
          $user_rows[$i]['username'],
          $user_rows[$i]['hashed_password'],
          $user_rows[$i]['email']
        );
        $user->set_id($user_rows[$i]['id']);
        $user->set_role($user_rows[$i]['role']);
        $user->set_active($user_rows[$i]['active']);
        array_push($user_list, $user);
    }
    return $user_list;
  }

  // services methods
  function get_users_by_id($list_of_ids){

    $user_rows = $this->get_all_users();


    $result = array();
    for ($i=0; $i<count($user_rows); $i++){
      if (in_array($user_rows[$i]->id, $list_of_ids)){
        array_push($result, $user_rows[$i]);
      }
    }
    return $result;

  }


  // add more than one db row
  function add_users($user_data_list){
    $users_ids = array();

    if (count($user_data_list) < 1){
      return False;
    }

    $user = new User();

    for ($i=0; $i<count($user_data_list); $i++){
      if (is_array($user_data_list[$i]) && count($user_data_list[$i]) == 6){

         $user->init(
           $user_data_list[$i][0],
           $user_data_list[$i][1],
           $user_data_list[$i][2],
           $user_data_list[$i][3]
         );
         $user->set_role($user_data_list[$i][4]);
         $user->set_active($user_data_list[$i][5]);
         $user_id = $this->user_mapper->insert($user);
         array($users_ids, $user_id);
      }
    }
    return $users_ids;
  }

  function delete_users($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  user
  function update_one_column($column, $value, $id){
    return $this->user_mapper->update_column($column, $value, $id);
  }

  function get_total_users(){
    return $this->user_mapper->get_total_users();
  }

  // used for get user by email or username
  function get_user_where($column, $value){
    $data = $this->user_mapper->get_user_where($column, $value);
    return $data;
  }

  // used for get user by email or username
  function get_user_data_where($column, $value){
    $user_data = $this->user_mapper->get_user_data_where($column, $value);
    if (!isset($user_data) || empty($user_data)){return array();}
    $user = new User();
    $user->init(
      $user_data['name'],
      $user_data['username'],
      $user_data['hashed_password'],
      $user_data['email']
    );
    $user->set_id($user_data['id']);
    $user->set_role($user_data['role']);
    $user->set_active($user_data['active']);
    return $user;
  }


}


/* ##################### Test #################### */
/* #####################

global $pdo;
$userService = new UserService($pdo);

echo "<pre>";
$userService->remove(1);

print_r($userService->get_user_by_id(2));
echo "<h1>get_user_by_id</h1><br /><br /><hr />";
echo count($userService->get_all_users());
echo "<h1>get_all_users</h1><br /><br /><hr />";

print_r($userService->get_users_by_id(array(4,17,10)));
echo "<h1>get_users_by_id</h1><br /><br /><hr />";

$userService->delete_users(array(1,6,10));
echo "<h1>delete_users</h1><br /><br /><hr />";

echo "</pre>";
*/
