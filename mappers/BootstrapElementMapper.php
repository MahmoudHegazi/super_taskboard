<?php
class BootstrapElementMapper {
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
    try {
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('INSERT INTO bootstrap_element(bg, text_color, p, m, border, border_size, border_color, border_round, width, height, m_t, m_b, m_r, m_l, p_t, p_b, p_r, p_l, visibility, box_shadow, flex_fill, flex_grow, ms_auto, flex_order, vertical_align, col_sm, h, display, text_wrap, font_weight, text_case, badge, float_position, text_align, text_break, center_content, element_id, cal_id) VALUES
      (:bg, :text_color, :p, :m, :border, :border_size, :border_color, :border_round, :width, :height, :m_t, :m_b, :m_r, :m_l, :p_t, :p_b, :p_r, :p_l, :visibility, :box_shadow, :flex_fill, :flex_grow, :ms_auto, :flex_order, :vertical_align, :col_sm, :h, :display, :text_wrap, :font_weight, :text_case, :badge, :float_position, :text_align, :text_break, :center_content, :element_id, :cal_id)');

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
        'flex_fill' => $bootstrap_container->get_flex_fill(),
        'flex_grow'=> $bootstrap_container->get_flex_grow(),
        'ms_auto' => $bootstrap_container->get_ms_auto(),
        'flex_order' => $bootstrap_container->get_flex_order(),
        'vertical_align' => $bootstrap_container->get_vertical_align(),
        'col_sm' => $bootstrap_container->get_col_sm(),
        'h' => $bootstrap_container->get_h(),
        'display' => $bootstrap_container->get_display(),
        'text_wrap' => $bootstrap_container->get_text_wrap(),
        'font_weight' => $bootstrap_container->get_font_weight(),
        'text_case' => $bootstrap_container->get_text_case(),
        'badge' => $bootstrap_container->get_badge(),
        'float_position' => $bootstrap_container->get_float_position(),
        'text_align' => $bootstrap_container->get_text_align(),
        'text_break' => $bootstrap_container->get_text_break(),
        'center_content' => $bootstrap_container->get_center_content(),
        'element_id' => $bootstrap_container->get_element_id(),
        'cal_id' => $bootstrap_container->get_cal_id()
      ));
      return $pdo->lastInsertId();
    } catch(Exception $e){
      $error = $e->getMessage();
      return 0;
    }
    return 0;
  }

  function getid($element_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT element_id FROM bootstrap_element WHERE id=:element_id");
    $stmt->bindParam(':element_id', $element_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data;
  }

  function show_column_names(){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM bootstrap_element LIMIT 1");
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data;
  }

  function is_valid_column_enum_value($column, $value){
    try {
      $column_enums = [];
      $pdo = $this->getPDO();
      $sql = 'SHOW COLUMNS FROM bootstrap_element WHERE field="'.$column.'"';
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

  function read_one_column($col, $id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT " . $col . " FROM bootstrap_element WHERE id = :id LIMIT 1");
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      return $data;
  }


  function read_one($log_id){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM bootstrap_element WHERE id=:id");
    $stmt->bindParam(':id', $log_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();
    return $data;
  }




  function update($user){
    $pdo = $this->getPDO();
      $statement = $pdo->prepare('UPDATE bootstrap_element(bg, text_color, p, m, border, border_size, border_color, border_round, width, height, m_t, m_b, m_r, m_l, p_t, p_b, p_r, p_l, visibility, box_shadow, flex_fill, flex_grow, ms_auto, flex_order, vertical_align, vertical_align, col_sm, h, display, text_wrap, font_weight, text_case, badge, float_position, text_align, text_break, center_content, element_id, cal_id) VALUES (:bg, :text_color, :p, :m, :border, :border_size, :border_color, :border_round, :width, :height, :m_t, :m_b, :m_r, :m_l, :p_t, :p_b, :p_r, :p_l, :visibility, :box_shadow, :flex_fill, :flex_grow, :ms_auto, :flex_order, :vertical_align, :vertical_align, :col_sm, :h, :display, :text_wrap, :font_weight, :text_case, :badge, :float_position, :text_align, :text_break, :center_content, :element_id, :cal_id)');
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
      'flex_fill' => $bootstrap_container->get_flex_fill(),
      'flex_grow'=> $bootstrap_container->get_flex_grow(),
      'ms_auto' => $bootstrap_container->get_ms_auto(),
      'flex_order' => $bootstrap_container->get_flex_order(),
      'vertical_align' => $bootstrap_container->get_vertical_align(),
      'col_sm' => $bootstrap_container->get_col_sm(),
      'h' => $bootstrap_container->get_h(),
      'display' => $bootstrap_container->get_display(),
      'text_wrap' => $bootstrap_container->get_text_wrap(),
      'font_weight' => $bootstrap_container->get_font_weight(),
      'text_case' => $bootstrap_container->get_text_case(),
      'badge' => $bootstrap_container->get_badge(),
      'float_position' => $bootstrap_container->get_float_position(),
      'text_align' => $bootstrap_container->get_text_align(),
      'text_break' => $bootstrap_container->get_text_break(),
      'center_content' => $bootstrap_container->get_center_content(),
      'element_id' => $bootstrap_container->get_element_id(),
      'cal_id' => $bootstrap_container->get_cal_id()
    ));
  }

  function delete($container_id){
    // construct the delete statement
    $pdo = $this->getPDO();
    $sql = 'DELETE FROM bootstrap_element WHERE id = :container_id';
    // prepare the statement for execution
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $container_id, PDO::PARAM_INT);
    return $statement->execute();
  }

  function read_all(){
    $pdo = $this->getPDO();
    $stmt = $pdo->prepare("SELECT * FROM bootstrap_element");
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  function delete_all(){
    $pdo = $this->getPDO();
    $statement = $pdo->prepare('DELETE FROM bootstrap_element WHERE id > 0');
    return $statement->execute();
  }

  function update_column($column, $value, $id){
    $pdo = $this->getPDO();
    $sql = "UPDATE bootstrap_element SET ".$column."=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    return $stmt->execute([$value, $id]);
  }

  function get_total_calendar_bselements($cal_id){
    $sql = 'SELECT COUNT(id) from bootstrap_element WHERE cal_id= :cal_id';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':cal_id', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }



  function get_bsid_by_element($element_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM bootstrap_element WHERE element_id = :element_id LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':element_id', $element_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }


  function get_bs_by_element($element_id){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM bootstrap_element WHERE element_id = :element_id LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':element_id', $element_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }

  function get_bselementid_where_str($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM bootstrap_element WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

  function get_bselementid_where_int($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT id FROM bootstrap_element WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }
  function get_bselement_data_where_int($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM bootstrap_element WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }

  function get_bselement_data_where_str($column, $value){
    $pdo = $this->getPDO();
    $sql = 'SELECT * FROM bootstrap_element WHERE '.test_input($column).' = :value LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':value', $value, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch();
  }

}
