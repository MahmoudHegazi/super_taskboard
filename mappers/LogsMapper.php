<?php
class LogsMapper {
  /* Main CRUD */
  /**
    * @var PDO
  */

  // get_id get_day get_day_name get_day_date get_month_id get_cal_id
  protected $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function getPDO(){
    return $this->pdo;
  }

  public function insert($log) {
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('INSERT INTO logs(user_id,	user_email,	valid,	cookies_enabled, admin_user, hash_password, ip, loc, os_type, browser_type, browser_language, blocked, block_end, banned, remember_me, cal_id, form_token, notes, completed, class_token, remember_me_token) VALUES (:user_id,	:user_email,	:valid,	:cookies_enabled, :admin_user, :hash_password, :ip, :loc, :os_type, :browser_type, :browser_language, :blocked, :block_end, :banned, :remember_me, :cal_id, :form_token, :notes, :completed, :class_token, :remember_me_token)');
      $statement->execute(array(
        'user_id' => $log->get_user_id(),
        'user_email' => $log->get_user_email(),
        'valid' => $log->get_valid(),
        'cookies_enabled' => $log->get_cookies_enabled(),
        'admin_user' => $log->get_admin_user(),
        'hash_password' => $log->get_hash_password(),
        'ip' => $log->get_ip(),
        'loc' => $log->get_loc(),
        'os_type' => $log->get_os_type(),
        'browser_type' => $log->get_browser_type(),
        'browser_language' => $log->get_browser_language(),
        'blocked' => $log->get_blocked(),
        'block_end' => $log->get_block_end(),
        'banned' => $log->get_banned(),
        'remember_me' => $log->get_remember_me(),
        'cal_id' => $log->get_cal_id(),
        'form_token' => $log->get_form_token(),
        'notes' => $log->get_notes(),
        'completed' => $log->get_completed(),
        'class_token' => $log->get_class_token(),
        'remember_me_token'=> $log->get_remember_me_token()
      ));
      return $pdo->lastInsertId();
  }


  function read_one($log_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM logs WHERE id=:id");
    $stmt->bindParam(':id', $log_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();
    return $data;
  }




  function update($user){
    $pdo = $this->getPDO();
      $statement = $pdo->prepare('UPDATE logs (user_id,	user_email,	valid,	cookies_enabled, admin_user, hash_password, ip, loc, os_type, browser_type, browser_language, blocked, block_end, banned, remember_me, cal_id, form_token, notes, completed, class_token, remember_me_token) VALUES (:user_id,	:user_email,	:valid,	:cookies_enabled, :admin_user, :hash_password, :ip, :loc, :os_type, :browser_type, :browser_language, :blocked, :block_end, :banned, :remember_me, :cal_id, :form_token, :notes, :completed, :class_token, :remember_me_token)');
    $statement->execute(array(
      'user_id' => $log->get_user_id(),
      'user_email' => $log->get_user_email(),
      'valid' => $log->get_valid(),
      'cookies_enabled' => $log->get_cookies_enabled(),
      'admin_user' => $log->get_admin_user(),
      'hash_password' => $log->get_hash_password(),
      'ip' => $log->get_ip(),
      'loc' => $log->get_loc(),
      'os_type' => $log->get_os_type(),
      'browser_type' => $log->get_browser_type(),
      'browser_language' => $log->get_browser_language(),
      'blocked' => $log->get_blocked(),
      'block_end' => $log->get_block_end(),
      'banned' => $log->get_banned(),
      'remember_me' => $log->get_remember_me(),
      'cal_id' => $log->get_cal_id(),
      'form_token' => $log->get_form_token(),
      'notes' => $log->get_notes(),
      'completed' => $log->get_completed(),
      'class_token' => $log->get_class_token(),
      'remember_me_token'=> $log->get_remember_me_token()
    ));
  }

  function delete($log_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM logs
            WHERE id = :id';
    // prepare the statement for execution
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $log_id, PDO::PARAM_INT);
    // execute the statement
    //echo $statement->rowCount();
    return $statement->execute();
  }

  function read_all(){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM logs");
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  function delete_all(){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('DELETE FROM logs WHERE id > 0');
    return $statement->execute();
  }
  // clear not used chache and logs
  function delete_logs_before($selected_date){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare("DELETE FROM logs WHERE log_time > '" .$selected_date. "' AND completed=1");
    return $statement->execute();
  }


  function update_column($column, $value, $id){
    $pdo = $this->getPDO();
    $sql = "UPDATE logs SET ".$column."=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }

  function update_user_log($column, $value, $user_id, $log_id){
    $pdo = $this->getPDO();
    $sql = "UPDATE logs SET ".$column."=? WHERE user_id=? AND id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $user_id, $log_id]);
  }

  function get_total_logs(){
    $pdo = $this->getPDO();
    return $pdo->query('select count(id) from logs')->fetchColumn();
  }

  function get_log_where_str($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM logs WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

  function get_log_where_int($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM logs WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }

  function get_logs_data_where_str($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM logs WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

  function get_logs_data_where_init($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM logs WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }


  function get_user_invalid_logs($log_time, $user_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT COUNT(id) AS total FROM logs WHERE valid = 0 AND log_time > :log_time AND user_id = :user_id';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':log_time', $log_time, PDO::PARAM_STR);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }

  function get_user_lastblock_log($user_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM logs WHERE blocked = 1 AND block_end != NULL AND user_id = :user_id';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }
  /*
  function get_logs_data_wheres(...){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM logs WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }
  */

  // secure tokens with last secure implnetent (this get last logs of the users )
  function get_possible_tokens(){
    $pdo = $this->getPDO();
    // join user with logs and create new table then join logs on that table it main on left and will keep search for id or date not have biger than it until it reach the last one and it true so it added with NULL from right stackoverflow
    $sql = 'SELECT c.id AS uid, p1.* FROM user c JOIN logs p1 ON (c.id = p1.user_id) LEFT OUTER JOIN logs p2 ON (c.id = p2.user_id AND (p1.log_time < p2.log_time OR (p1.log_time = p2.log_time AND p1.id < p2.id))) WHERE p2.id IS NULL AND
    p1.remember_me = 1 AND p1.cookies_enabled = 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchAll();
  }

  function get_last_user_log($user_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT MAX(id) FROM logs WHERE user_id = 40 LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

  function get_user_blocked_status($user_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT id, blocked, block_end  FROM logs WHERE completed = 0 AND user_id = :user_id LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }


  function get_user_banned_status($user_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT banned  FROM logs WHERE completed = 0 AND user_id = :user_id LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }
  /* Secuirty Alerts */

  function get_user_login_hack_alerts($user_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT *, COUNT(id) AS total FROM logs WHERE form_token != class_token AND user_id = :user_id GROUP BY id';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }

  function get_user_cookes_enabled($user_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT cookies_enabled FROM logs WHERE user_id = :user_id LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }



}
