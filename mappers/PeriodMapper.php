<?php
class PeriodMapper {
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

  public function insert($period) {
      $pdo = $this->getPDO();


      $statement = $pdo->prepare('INSERT INTO period(day_id, period_date, description) VALUES(:day_id, :period_date, :description)');
      $statement->execute(array(
          'day_id' => $period->get_day_id(),
          'period_date' => $period->get_period_date(),
          'description' => $period->get_description()
      ));
      return $pdo->lastInsertId();
  }


  function read_one($period_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM period WHERE id=:id");
    $stmt->bindParam(':id', $period_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();
    return $data;
  }



  function update($period){
    $statement = $pdo->prepare('UPDATE period (day_id, period_date, description) VALUES(:day_id, :period_date, :description)');
    $statement->execute(array(
      'day_id' => $period->get_day_id(),
      'period_date' => $period->get_period_date(),
      'description' => $period->get_description()
    ));
  }

  function delete($period_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM period
            WHERE id = :id';
    // prepare the statement for execution
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $period_id, PDO::PARAM_INT);
    // execute the statement
    //echo $statement->rowCount();
    return $statement->execute();
  }

  function read_all(){
    //$stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit, :offset");
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM period");
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  function delete_all(){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('DELETE FROM period WHERE id > 0');
    return $statement->execute();
  }

}
