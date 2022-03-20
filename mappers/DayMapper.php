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
    function read_one_by_force($day_id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT calendar.id FROM day JOIN month ON day.month_id = month.id JOIN year ON
      month.year_id = year.id JOIN calendar ON year.cal_id = calendar.id WHERE day.id=:day_id");
      $stmt->bindParam(':day_id', $day_id, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch();
      return $data;
    }

    function get_dayid_by_date($day_date, $cal_id){
      $pdo = $this->getPDO();
      $sql = "SELECT day.id FROM day JOIN month ON day.month_id = month.id JOIN year ON month.year_id = year.id WHERE year.cal_id=? AND day.day_date=?";
      $stmt= $pdo->prepare($sql);
      $stmt->execute([$cal_id, $day_date]);
      $data = $stmt->fetch();
      $data = isset($data) && !empty($data) && isset($data['id']) ? $data['id'] : false;
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


    function get_days_where($calid, $column, $value, $limit='', $and_column='', $and_val=''){
      $limit  = $limit != '' ? ' ORDER BY day.id LIMIT ' . $limit : ' ORDER BY day.id';
      $pdo = $this->getPDO();
      if ($and_column != '' && $and_val != ''){
        $mysql1 = "SELECT day.* FROM day JOIN month ON day.month_id = month.id JOIN year ON
        month.year_id = year.id JOIN calendar ON year.cal_id = calendar.id WHERE
        day.".$column."=:value AND ". $and_column ."=:and_val AND calendar.id=:calid".$limit;
        $stmt = $pdo->prepare($mysql1);
        $stmt->bindValue(':value', (int) $value, PDO::PARAM_INT);
        $stmt->bindValue(':and_val', (int) $calid, PDO::PARAM_INT);
        $stmt->bindValue(':calid', (int) $value, PDO::PARAM_INT);
        $data = $stmt->execute();
      } else {
        $mysql1 = "SELECT day.* FROM day JOIN month ON day.month_id = month.id JOIN year ON
        month.year_id = year.id JOIN calendar ON year.cal_id = calendar.id WHERE
        day.".$column."=:value AND calendar.id=:calid".$limit;
        $stmt = $pdo->prepare($mysql1);
        $stmt->bindValue(':value', (int) $value, PDO::PARAM_INT);
        $stmt->bindValue(':calid', (int) $calid, PDO::PARAM_INT);
        $data = $stmt->execute();
      }
      if ($data){
        return $stmt->fetchAll();
      } else {
        return array();
      }
    }

}
//SELECT calendar.id FROM period JOIN day ON day.id = period.day_id JOIN month on day.month_id = month.id JOIN year ON year.id = month.year_id JOIN calendar ON year.cal_id = calendar.id WHERE period.id=287202
