<?php
class SlotMapper {
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

  public function insert($slot) {
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('INSERT INTO slot(start_from, end_at, period_id, empty) VALUES(:start_from, :end_at, :period_id, :empty)');
      $statement->execute(array(
        'start_from' => $slot->get_start_from(),
        'end_at' => $slot->get_end_at(),
        'period_id' => $slot->get_period_id(),
        'empty' => $slot->get_empty()
      ));
      return $pdo->lastInsertId();
  }


  function read_one($slot_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM slot WHERE id=:id");
    $stmt->bindParam(':id', $slot_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();
    return $data;
  }




  function update($slot){
    $statement = $pdo->prepare('UPDATE slot (start_from, end_at, period_id, empty) VALUES(:start_from, :end_at, :period_id, :empty)');
    $statement->execute(array(
      'start_from' => $slot->get_start_from(),
      'end_at' => $slot->get_end_at(),
      'period_id' => $slot->get_period_id(),
      'empty' => $slot->get_empty()
    ));
  }

  function delete($slot_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM slot
            WHERE id = :id';
    // prepare the statement for execution
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $slot_id, PDO::PARAM_INT);
    // execute the statement
    //echo $statement->rowCount();
    return $statement->execute();
  }

  function read_all(){
    //$stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit, :offset");
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM slot");
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  function delete_all(){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('DELETE FROM slot WHERE id > 0');
    return $statement->execute();
  }

  function update_column($column, $value, $id){
    $pdo = $this->getPDO();
    $sql = "UPDATE slot ".$column."=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }

  function get_total_slots(){
    $pdo = $this->getPDO();
    return $pdo->query('select count(id) from slot')->fetchColumn();
  }
}
