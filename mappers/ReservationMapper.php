<?php
class ReservationMapper {
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

  public function insert($reservation) {
      $pdo = $this->getPDO();



      $statement = $pdo->prepare('INSERT INTO reservation(name, notes, slot_id, user_id) VALUES(:name, :notes, :slot_id, :user_id)');
      $statement->execute(array(
          'name' => $reservation->get_name(),
          'notes' => $reservation->get_notes(),
          'slot_id' => $reservation->get_slot_id(),
          'user_id' => $reservation->get_user_id()
      ));
      return $pdo->lastInsertId();
  }


  function read_one($reservation_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM reservation WHERE id=:id");
    $stmt->bindParam(':id', $reservation_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();
    return $data;
  }



  function update($reservation){
    $statement = $pdo->prepare('UPDATE reservation (name, notes, slot_id, user_id) VALUES(:name, :notes, :slot_id, :user_id)');
    $statement->execute(array(
      'name' => $reservation->get_name(),
      'notes' => $reservation->get_notes(),
      'slot_id' => $reservation->get_slot_id(),
      'user_id' => $reservation->get_user_id()
    ));
  }

  function delete($reservation_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM reservation
            WHERE id = :id';
    // prepare the statement for execution
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $reservation_id, PDO::PARAM_INT);
    // execute the statement
    //echo $statement->rowCount();
    return $statement->execute();
  }

  function read_all(){
    //$stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit, :offset");
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM reservation");
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  function delete_all(){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('DELETE FROM reservation WHERE id > 0');
    return $statement->execute();
  }
}
