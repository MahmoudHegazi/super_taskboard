<?php
class MonthMapper {
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

    public function insert($month) {
        $pdo = $this->getPDO();

        $statement = $pdo->prepare('INSERT INTO month(month, year_id) VALUES(:month, :year_id)');
        $statement->execute(array(
            'month' => $month->get_month(),
            'year_id' => $month->get_year_id()
        ));
        return $pdo->lastInsertId();
    }


    function read_one($month_id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM month WHERE id=:id");
      $stmt->bindParam(':id', $month_id, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch();
      return $data;
    }

    function update($month){
      $statement = $pdo->prepare('UPDATE month (month, year_id) VALUES(:month, :year_id)');
      $statement->execute(array(
        'month' => $month->get_month(),
        'year_id' => $month->get_year_id()
      ));
    }

    function delete($month_id){
      // construct the delete statement
      $pdo = $this->getPDO();
      $sql = 'DELETE FROM month
              WHERE id = :id';
      // prepare the statement for execution
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $month_id, PDO::PARAM_INT);
      // execute the statement
      //echo $statement->rowCount();
      return $statement->execute();
    }

    function read_all(){
      //$stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit, :offset");
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM month");
      $stmt->execute();
      $data = $stmt->fetchAll();
      return $data;
    }

    function delete_all(){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('DELETE FROM month WHERE id > 0');
      return $statement->execute();
    }

    function update_column($column, $value, $id){
      $pdo = $this->getPDO();
      $sql = "UPDATE month ".$column."=? WHERE id=?";
      $stmt= $pdo->prepare($sql);
      return $stmt->execute([$value, $id]);
    }

    function get_total_months(){
      $pdo = $this->getPDO();
      return $pdo->query('select count(id) from month')->fetchColumn();
    }


    // new update
    function insert_group_fast($data){
      $inserted_ids = array();
      $pdo = $this->getPDO();
      $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
      $pdo->beginTransaction(); // also helps speed up your inserts.
      $stmt = $pdo->prepare('INSERT INTO month(month, year_id) VALUES(:month, :year_id)');
      foreach($data as $item)
      {
          $stmt->bindValue(':month', $item->get_month());
          $stmt->bindValue(':year_id', $item->get_year_id());
          $stmt->execute();
          $id = $pdo->lastInsertId();
          array_push($inserted_ids, $id);
      }
      $pdo->commit();
      $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
      return $inserted_ids;
    }

}
