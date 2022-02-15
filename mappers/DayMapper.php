<?php
class DayMapper {
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

    public function insert($day) {
        $pdo = $this->getPDO();

        $statement = $pdo->prepare('INSERT INTO day(day, day_name, day_date, month_id) VALUES(:day, :day_name, :day_date, :month_id)');
        $statement->execute(array(
            'day' => $day->get_day(),
            'day_name' => $day->get_day_name(),
            'day_date' => $day->get_day_date(),
            'month_id' => $day->get_month_id()
        ));
        return $pdo->lastInsertId();
    }


    function read_one($day_id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM day WHERE id=:id");
      $stmt->bindParam(':id', $day_id, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch();
      return $data;
    }

    function update($day){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('UPDATE day (day, day_name, day_date, month_id) VALUES(:day, :day_name, :day_date, :month_id)');
      $statement->execute(array(
        'day' => $day->get_day(),
        'day_name' => $day->get_day_name(),
        'day_date' => $day->get_day_date(),
        'month_id' => $day->get_month_id()
      ));
    }

    function delete($day_id){
      // construct the delete statement
      $pdo = $this->getPDO();
      $sql = 'DELETE FROM day
              WHERE id = :id';
      // prepare the statement for execution
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $day_id, PDO::PARAM_INT);
      // execute the statement
      //echo $statement->rowCount();
      return $statement->execute();
    }

    function read_all(){
      //$stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit, :offset");
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM day");
      $stmt->execute();
      $data = $stmt->fetchAll();
      return $data;
    }

    function delete_all(){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('DELETE FROM day WHERE id > 0');
      return $statement->execute();
    }

    function update_column($column, $value, $id){
      $pdo = $this->getPDO();
      $sql = "UPDATE day ".$column."=? WHERE id=?";
      $stmt= $pdo->prepare($sql);
      return $stmt->execute([$value, $id]);
    }

    function get_total_days(){
      $pdo = $this->getPDO();
      return $pdo->query('select count(id) from day')->fetchColumn();
    }

    // new update
    function insert_group_fast($data){
      $inserted_ids = array();
      $pdo = $this->getPDO();
      $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
      $pdo->beginTransaction(); // also helps speed up your inserts.
      $stmt = $pdo->prepare('INSERT INTO day(day, day_name, day_date, month_id) VALUES(:day, :day_name, :day_date, :month_id)');
      foreach($data as $item)
      {
          $stmt->bindValue(':day', $item->get_day());
          $stmt->bindValue(':day_name', $item->get_day_name());
          $stmt->bindValue(':day_date', $item->get_day_date());
          $stmt->bindValue(':month_id', $item->get_month_id());
          $stmt->execute();
          $id = $pdo->lastInsertId();
          array_push($inserted_ids, $id);
      }
      $pdo->commit();
      $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
      return $inserted_ids;
    }
}
