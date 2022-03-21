<?php
require_once(dirname(__FILE__, 2) . '/config.php');
require_once(dirname(__FILE__, 2) . '/mappers/BootstrapElementMapper.php');
require_once(dirname(__FILE__, 2) . '/models/BootstrapElement.php');

class BootstrapElementService {
  protected $pdo;
  protected $bootstrap_element_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->bootstrap_element_mapper = new BootstrapElementMapper($pdo);
  }
  // Add New bs_container
  function add(
    $element_id, $cal_id, $bg='', $text_color='', $p='', $m='', $border='', $border_size='', $border_color='', $border_round='', $width='', $height='',
    $m_t='', $m_b='', $m_r='', $m_l='', $p_t='', $p_b='', $p_r='', $p_l='', $visibility='', $box_shadow='', $flex_fill='', $flex_grow='', $ms_auto='',
    $flex_order='', $vertical_align='', $col_sm='', $h='', $display='', $text_wrap='', $font_weight='', $text_case='', $badge='',
    $float_position='', $text_align='', $text_break='', $center_content=''
    ){
    $bs_element = new BootstrapElement();
    $bs_element->init(
      $element_id, $cal_id, $bg, $text_color, $p, $m, $border, $border_size, $border_color, $border_round, $width, $height,
      $m_t, $m_b, $m_r, $m_l, $p_t, $p_b, $p_r, $p_l, $visibility, $box_shadow, $flex_fill, $flex_grow, $ms_auto,
      $flex_order, $vertical_align, $col_sm, $h, $display, $text_wrap, $font_weight, $text_case, $badge,
      $float_position, $text_align, $text_break, $center_content
    );
    return $this->bootstrap_element_mapper->insert($bs_element);
  }

  // Remove  bs_element
  function remove($bscontainer_id){
    return $this->bootstrap_element_mapper->delete($bscontainer_id);
  }

  // Get bs_element Using it's bscontainer_id
  function get_bs_element_by_id($bs_elmid){
    $element_row = $this->bootstrap_element_mapper->read_one($bs_elmid);
    // if element not found
    if (!isset($element_row['id']) || empty($element_row['id'])){return array();}
    $bs_element = new BootstrapElement();
    $bs_element->init(
      $container_row['element_id'], $container_row['cal_id'],
      $element_row['bg'], $element_row['text_color'],
      $element_row['p'], $element_row['m'],
      $element_row['border'], $element_row['border_size'],
      $element_row['border_color'], $element_row['border_round'],
      $element_row['width'], $element_row['height'], $element_row['m_t'],
      $element_row['m_b'], $element_row['m_r'],
      $element_row['m_l'], $element_row['p_t'],
      $element_row['p_b'], $element_row['p_r'],
      $element_row['p_l'], $element_row['visibility'],
      $element_row['box_shadow'], $element_row['flex_fill'],
      $element_row['flex_grow'], $element_row['ms_auto'], $element_row['flex_order'],
      $element_row['vertical_align'], $element_row['col_sm'],
      $element_row['h'], $element_row['display'],
      $element_row['text_wrap'], $element_row['font_weight'],
      $element_row['text_case'], $element_row['badge'],
      $element_row['float_position'], $element_row['text_align'],
      $element_row['text_break'], $element_row['center_content'],
      $container_row['last_update']
    );
    $bs_element->set_id($element_row['id']);
    return $bs_element;
  }

  function get_public_bs_element_by_id($bs_elmid){
    $element_row = $this->bootstrap_element_mapper->read_one($bs_elmid);
    // if element not found
    if (!isset($element_row) || empty($element_row)){return array();}
    $bs_element = array(
      'bg'=>$element_row['bg'],
      'text_color'=>$element_row['text_color'],
      'p'=>$element_row['p'],
      'm'=>$element_row['m'],
      'border'=>$element_row['border'],
      'border_size'=>$element_row['border_size'],
      'border_color'=>$element_row['border_color'],
      'border_round'=>$element_row['border_round'],
      'width'=>$element_row['width'],
      'height'=>$element_row['height'],
      'm_t'=>$element_row['m_t'],
      'm_b'=>$element_row['m_b'],
      'm_r'=>$element_row['m_r'],
      'm_l'=>$element_row['m_l'],
      'p_t'=>$element_row['p_t'],
      'p_b'=>$element_row['p_b'],
      'p_r'=>$element_row['p_r'],
      'p_l'=>$element_row['p_l'],
      'visibility'=>$element_row['visibility'],
      'box_shadow'=>$element_row['box_shadow'],
      'flex_fill'=>$element_row['flex_fill'],
      'flex_grow'=>$element_row['flex_grow'],
      'ms_auto'=>$element_row['ms_auto'],
      'flex_order'=>$element_row['flex_order'],
      'vertical_align'=>$element_row['vertical_align'],
      'col_sm'=>$element_row['col_sm'],
      'h'=>$element_row['h'],
      'display'=>$element_row['display'],
      'text_wrap'=>$element_row['text_wrap'],
      'font_weight'=>$element_row['font_weight'],
      'text_case'=>$element_row['text_case'],
      'badge'=>$element_row['badge'],
      'float_position'=>$element_row['float_position'],
      'text_align'=>$element_row['text_align'],
      'text_break'=>$element_row['text_break'],
      'center_content'=>$element_row['center_content'],
      'last_update'=>$element_row['last_update'],
      'element_id'=>$element_row['element_id'],
      'cal_id'=>$element_row['cal_id'],
      'id'=>$element_row['id']
    );
    return $bs_element;
  }

  function get_bs_element_id($container_id){
    $elm_row = $this->bootstrap_element_mapper->getid($container_id);
    if (isset($elm_row['element_id']) && !empty($elm_row['element_id'])){
      return intval($elm_row['element_id']);
    } else {
      print_r($elm_row);
      return 0;
    }
  }

  function is_valid_key($column_name='') {
    $valid_column = false;
    $col_names = $this->bootstrap_element_mapper->show_column_names();
    foreach($col_names as $key => $value) {
      if ($key == $column_name){
        $valid_column = true;
      }
    }
    return $valid_column;
  }

  function is_valid_column_enum_value($column, $value){
    $column = test_input($column);
    $value = test_input($value);
    return $this->bootstrap_element_mapper->is_valid_column_enum_value($column, $value);
  }


  function get_column_value($col, $id){
    $col = test_input($col);
    $col_value_row = $this->bootstrap_element_mapper->read_one_column($col, $id);
    if (isset($col_value_row[$col])){
       return $col_value_row[$col];
    } else {
      return '';
    }
  }




  function get_bscontainer_by_element($element_id){
    // if element not found
    $element_row = $this->bootstrap_element_mapper->get_bs_by_element($element_id);
    // if element not found
    if (!isset($element_row['id']) || empty($element_row['id'])){return array();}
    $bs_element = new BootstrapElement();
    $bs_element->init(
      $container_row['element_id'], $container_row['cal_id'],
      $element_row['bg'], $element_row['text_color'],
      $element_row['p'], $element_row['m'],
      $element_row['border'], $element_row['border_size'],
      $element_row['border_color'], $element_row['border_round'],
      $element_row['width'], $element_row['height'], $element_row['m_t'],
      $element_row['m_b'], $element_row['m_r'],
      $element_row['m_l'], $element_row['p_t'],
      $element_row['p_b'], $element_row['p_r'],
      $element_row['p_l'], $element_row['visibility'],
      $element_row['box_shadow'], $element_row['flex_fill'],
      $element_row['flex_grow'], $element_row['ms_auto'], $element_row['flex_order'],
      $element_row['vertical_align'], $element_row['col_sm'],
      $element_row['h'], $element_row['display'],
      $element_row['text_wrap'], $element_row['font_weight'],
      $element_row['text_case'], $element_row['badge'],
      $element_row['float_position'], $element_row['text_align'],
      $element_row['text_break'], $element_row['center_content'],
      $container_row['last_update']
    );
    $bs_element->set_id($element_row['id']);
    return $element_row;
  }


  function convertDataToBSContainer(
    $element_id, $cal_id, $bg='', $text_color='', $p='', $m='', $border='', $border_size='', $border_color='', $border_round='', $width='', $height='',
    $m_t='', $m_b='', $m_r='', $m_l='', $p_t='', $p_b='', $p_r='', $p_l='', $visibility='', $box_shadow='', $flex_fill='', $flex_grow='', $ms_auto='',
    $flex_order='', $vertical_align='', $col_sm='', $h='', $display='', $text_wrap='', $font_weight='', $text_case='', $badge='',
    $float_position='', $text_align='', $text_break='', $center_content=''
  ){
    $bs_element = new BootstrapElement();
    $bs_element->init(
      $element_id, $cal_id, $bg, $text_color, $p, $m, $border, $border_size, $border_color, $border_round, $width, $height,
      $m_t, $m_b, $m_r, $m_l, $p_t, $p_b, $p_r, $p_l, $visibility, $box_shadow, $flex_fill, $flex_grow, $ms_auto,
      $flex_order, $vertical_align, $col_sm, $h, $display, $text_wrap, $font_weight, $text_case, $badge,
      $float_position, $text_align, $text_break, $center_content
    );
    $bs_element->set_id(null);
    return $bs_element;
  }

  // get All user
  function get_all_bs_bselements(){

    $bs_element_list = array();
    $element_rows = $this->bootstrap_element_mapper->read_all();
    if (count($element_rows) == 0){return array();}

    for ($i=0; $i<count($log_rows); $i++){
        $bs_element = new BootstrapElement();
        $bs_element->init(
          $element_rows[$i]['bg'], $element_rows[$i]['text_color'],
          $element_rows[$i]['p'], $element_rows[$i]['m'],
          $element_rows[$i]['border'], $element_rows[$i]['border_size'],
          $element_rows[$i]['border_color'], $element_rows[$i]['border_round'],
          $element_rows[$i]['width'], $element_rows[$i]['height'], $element_rows[$i]['m_t'],
          $element_rows[$i]['m_b'], $element_rows[$i]['m_r'],
          $element_rows[$i]['m_l'], $element_rows[$i]['p_t'],
          $element_rows[$i]['p_b'], $element_rows[$i]['p_r'],
          $element_rows[$i]['p_l'], $element_rows[$i]['visibility'],
          $element_rows[$i]['box_shadow'], $element_rows[$i]['flex_fill'],
          $element_rows[$i]['flex_grow'], $element_rows[$i]['ms_auto'], $element_rows[$i]['flex_order'],
          $element_rows[$i]['vertical_align'], $element_rows[$i]['col_sm'],
          $element_rows[$i]['h'], $element_rows[$i]['display'],
          $element_rows[$i]['text_wrap'], $element_rows[$i]['font_weight'],
          $element_rows[$i]['text_case'], $element_rows[$i]['badge'],
          $element_rows[$i]['float_position'], $element_rows[$i]['text_align'],
          $element_rows[$i]['text_break'], $element_rows[$i]['center_content'],
          $element_rows[$i]['last_update'], $element_rows[$i]['element_id'], $element_rows[$i]['cal_id']
        );
        $bs_element->set_id($element_rows[$i]['id']);
        array_push($bs_element_list, $bs_element);
    }
    return $bs_element_list;
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
    return $this->bootstrap_element_mapper->update_column($column, $value, $id);
  }

  function update_bs_by_elmgroup($bs_class, $bs_value, $data_group){
    return $this->bootstrap_element_mapper->update_bs_elements_by_elm_group_fast($bs_class, $bs_value, $data_group);
  }


  function get_total_bscontainers(){
    return $this->bootstrap_element_mapper->get_total_calendar_bscontainers();
  }

  function get_bselm_id_by_element($element_id){
    // if element not found
    $elm_row = $this->bootstrap_element_mapper->get_bsid_by_element($element_id);
    if (isset($elm_row['id']) && !empty($elm_row['id'])){
      return $elm_row['id'];
    } else {
      return false;
    }
  }

  // used for get bscontainer id by init value
  function get_bscontainerid_where_str($column, $value){
    $data = $this->bootstrap_element_mapper->get_bscontainerid_where_str($column, $value);
    if (isset($data['id']) && !empty($data['id'])){
      return $data['id'];
    } else {
      return false;
    }
  }

  // used for get bscontainer id by init value
  function get_bscontainerid_where_int($column, $value){
    $data = $this->bootstrap_element_mapper->get_bscontainerid_where_int($column, $value);
    if (isset($data['id']) && !empty($data['id'])){
      return $data['id'];
    } else {
      return false;
    }
  }

  // used for get full bscontainer data by str value
  function get_bscontainer_data_where_str($column, $value){
    $element_row = $this->bootstrap_element_mapper->get_bscontainer_data_where_str($column, $value);
    if (!isset($element_row) || empty($element_row)){return array();}
    $bs_element = new BootstrapElement();
    $bs_element->init(
      $element_row['bg'], $element_row['text_color'],
      $element_row['p'], $element_row['m'],
      $element_row['border'], $element_row['border_size'],
      $element_row['border_color'], $element_row['border_round'],
      $element_row['width'], $element_row['height'], $element_row['m_t'],
      $element_row['m_b'], $element_row['m_r'],
      $element_row['m_l'], $element_row['p_t'],
      $element_row['p_b'], $element_row['p_r'],
      $element_row['p_l'], $element_row['visibility'],
      $element_row['box_shadow'], $element_row['flex_fill'],
      $element_row['flex_grow'], $element_row['ms_auto'], $element_row['flex_order'],
      $element_row['vertical_align'], $element_row['col_sm'],
      $element_row['h'], $element_row['display'],
      $element_row['text_wrap'], $element_row['font_weight'],
      $element_row['text_case'], $element_row['badge'],
      $element_row['float_position'], $element_row['text_align'],
      $element_row['text_break'], $element_row['center_content'],
      $container_row['last_update'], $container_row['element_id'], $container_row['cal_id']
    );
    $bs_element->set_id($element_row['id']);
    return $bs_element;
  }

  function get_bscontainer_data_where_init($column, $value){
    $element_row = $this->bootstrap_element_mapper->get_bscontainer_data_where_init($column, $value);
    if (!isset($element_row) || empty($element_row)){return array();}
    $bs_element = new BootstrapElement();
    $bs_element->init(
      $element_row['bg'], $element_row['text_color'],
      $element_row['p'], $element_row['m'],
      $element_row['border'], $element_row['border_size'],
      $element_row['border_color'], $element_row['border_round'],
      $element_row['width'], $element_row['height'], $element_row['m_t'],
      $element_row['m_b'], $element_row['m_r'],
      $element_row['m_l'], $element_row['p_t'],
      $element_row['p_b'], $element_row['p_r'],
      $element_row['p_l'], $element_row['visibility'],
      $element_row['box_shadow'], $element_row['flex_fill'],
      $element_row['flex_grow'], $element_row['ms_auto'], $element_row['flex_order'],
      $element_row['vertical_align'], $element_row['col_sm'],
      $element_row['h'], $element_row['display'],
      $element_row['text_wrap'], $element_row['font_weight'],
      $element_row['text_case'], $element_row['badge'],
      $element_row['float_position'], $element_row['text_align'],
      $element_row['text_break'], $element_row['center_content'],
      $container_row['last_update'], $container_row['element_id'], $container_row['cal_id']

    );
    $bs_element->set_id($element_row['id']);
    return $bs_element;
  }


  function get_bootstrap_classes_by_element($bs_element_id){
    $bootstrap_classes = ' ';
    $element_row = $this->bootstrap_element_mapper->get_bs_by_element($bs_element_id);
    if (!isset($element_row) || empty($element_row)){
      return '';
    }
    // my way to get assoc or index array from the pdo fetch which retun index and assoc (not get empty values)
    $get_assoc_array = array();
    foreach ($element_row as $key => $value) {
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

}
