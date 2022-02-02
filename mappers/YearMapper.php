<?php
class YearMapper {
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


  public function insert($year) {
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('INSERT INTO year(year, cal_id) VALUES(:year, :cal_id)');
      $statement->execute(array(
        'year' => $year->get_year(),
        'cal_id' => $year->get_cal_id()
      ));
      return $pdo->lastInsertId();
  }


  function read_one($year_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM year WHERE id=:id");
    $stmt->bindParam(':id', $year_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();
    return $data;
  }




  function update($year){
    $statement = $pdo->prepare('UPDATE year (year, cal_id) VALUES(:year, :cal_id)');
    $statement->execute(array(
      'year' => $year->get_year(),
      'cal_id' => $year->get_cal_id()
    ));
  }

  function delete($year_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM slot
            WHERE id = :id';
    // prepare the statement for execution
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $year_id, PDO::PARAM_INT);
    // execute the statement
    //echo $statement->rowCount();
    return $statement->execute();
  }

  function read_all(){
    //$stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit, :offset");
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM year");
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  function delete_all(){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('DELETE FROM year WHERE id > 0');
    return $statement->execute();
  }

  function update_column($column, $value, $id){
    $pdo = $this->getPDO();
    $sql = "UPDATE year ".$column."=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }

  function get_total_years(){
    $pdo = $this->getPDO();
    return $pdo->query('select count(id) from year')->fetchColumn();
  }
}
