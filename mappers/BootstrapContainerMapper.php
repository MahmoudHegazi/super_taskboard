<?php
class BootstrapContainerMapper {
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

  public function insert($bootstrap_container) {
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('INSERT INTO bootstrap_container(bg, text_color, p, m, border, border_size, border_color, border_round, width, height, m_t, m_b, m_r, m_l, p_t, p_b, p_r, p_l, visibility, box_shadow, justify_content, align_items, ratio, flex_flow, flex_type, flex_wrap, align_content, element_id, cal_id) VALUES
      (:bg, :text_color, :p, :m, :border, :border_size, :border_color, :border_round, :width, :height, :m_t, :m_b, :m_r, :m_l, :p_t, :p_b, :p_r, :p_l, :visibility, :box_shadow, :justify_content, :align_items, :ratio, :flex_flow, :flex_type, :flex_wrap, :align_content, :element_id, :cal_id)');
      $statement->execute(array(
        'bg' => $bootstrap_container->get_bg(),
        'text_color' => $bootstrap_container->get_text_color(),
        'p' => $bootstrap_container->get_p(),
        'm' => $bootstrap_container->get_m(),
        'border' => $bootstrap_container->get_border(),
        'border_size' => $bootstrap_container->get_border_size(),
        'border_color' => $bootstrap_container->get_border_color(),
        'border_round' => $bootstrap_container->get_border_round(),
        'width' => $bootstrap_container->get_width(),
        'height' => $bootstrap_container->get_height(),
        'm_t' => $bootstrap_container->get_m_t(),
        'm_b' => $bootstrap_container->get_m_b(),
        'm_r' => $bootstrap_container->get_m_r(),
        'm_l' => $bootstrap_container->get_m_l(),
        'p_t' => $bootstrap_container->get_p_t(),
        'p_b' => $bootstrap_container->get_p_b(),
        'p_r' => $bootstrap_container->get_p_r(),
        'p_l' => $bootstrap_container->get_p_l(),
        'visibility' => $bootstrap_container->get_visibility(),
        'box_shadow'=> $bootstrap_container->get_box_shadow(),
        'justify_content' => $bootstrap_container->get_justify_content(),
        'align_items' => $bootstrap_container->get_align_items(),
        'ratio' => $bootstrap_container->get_ratio(),
        'flex_flow' => $bootstrap_container->get_flex_flow(),
        'flex_type' => $bootstrap_container->get_flex_type(),
        'flex_wrap' => $bootstrap_container->get_flex_wrap(),
        'align_content' => $bootstrap_container->get_align_content(),
        'element_id' => $bootstrap_container->get_element_id(),
        'cal_id' => $bootstrap_container->get_cal_id()
      ));
      return $pdo->lastInsertId();
  }


  function read_one($container_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM bootstrap_container WHERE id=:container_id");
    $stmt->bindParam(':container_id', $container_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();
    return $data;
  }

  function getid($container_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT element_id FROM bootstrap_container WHERE id=:container_id");
    $stmt->bindParam(':container_id', $container_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data;
  }


  function read_one_column($col, $id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT " . $col . " FROM bootstrap_container WHERE id = :id LIMIT 1");
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      return $data;
  }

  function update($bootstrap_container){
    $pdo = $this->getPDO();
      $statement = $pdo->prepare('UPDATE bootstrap_container(bg, text_color, p, m, border, border_size, border_color, border_round, width, height, m_t, m_b, m_r, m_l, p_t, p_b, p_r, p_l, visibility, box_shadow, justify_content, align_items, ratio, flex_flow, flex_type, flex_wrap, align_content, element_id, cal_id) VALUES (:bg, :text_color, :p, :m, :border, :border_size, :border_color, :border_round, :width, :height, :m_t, :m_b, :m_r, :m_l, :p_t, :p_b, :p_r, :p_l, :visibility, :box_shadow, :justify_content, :align_items, :ratio, :flex_flow, :flex_type, :flex_wrap, :align_content, :element_id, :cal_id)');
    $statement->execute(array(
      'bg' => $bootstrap_container->get_bg(),
      'text_color' => $bootstrap_container->get_text_color(),
      'p' => $bootstrap_container->get_p(),
      'm' => $bootstrap_container->get_m(),
      'border' => $bootstrap_container->get_border(),
      'border_size' => $bootstrap_container->get_border_size(),
      'border_color' => $bootstrap_container->get_border_color(),
      'border_round' => $bootstrap_container->get_border_round(),
      'width' => $bootstrap_container->get_width(),
      'height' => $bootstrap_container->get_height(),
      'm_t' => $bootstrap_container->get_m_t(),
      'm_b' => $bootstrap_container->get_m_b(),
      'm_r' => $bootstrap_container->get_m_r(),
      'm_l' => $bootstrap_container->get_m_l(),
      'p_t' => $bootstrap_container->get_p_t(),
      'p_b' => $bootstrap_container->get_p_b(),
      'p_r' => $bootstrap_container->get_p_r(),
      'p_l' => $bootstrap_container->get_p_l(),
      'visibility' => $bootstrap_container->get_visibility(),
      'box_shadow'=> $bootstrap_container->get_box_shadow(),
      'justify_content' => $bootstrap_container->get_justify_content(),
      'align_items' => $bootstrap_container->get_align_items(),
      'ratio' => $bootstrap_container->get_ratio(),
      'flex_flow' => $bootstrap_container->get_flex_flow(),
      'flex_type' => $bootstrap_container->get_flex_type(),
      'flex_wrap' => $bootstrap_container->get_flex_wrap(),
      'align_content' => $bootstrap_container->get_align_content(),
      'element_id' => $bootstrap_container->get_element_id(),
      'cal_id' => $bootstrap_container->get_cal_id()
    ));
  }

  function delete($container_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM bootstrap_container WHERE id = :container_id';
    // prepare the statement for execution
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $container_id, PDO::PARAM_INT);
    return $statement->execute();
  }

  function read_all(){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM bootstrap_container");
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  function delete_all(){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('DELETE FROM bootstrap_container WHERE id > 0');
    return $statement->execute();
  }

  function update_column($column, $value, $id){
    $pdo = $this->getPDO();
    $sql = "UPDATE bootstrap_container SET ".$column."=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }
  function show_column_names(){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM bootstrap_container LIMIT 1");
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data;
  }
  function is_valid_column_enum_value($column, $value){
    try {
      $column_enums = [];
      $pdo = $this->getPDO();
      $sql = 'SHOW COLUMNS FROM bootstrap_container WHERE field="'.$column.'"';
      $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

      foreach(explode("','",substr($row['Type'],6,-2)) as $option) {
         $column_enums[] = $option;
       }
      return in_array($value, $column_enums);
    }
    catch( Exception $e ) {
      return 0;
    }
  }

  function get_total_calendar_bscontainers($cal_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT COUNT(id) from bootstrap_container WHERE cal_id= :cal_id';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':cal_id', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

  function get_bsid_by_element($element_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM bootstrap_container WHERE element_id =:element_id LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':element_id', $element_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }

  function get_bs_by_element($element_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM bootstrap_container WHERE element_id =:element_id LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':element_id', $element_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }

  function get_bscontainerid_where_str($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM bootstrap_container WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

  function get_bscontainerid_where_int($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM bootstrap_container WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }
  function get_bscontainer_data_where_init($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM bootstrap_container WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }

  function get_bscontainer_data_where_str($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM bootstrap_container WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }



}
