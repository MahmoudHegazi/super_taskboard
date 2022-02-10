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

    function read_all(){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM style");
      $stmt->execute();
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

}
