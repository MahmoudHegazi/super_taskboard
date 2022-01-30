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
      $statement = $pdo->prepare('INSERT INTO user(name, username, hashed_password, email) VALUES(:name, :username, :hashed_password, :email)');
      $statement->execute(array(
        'name' => $user->get_name(),
        'username' => $user->get_username(),
        'hashed_password' => $user->get_hashed_password(),
        'email' => $user->get_email()
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
    $statement = $pdo->prepare('UPDATE user (name, username, hashed_password, email) VALUES(:name, :username, :hashed_password, :email)');
    $statement->execute(array(
      'name' => $user->get_name(),
      'username' => $user->get_username(),
      'hashed_password' => $user->get_hashed_password(),
      'email' => $user->get_email()
    ));
  }

  function delete($user_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM slot
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
}
