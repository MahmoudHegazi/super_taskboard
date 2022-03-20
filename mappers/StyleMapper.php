<?php
class StyleMapper {
    /* Main CRUD */
    /**
      * @var PDO
    */

    protected $pdo;

    public function __construct(PDO $pdo)
    {
      $this->pdo = $pdo;
    }

    public function getPDO(){
      return $this->pdo;
    }

    public function insert($style) {
        $pdo = $this->getPDO();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $pdo->beginTransaction();
        $statement = $pdo->prepare('INSERT INTO style(classname, element_id, style, class_id, active, custom, cal_id, title, category) VALUES(:classname, :element_id, :style, :class_id, :active, :custom, :cal_id, :title, :category)');
        $statement->execute(array(
            'classname' => $style->get_classname(),
            'element_id' => $style->get_element_id(),
            'style' => $style->get_style(),
            'class_id' => $style->get_class_id(),
            'active' => $style->get_active(),
            'custom' => $style->get_custom(),
            'cal_id' => $style->get_cal_id(),
            'title' => $style->get_title(),
            'category' => $style->get_category()

        ));
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
        $pdo->commit();
        return $pdo->lastInsertId();
    }

    function read_one($style_id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM style WHERE id=:id");
      $stmt->bindParam(':id', $style_id, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch();
      return $data;
    }

    function update($style){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('UPDATE style (classname, element_id, style, class_id, active, custom, cal_id, title, category) VALUES(:classname, :element_id, :style, :class_id, :active, :custom, :cal_id, :title, :category)');
      $statement->execute(array(
        'classname' => $style->get_classname(),
        'element_id' => $style->get_element_id(),
        'style' => $style->get_style(),
        'class_id' => $style->get_class_id(),
        'active' => $style->get_active(),
        'custom' => $style->get_custom(),
        'cal_id' => $style->get_cal_id(),
        'title' => $style->get_title(),
        'category' => $style->get_title()


      ));
    }

    function delete($style_id){
      // construct the delete statement
      $pdo = $this->getPDO();
      $sql = 'DELETE FROM style
              WHERE id = :id';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $style_id, PDO::PARAM_INT);
      return $statement->execute();
    }

    function read_all($ative=1){
      if ($ative==1){
        $ative=' WHERE active=1';
      } else {
        $ative = '';
      }
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM style".$ative);
      $stmt->execute();
      $data = $stmt->fetchAll();
      return $data;
    }

    function read_class_styles($year, $month, $cal_id, $class_type='period'){
      $pdo = $this->getPDO();
      // load style for previous and after month as this pro cal get all days dynamic
      $min_month = $month > 1 ? $month - 1 : $month;
      $min_month = $min_month == 12 ? $min_month - 1 : $min_month;
      $max_month = $month >= 12 ? $month : $month + 1;

      $periods_sql = "SELECT style.* FROM style JOIN period ON period.id = style.class_id JOIN day
      ON period.day_id = day.id JOIN month ON day.month_id = month.id JOIN year ON month.year_id = year.id
      WHERE year.year=? AND month.month=? AND style.cal_id=? AND style.active=1";

      $slots_sql = "SELECT style.* FROM style JOIN slot ON slot.id = style.class_id JOIN
      period ON slot.period_id = period.id JOIN day ON period.day_id = day.id JOIN month ON
      day.month_id = month.id JOIN year ON month.year_id = year.id WHERE year.year=? AND month.month=? AND style.cal_id=? AND style.active=1";

      if ($class_type == 'slot'){
        $stmt = $pdo->prepare($slots_sql);
      } else {
        $stmt = $pdo->prepare($periods_sql);
      }
      $stmt->execute([$year, $month, $cal_id]);
      /*
      $stmt = $pdo->prepare("
       SELECT style.id, style.class_id, style.custom, style.category, style.title, style.cal_id, style.style, style.active, style.classname, style.element_id, style.category, month.month
       FROM calendar JOIN year ON calendar.id = year.cal_id JOIN month ON month.year_id = year.id JOIN
       day ON day.month_id = month.id JOIN period ON day.id = period.day_id JOIN slot ON
       period.id = slot.period_id JOIN style ON
       style.class_id=".$class_type." WHERE year.year=? AND (month=?) AND calendar.id=? AND style.active=1 "
      );
      $stmt->execute([$year, $month, $min_month, $max_month, $cal_id]);
      */
      //print_r($stmt);
      $data = $stmt->fetchAll();
      return $data;
    }

    function delete_all(){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('DELETE FROM style WHERE id > 0');
      return $statement->execute();
    }

    function update_column($column, $value, $id){
      $pdo = $this->getPDO();
      $sql = "UPDATE style ".$column."=? WHERE id=?";
      $stmt= $pdo->prepare($sql);
      return $stmt->execute([$value, $id]);
    }

    function get_total_styles(){
      $pdo = $this->getPDO();
      return $pdo->query('select count(id) from style')->fetchColumn();
    }

    function get_styles_where($column, $value, $limit=''){
      $pdo = $this->getPDO();
      if (!empty($value)){
        $start = substr( $value, 0 ,1) === "'";
        $end = substr( $value, strlen($value)-1 ,strlen($value)) === "'";
        if (!$start && !$end){
          $value = "'" . $value . "'";
        }
      }
      $limit  = $limit != '' ? ' LIMIT ' . $limit : '';
      $sql = "SELECT * FROM style WHERE ".$column."=".$value."".$limit;
      $stmt = $pdo->prepare($sql);
      $data = $stmt->execute();
      if ($data){
        return $stmt->fetchAll();
      } else {
        return array();
      }
    }


  function placeholders($text, $count=0, $separator=","){
      $result = array();
      if($count > 0){
        for($x=0; $x<$count; $x++){
          $result[] = $text;
        }
      }
      return implode($separator, $result);
  }


  function insert_group_fast($data){
    $pdo = $this->getPDO();
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    $pdo->beginTransaction(); // also helps speed up your inserts.
    $stmt = $pdo->prepare('INSERT INTO style(classname, element_id, style, class_id, active, cal_id, custom, title, category) VALUES(:classname, :element_id, :style, :class_id, :active, :cal_id, :custom, :title, :category)');
    $total = 0;
    foreach($data as $item)
    {
        $stmt->bindValue(':classname', $item->get_classname());
        $stmt->bindValue(':element_id', $item->get_element_id());
        $stmt->bindValue(':style', $item->get_style());
        $stmt->bindValue(':class_id', $item->get_class_id());
        $stmt->bindValue(':active', $item->get_active());
        $stmt->bindValue(':custom', $item->get_custom());
        $stmt->bindValue(':cal_id', $item->get_cal_id());
        $stmt->bindValue(':title', $item->get_title());
        $stmt->bindValue(':category', $item->get_category());
        $total += $stmt->execute() ? 1 : 0;
    }
    $pdo->commit();
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    return $total;
  }

}
