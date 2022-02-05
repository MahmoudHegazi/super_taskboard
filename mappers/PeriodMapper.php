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
      $statement = $pdo->prepare('INSERT INTO period(day_id, period_date, description, period_index) VALUES(:day_id, :period_date, :description, :period_index)');

      $period_date = date('Y-m-d H:i:s', strtotime(test_input($period->get_period_date())));
      $statement->execute(array(
          'day_id' => $period->get_day_id(),
          'period_date' => $period_date,
          'description' => $period->get_description(),
          'period_index' => $period->get_period_index()
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

  function update_column($column, $value, $id){
    $pdo = $this->getPDO();
    $sql = "UPDATE period SET ".$column."=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }

  function update_column_where($column, $where, $value, $id){
    $pdo = $this->getPDO();
    $sql = "UPDATE period SET ".$column."=? WHERE ".$where."=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }

  function get_total_periods(){
    $pdo = $this->getPDO();
    return $pdo->query('select count(id) from period')->fetchColumn();
  }


  function get_periods_where($column, $value, $limit=''){
    $pdo = $this->getPDO();
    $limit  = $limit != '' ? ' LIMIT ' . $limit : '';
    $sql = "SELECT * FROM period WHERE period_index=".$value."".$limit;
    $stmt = $pdo->prepare($sql);
    $data = $stmt->execute();
    if ($data){
      return $stmt->fetchAll();
    } else {
      return array();
    }
  }


  function get_distinct_periods($cal_id){
    $pdo = $this->getPDO();

    $slots_sql = "SELECT DISTINCT period.period_index FROM period join
    day ON period.day_id = day.id JOIN month ON month.id=day.month_id
    JOIN year ON year.id=month.year_id JOIN calendar ON calendar.id=year.cal_id WHERE calendar.id=".$cal_id;
    $query_sql = test_input($slots_sql);
    $stmt = $pdo->prepare($query_sql);
    $stmt->execute();
    return  $stmt->fetchAll(PDO::FETCH_ASSOC);
  }



}
