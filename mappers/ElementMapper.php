<?php
class ElementMapper {
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

    public function insert($element) {
        $pdo = $this->getPDO();
        if (empty($element)){return 0;}

        $statement = $pdo->prepare('INSERT INTO element (element_id, class_name, cal_id, type, default_bootstrap, default_style, data_group, bootstrap_classes, innerHTML, innerText, data) VALUES(:element_id, :class_name, :cal_id, :type, :default_bootstrap, :default_style, :data_group, :bootstrap_classes, :innerHTML, :innerText, :data)');
        //print_r($statement);
        //die();
        try{
          $statement->execute(array(
              'element_id' => $element->get_element_id(),
              'class_name' => $element->get_class_name(),
              'cal_id' => $element->get_cal_id(),
              'type'=> $element->get_type(),
              'default_bootstrap' => $element->get_default_bootstrap(),
              'default_style' => $element->get_default_style(),
              'data_group' => $element->get_data_group(),
              'bootstrap_classes' => $element->get_bootstrap_classes(),
              'innerHTML' => $element->get_innerHTML(),
              'innerText' => $element->get_innerText(),
              'data' => $element->get_data()

          ));
        } catch(Exception $e){
          return 0;
        }
        return $pdo->lastInsertId();
    }
    function read_one($id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM element WHERE id=:id");
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch();
      return $data;
    }

    function get_element($element_id, $type='container', $calid){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM element WHERE element_id=:element_id AND type=:type AND cal_id=:calid");
      $stmt->bindParam(':element_id', $element_id, PDO::PARAM_STR);
      $stmt->bindParam(':type', $type, PDO::PARAM_STR);
      $stmt->bindParam(':calid', $calid, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch();
      return $data;
    }

    function get_elementid($element_id, $type='container'){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT id FROM element WHERE element_id=:element_id AND type=:type");
      $stmt->bindParam(':element_id', $element_id, PDO::PARAM_STR);
      $stmt->bindParam(':type', $type, PDO::PARAM_STR);
      $stmt->execute();
      $data = $stmt->fetch();
      return $data;
    }


    function update($day){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('UPDATE element (element_id, class_name, cal_id, type, default_bootstrap, default_style, data_group, bootstrap_classes, innerHTML, innerText, data) VALUES(:element_id, :class_name, :cal_id, :type, :default_bootstrap, :default_style, :data_group, :bootstrap_classes, :innerHTML, :innerText, :data)');
      $statement->execute(array(
        'element_id' => $day->get_element_id(),
        'class_name' => $day->get_class_name(),
        'cal_id' => $day->get_cal_id(),
        'type'=> $element->get_type(),
        'default_bootstrap' => $day->get_default_bootstrap(),
        'default_style' => $day->get_default_style(),
        'data_group' => $day->get_data_group(),
        'bootstrap_classes' => $day->get_bootstrap_classes(),
        'innerHTML' => $day->get_innerHTML(),
        'innerText' => $day->get_innerText(),
        'data' => $day->get_data()
      ));
    }

    function delete($element_id){
      // construct the delete statement
      $pdo = $this->getPDO();
      $sql = 'DELETE FROM element
              WHERE id = :id';
      // prepare the statement for execution
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $element_id, PDO::PARAM_INT);
      // execute the statement
      //echo $statement->rowCount();
      $statement->execute();
      // return statment not excute on delete note with count if deleted
      return $statement;
    }

    function read_all(){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM element");
      $stmt->execute();
      $data = $stmt->fetchAll();
      return $data;
    }

    function read_all_cal_elements($cal_id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM `element` WHERE cal_id=?");
      $stmt->execute([$cal_id]);
      $data = $stmt->fetchAll();
      return $data;
    }

    function delete_all(){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('DELETE FROM element WHERE id > 0');
      return $statement->execute();
    }

    function delete_all_cal_elements($cal_id){
      $pdo = $this->getPDO();
      $sql = 'DELETE FROM element WHERE id > 0 AND cal_id = :cal_id';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':cal_id', $cal_id, PDO::PARAM_INT);
      $statement->execute();
      return $statement;
    }

    function update_column($column, $value, $id){
      $pdo = $this->getPDO();
      $sql = "UPDATE element SET ".$column."=? WHERE id=?";
      $stmt= $pdo->prepare($sql);
      $data = $stmt->execute([$value, $id]);
      return $data ? 1 : 0;
    }

    function get_total_elements(){
      $pdo = $this->getPDO();
      return $pdo->query('select count(id) from element')->fetchColumn();
    }

    // new update
    function insert_group_fast($data){
      $inserted_ids = array();
      $pdo = $this->getPDO();
      $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
      $pdo->beginTransaction(); // also helps speed up your inserts.
      $stmt = $pdo->prepare('INSERT INTO day(element_id, class_name, cal_id, type, default_bootstrap, default_style, group, bootstrap_classes, innerHTML, innerText, data) VALUES(:element_id, :class_name, :cal_id, :type, :default_bootstrap, :default_style, :group, :bootstrap_classes, :innerHTML, :innerText, :data)');
      foreach($data as $item)
      {
          $stmt->bindValue(':element_id', $item->get_element_id());
          $stmt->bindValue(':class_name', $item->get_class_name());
          $stmt->bindValue(':cal_id', $item->get_cal_id());
          $stmt->bindValue(':type', $item->get_type());
          $stmt->bindValue(':default_bootstrap', $item->get_default_bootstrap());
          $stmt->bindValue(':default_style', $item->get_default_style());
          $stmt->bindValue(':data_group', $item->get_data_data_group());
          $stmt->bindValue(':bootstrap_classes', $item->get_bootstrap_classes());
          $stmt->bindValue(':innerHTML', $item->get_innerHTML());
          $stmt->bindValue(':innerText', $item->get_innerText());
          $stmt->bindValue(':data', $item->get_data());
          $stmt->execute();
          $id = $pdo->lastInsertId();
          array_push($inserted_ids, $id);
      }
      $pdo->commit();
      $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
      return $inserted_ids;
    }


    function get_element_where($column, $value, $limit='', $and_column='', $and_val=''){
      $limit  = $limit != '' ? 'ORDER BY id LIMIT ' . $limit : ' ORDER BY id';
      $pdo = $this->getPDO();
      $data;
      if ($and_column != '' && $and_val != ''){

        $sql = "SELECT * FROM element WHERE ".$column."=? AND ".$and_column."=?" . $limit;
        $stmt = $pdo->prepare($sql);
        $data = $stmt->execute([$value, $and_val]);
      } else {

        $sql = "SELECT * FROM element WHERE ".$column."=?".$limit;
        $stmt = $pdo->prepare($sql);
        $data = $stmt->execute([$value]);
      }
      if ($data){
        return $stmt->fetchAll();
      } else {
        return array();
      }
    }


}
