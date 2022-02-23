<?php
class UserMapper {
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

  public function insert($user) {
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('INSERT INTO user(name, username, hashed_password, email, role, active) VALUES(:name, :username, :hashed_password, :email, :role, :active)');
      $statement->execute(array(
        'name' => $user->get_name(),
        'username' => $user->get_username(),
        'hashed_password' => $user->get_hashed_password(),
        'email' => $user->get_email(),
        'role' => $user->get_role(),
        'active' => $user->get_active()
      ));
      return $pdo->lastInsertId();
  }


  function read_one($user_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM user WHERE id=:id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();
    return $data;
  }




  function update($user){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('UPDATE user (name, username, hashed_password, email, role, active) VALUES(:name, :username, :hashed_password, :email, :role, :active)');
    $statement->execute(array(
      'name' => $user->get_name(),
      'username' => $user->get_username(),
      'hashed_password' => $user->get_hashed_password(),
      'email' => $user->get_email(),
      'role' => $user->get_role(),
      'active' => $user->get_active()
    ));
  }

  function delete($user_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM user
            WHERE id = :id';
    // prepare the statement for execution
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $user_id, PDO::PARAM_INT);
    // execute the statement
    //echo $statement->rowCount();
    return $statement->execute();
  }

  function read_all(){
    //$stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit, :offset");
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM user");
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  function delete_all(){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('DELETE FROM user WHERE id > 0');
    return $statement->execute();
  }


  function update_column($column, $value, $id){
    $pdo = $this->getPDO();
    $sql = "UPDATE user SET ".$column."=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }

  function get_total_users(){
    $pdo = $this->getPDO();
    return $pdo->query('select count(id) from user')->fetchColumn();
  }

  function get_user_where($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM user WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

  function get_user_data_where($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM user WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

}
