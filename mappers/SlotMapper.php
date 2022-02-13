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
      $statement = $pdo->prepare('INSERT INTO slot(start_from, end_at, period_id, element_id, element_class, empty, slot_index) VALUES(:start_from, :end_at, :period_id, :element_id, :element_class, :empty, :slot_index)');
      $statement->execute(array(
        'start_from' => $slot->get_start_from(),
        'end_at' => $slot->get_end_at(),
        'period_id' => $slot->get_period_id(),
        'empty' => $slot->get_empty(),
        'element_id' => $slot->get_element_id(),
        'element_class' => $slot->get_element_class(),
        'slot_index' => $slot->get_slot_index()
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
    $statement = $pdo->prepare('UPDATE slot (start_from, end_at, period_id, element_id, element_class, empty) VALUES(:start_from, :end_at, :period_id, :element_id, :element_class, :empty)');
    $statement->execute(array(
      'start_from' => $slot->get_start_from(),
      'end_at' => $slot->get_end_at(),
      'period_id' => $slot->get_period_id(),
      'element_id' => $slot->get_element_id(),
      'element_class' => $slot->get_element_class(),
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
    $sql = "UPDATE slot SET ".$column."=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }

  function get_total_slots(){
    $pdo = $this->getPDO();
    return $pdo->query('select count(id) from slot')->fetchColumn();
  }

  function get_slots_where($column, $value, $limit=''){
    $pdo = $this->getPDO();
    $limit  = $limit != '' ? ' LIMIT ' . $limit : '';
    $sql = "SELECT * FROM slot WHERE slot_index=".$value."".$limit;
    $stmt = $pdo->prepare($sql);
    $data = $stmt->execute();
    if ($data){
      return $stmt->fetchAll();
    } else {
      return array();
    }
  }

  function get_distinct_slots($cal_id){
    $pdo = $this->getPDO();

    $slots_sql = "SELECT DISTINCT slot.slot_index FROM slot join period ON slot.period_id = period.id JOIN
    day ON period.day_id = day.id JOIN month ON month.id=day.month_id
    JOIN year ON year.id=month.year_id JOIN calendar ON calendar.id=year.cal_id WHERE calendar.id=".$cal_id;
    $query_sql = test_input($slots_sql);
    $stmt = $pdo->prepare($query_sql);
    $stmt->execute();
    return  $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  // new update
  function insert_group_fast($data){
    $inserted_ids = array();
    $pdo = $this->getPDO();
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    $pdo->beginTransaction(); // also helps speed up your inserts.
    $stmt = $pdo->prepare('INSERT INTO slot(start_from, end_at, period_id, element_id, element_class, empty, slot_index) VALUES(:start_from, :end_at, :period_id, :element_id, :element_class, :empty, :slot_index)');
    foreach($data as $item)
    {

        $stmt->bindValue(':start_from', $item->get_start_from());
        $stmt->bindValue(':end_at', $item->get_end_at());
        $stmt->bindValue(':period_id', $item->get_period_id());
        $stmt->bindValue(':empty', $item->get_empty());
        $stmt->bindValue(':element_id', $item->get_element_id());
        $stmt->bindValue(':element_class', $item->get_element_class());
        $stmt->bindValue(':slot_index', $item->get_slot_index());
        $stmt->execute();
        $id = $pdo->lastInsertId();
        array_push($inserted_ids, $id);
    }
    $pdo->commit();
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    return $inserted_ids;
  }



}
