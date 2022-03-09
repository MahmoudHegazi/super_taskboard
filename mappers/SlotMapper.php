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

  function get_slots_by_period($period_id){
    $pdo = $this->getPDO();
    $sql = "SELECT * FROM slot WHERE period_id=?";
    $stmt = $pdo->prepare($sql);
    $data = $stmt->execute([$period_id]);
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

  function free_group_query($sql){
    $pdo = $this->getPDO();
    $query_sql = test_input($sql);
    $stmt = $pdo->prepare($query_sql);
    $stmt->execute();
    return  $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function free_single_query($sql){
    $pdo = $this->getPDO();
    $query_sql = test_input($sql);
    $stmt = $pdo->prepare($query_sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  function getLastSlotId($cal_id){
    $last_slot_id_sql = "SELECT MAX(slot.id) AS last FROM slot JOIN period ON period.id = slot.period_id JOIN day on period.day_id = day.id JOIN month ON day.month_id = month.id JOIN year ON month.year_id = year.id JOIN
    calendar ON year.cal_id = calendar.id WHERE calendar.id = ".$cal_id." LIMIT 1";

    $last_slot = $this->free_single_query($last_slot_id_sql);

    if (isset($last_slot) && !empty($last_slot) && isset($last_slot['last']) && !empty($last_slot['last']) && is_numeric($last_slot['last'])){
      $slot_id = $last_slot['last'];
      $last_slot_data = "SELECT element_id FROM `slot` WHERE id=".test_input($slot_id);
      $last_element_id = $this->free_single_query($last_slot_data);

      if (isset($last_element_id) && !empty($last_element_id) && isset($last_element_id['element_id']) && !empty($last_element_id['element_id'])){
        $last_elmid = trim($last_element_id['element_id'],"");

        $last_elmid_list = explode("_",$last_elmid);
        if (count($last_elmid_list) > 0){
          $lastid = $last_elmid_list[count($last_elmid_list)-1];
          $last_found = 0;
          $found_number = array();
          if (is_numeric($lastid)){
            return (intval($lastid) + 1);
          } else {
            for ($check=0; $check<count($last_elmid_list); $check++){
              if (is_numeric($last_elmid_list[$check])){
                array_push($found_number, intval($last_elmid_list[$check]));
              }
            }
            if (count($found_number) > 0){
              $last_idd = max(array($found_number));
              return intval($last_idd) + 1;
            } else {
              return 1;
            }
          }
        } else {
          return 1;
        }
      } else {
        return 1;
      }
    } else {
      return 1;
    }
    return 1;
  }



}
