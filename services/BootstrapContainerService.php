<?php
require_once(dirname(__FILE__, 2) . '/config.php');
require_once(dirname(__FILE__, 2) . '/mappers/BootstrapContainerMapper.php');
require_once(dirname(__FILE__, 2) . '/models/BootstrapContainer.php');

class BootstrapContainerService {
  protected $pdo;
  protected $bootstrap_container_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->bootstrap_container_mapper = new BootstrapContainerMapper($pdo);
  }

  function is_valid_key($column_name='') {
    $valid_column = false;
    $col_names = $this->bootstrap_container_mapper->show_column_names();
    foreach($col_names as $key => $value) {
      if ($key == $column_name){
        $valid_column = true;
      }
    }
    return $valid_column;
  }


  // Add New bs_container
  function add(
    $element_id, $cal_id, $bg='', $text_color='', $p='', $m='', $border='', $border_size='', $border_color='', $border_round='', $width='', $height='',
    $m_t='', $m_b='', $m_r='', $m_l='', $p_t='', $p_b='', $p_r='', $p_l='', $visibility='', $box_shadow='', $justify_content='', $align_items='',
    $ratio='', $flex_flow='', $flex_type='', $flex_wrap='', $align_content=''
    ){

    $bs_container = new BootstrapContainer();
    $bs_container->init(
      $element_id, $cal_id, $bg, $text_color, $p, $m, $border, $border_size, $border_color, $border_round, $width, $height, $m_t, $m_b, $m_r,
      $m_l, $p_t, $p_b, $p_r, $p_l, $visibility, $box_shadow, $justify_content, $align_items, $ratio, $flex_flow, $flex_type, $flex_wrap, $align_content
    );
    return $this->bootstrap_container_mapper->insert($bs_container);
  }

  // Remove  bs_container
  function remove($bscontainer_id){
    return $this->bootstrap_container_mapper->delete($bscontainer_id);
  }

  // Get bs_container Using it's bscontainer_id
  function get_bs_container_by_id($bscontainer_id){

    $container_row = $this->bootstrap_container_mapper->read_one($bscontainer_id);

    // if element not found
    if (!isset($container_row['id']) || empty($container_row['id'])){return array();}
    $bs_container = new BootstrapContainer();
    $bs_container->init(
      $container_row['element_id'], $container_row['cal_id'],
      $container_row['bg'], $container_row['text_color'],
      $container_row['p'], $container_row['m'],
      $container_row['border'], $container_row['border_size'],
      $container_row['border_color'], $container_row['border_round'],
      $container_row['width'], $container_row['height'], $container_row['m_t'],
      $container_row['m_b'], $container_row['m_r'],
      $container_row['m_l'], $container_row['p_t'],
      $container_row['p_b'], $container_row['p_r'],
      $container_row['p_l'], $container_row['visibility'],
      $container_row['box_shadow'], $container_row['justify_content'],
      $container_row['align_items'], $container_row['ratio'], $container_row['flex_flow'],
      $container_row['flex_type'], $container_row['flex_wrap'],
      $container_row['align_content'],
      $container_row['last_update']
    );
    $bs_container->set_id($container_row['id']);
    $bs_container->set_last_update($container_row['last_update']);
    return $bs_container;
  }

  function get_public_bs_container_by_id($id){
    $container_row = $this->get_bs_container_by_id($id);
    // if element not found
    if (!isset($container_row) || empty($container_row)){return array();}
    $bs_container = array(
      'bg'=>$container_row->get_bg(),
      'text_color'=>$container_row->get_text_color(),
      'p'=>$container_row->get_p(),
      'm'=>$container_row->get_m(),
      'border'=>$container_row->get_border(),
      'border_size'=>$container_row->get_border_size(),
      'border_color'=>$container_row->get_border_color(),
      'border_round'=>$container_row->get_border_round(),
      'width'=>$container_row->get_width(),
      'height'=>$container_row->get_height(),
      'm_t'=>$container_row->get_m_t(),
      'm_b'=>$container_row->get_m_b(),
      'm_r'=>$container_row->get_m_r(),
      'm_l'=>$container_row->get_m_l(),
      'p_t'=>$container_row->get_p_t(),
      'p_b'=>$container_row->get_p_b(),
      'p_r'=>$container_row->get_p_r(),
      'p_l'=>$container_row->get_p_l(),
      'visibility'=>$container_row->get_visibility(),
      'box_shadow'=>$container_row->get_box_shadow(),
      'justify_content'=>$container_row->get_justify_content(),
      'align_items'=>$container_row->get_align_items(),
      'ratio'=>$container_row->get_ratio(),
      'flex_flow'=>$container_row->get_flex_flow(),
      'flex_type'=>$container_row->get_flex_type(),
      'flex_wrap'=>$container_row->get_flex_wrap(),
      'align_content'=>$container_row->get_align_content(),
      'last_update'=>$container_row->get_last_update(),
      'element_id'=>$container_row->get_element_id(),
      'cal_id'=>$container_row->get_cal_id(),
      'id'=>$container_row->get_id()
    );
    return $bs_container;
  }

  function get_bootstrap_classes_by_element($bscontainer_id){
    $bootstrap_classes = ' ';
    $container_row = $this->bootstrap_container_mapper->get_bs_by_element($bscontainer_id);
    if (!isset($container_row) || empty($container_row)){
      // this good it will return error if u mistake in table
      //echo $bscontainer_id;
      return '';
    }
    // my way to get assoc or index array from the pdo fetch which retun index and assoc (not get empty values)
    $get_assoc_array = array();
    foreach ($container_row as $key => $value) {
      if (!is_numeric($key) && !empty($key) && !empty($value)){
        array_push($get_assoc_array, array($key,$value));
      }
    }
    for ($c=0; $c<count($get_assoc_array); $c++){
      $current_class = $get_assoc_array[$c];
      if (count($current_class) != 2){continue;}
      if ($current_class[0] == 'id' || $current_class[0] == 'element_id' || $current_class[0] == 'last_update' || $current_class[0] == 'cal_id'){
        continue;
      }
      $bootstrap_classes .= $current_class[1] . ' ';
    }
    if (trim($bootstrap_classes," ") == ''){
      $bootstrap_classes = '';
    }
    return $bootstrap_classes;
  }

  function get_bscontainerid_by_element($element_id){
    // if element not found
    $container_row = $this->bootstrap_container_mapper->get_bsid_by_element($element_id);
    if (isset($container_row['id']) && !empty($container_row['id'])){
      return $container_row['id'];
    } else {
      return false;
    }
  }

  function get_bscontainer_by_element($element_id){
    // if element not found
    $container_row = $this->bootstrap_container_mapper->get_bs_by_element($element_id);
    // if element not found
    if (!isset($container_row['id']) || empty($container_row['id'])){return array();}
    $bs_container = new BootstrapContainer();
    $bs_container->init(
      $container_row['bg'], $container_row['text_color'],
      $container_row['p'], $container_row['m'],
      $container_row['border'], $container_row['border_size'],
      $container_row['border_color'], $container_row['border_round'],
      $container_row['width'], $container_row['height'], $container_row['m_t'],
      $container_row['m_b'], $container_row['m_r'],
      $container_row['m_l'], $container_row['p_t'],
      $container_row['p_b'], $container_row['p_r'],
      $container_row['p_l'], $container_row['visibility'],
      $container_row['box_shadow'], $container_row['justify_content'],
      $container_row['align_items'], $container_row['ratio'], $container_row['flex_flow'],
      $container_row['flex_type'], $container_row['flex_wrap'],
      $container_row['align_content'],
      $container_row['last_update'], $container_row['element_id'], $container_row['cal_id']
    );
    $bs_container->set_id($container_row['id']);
    return $container_row;
  }


  function convertDataToBSContainer(
    $element_id, $cal_id, $bg='', $text_color='', $p='', $m='', $border='', $border_size='', $border_color='', $border_round='', $width='', $height='',
    $m_t='', $m_b='', $m_r='', $m_l='', $p_t='', $p_b='', $p_r='', $p_l='', $visibility='', $box_shadow='', $justify_content='', $align_items='',
    $ratio='', $flex_flow='', $flex_type='', $flex_wrap='', $align_content=''){
    $bs_container = new BootstrapContainer();
    $bs_container->init(
      $element_id, $cal_id, $bg, $text_color, $p, $m, $border, $border_size, $border_color, $border_round, $width, $height, $m_t, $m_b, $m_r,
      $m_l, $p_t, $p_b, $p_r, $p_l, $visibility, $box_shadow, $justify_content, $align_items, $ratio, $flex_flow, $flex_type, $flex_wrap, $align_content
    );
    $bs_container->set_id(null);
    return $bs_container;
  }

  // get All user
  function get_all_bs_containers(){

    $bs_container_list = array();
    $container_rows = $this->bootstrap_container_mapper->read_all();
    if (count($container_rows) == 0){return array();}

    for ($i=0; $i<count($log_rows); $i++){
        $bs_container = new BootstrapContainer();
        $bs_container->init(
          $container_rows[$i]['bg'], $container_rows[$i]['text_color'],
          $container_rows[$i]['p'], $container_rows[$i]['m'],
          $container_rows[$i]['border'], $container_rows[$i]['border_size'],
          $container_rows[$i]['border_color'], $container_rows[$i]['border_round'],
          $container_rows[$i]['width'], $container_rows[$i]['height'], $container_rows[$i]['m_t'],
          $container_rows[$i]['m_b'], $container_rows[$i]['m_r'],
          $container_rows[$i]['m_l'], $container_rows[$i]['p_t'],
          $container_rows[$i]['p_b'], $container_rows[$i]['p_r'],
          $container_rows[$i]['p_l'], $container_rows[$i]['visibility'],
          $container_rows[$i]['box_shadow'], $container_rows[$i]['justify_content'],
          $container_rows[$i]['align_items'], $container_rows[$i]['ratio'], $container_rows[$i]['flex_flow'],
          $container_rows[$i]['flex_type'], $container_rows[$i]['flex_wrap'],
          $container_rows[$i]['align_content'],
          $container_row['last_update'], $container_row['element_id'], $container_row['cal_id']
        );
        $bs_container->set_id($container_rows[$i]['id']);
        array_push($bs_container_list, $bs_container);
    }
    return $bs_container_list;
  }


  function delete_logs($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  log
  function update_one_column($column, $value, $id){
    return $this->bootstrap_container_mapper->update_column($column, $value, $id);
  }

  function update_bs_by_contgroup($bs_class, $bs_value, $data_group){
    return $this->bootstrap_container_mapper->update_bs_containerss_by_elm_group_fast($bs_class, $bs_value, $data_group);
  }


  function get_total_bscontainers(){
    return $this->bootstrap_container_mapper->get_total_calendar_bscontainers();
  }

  function is_valid_column_enum_value($column, $value){
    $column = test_input($column);
    $value = test_input($value);
    return $this->bootstrap_container_mapper->is_valid_column_enum_value($column, $value);
  }

  function get_bs_element_id($container_id){
    $elm_row = $this->bootstrap_container_mapper->getid($container_id);
    if (isset($elm_row['element_id']) && !empty($elm_row['element_id'])){
      return intval($elm_row['element_id']);
    } else {
      print_r($elm_row);
      return 0;
    }
  }

  function get_column_value($col, $id){
    $col = test_input($col);
    $col_value_row = $this->bootstrap_container_mapper->read_one_column($col, $id);
    if (isset($col_value_row[$col])){
       return $col_value_row[$col];
    } else {
      return '';
    }
  }

  // used for get bscontainer id by init value
  function get_bscontainerid_where_str($column, $value){
    $data = $this->bootstrap_container_mapper->get_bscontainerid_where_str($column, $value);
    if (isset($data['id']) && !empty($data['id'])){
      return $data['id'];
    } else {
      return false;
    }
  }

  // used for get bscontainer id by init value
  function get_bscontainerid_where_int($column, $value){
    $data = $this->bootstrap_container_mapper->get_bscontainerid_where_int($column, $value);
    if (isset($data['id']) && !empty($data['id'])){
      return $data['id'];
    } else {
      return false;
    }
  }

  // used for get full bscontainer data by str value
  function get_bscontainer_data_where_str($column, $value){
    $container_row = $this->bootstrap_container_mapper->get_bscontainer_data_where_str($column, $value);
    if (!isset($container_row) || empty($container_row)){return array();}
    $bs_container = new BootstrapContainer();
    $bs_container->init(
      $container_row['bg'], $container_row['text_color'],
      $container_row['p'], $container_row['m'],
      $container_row['border'], $container_row['border_size'],
      $container_row['border_color'], $container_row['border_round'],
      $container_row['width'], $container_row['height'], $container_row['m_t'],
      $container_row['m_b'], $container_row['m_r'],
      $container_row['m_l'], $container_row['p_t'],
      $container_row['p_b'], $container_row['p_r'],
      $container_row['p_l'], $container_row['visibility'],
      $container_row['box_shadow'], $container_row['justify_content'],
      $container_row['align_items'], $container_row['ratio'], $container_row['flex_flow'],
      $container_row['flex_type'], $container_row['flex_wrap'],
      $container_row['align_content'],
      $container_row['last_update'], $container_row['element_id'], $container_row['cal_id']
    );
    $bs_container->set_id($container_row['id']);
    return $bs_container;
  }

  function get_bscontainer_data_where_init($column, $value){
    $container_row = $this->bootstrap_container_mapper->get_bscontainer_data_where_init($column, $value);
    if (!isset($container_row) || empty($container_row)){return array();}
    $bs_container = new BootstrapContainer();
    $bs_container->init(
      $container_row['bg'], $container_row['text_color'],
      $container_row['p'], $container_row['m'],
      $container_row['border'], $container_row['border_size'],
      $container_row['border_color'], $container_row['border_round'],
      $container_row['width'], $container_row['height'], $container_row['m_t'],
      $container_row['m_b'], $container_row['m_r'],
      $container_row['m_l'], $container_row['p_t'],
      $container_row['p_b'], $container_row['p_r'],
      $container_row['p_l'], $container_row['visibility'],
      $container_row['box_shadow'], $container_row['justify_content'],
      $container_row['align_items'], $container_row['ratio'], $container_row['flex_flow'],
      $container_row['flex_type'], $container_row['flex_wrap'],
      $container_row['align_content'],
      $container_row['last_update'], $container_row['element_id'], $container_row['cal_id']
    );
    $bs_container->set_id($container_row['id']);
    return $bs_container;
  }

}
