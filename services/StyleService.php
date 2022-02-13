<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\mappers\StyleMapper.php');
require_once(dirname(__FILE__, 2) . '\models\Style.php');


class StyleService {
  protected $pdo;
  protected $style_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->style_mapper = new StyleMapper($pdo);
  }
  // Add New Style
  function add($classname, $element_id, $style, $class_id, $active, $title, $custom, $cal_id, $category){

    $style_obj = new Style();
    $style_obj->init($classname, $element_id, $style, $class_id, $active, $title, $custom, $cal_id, $category);
    return $this->style_mapper->insert($style_obj);
  }

  // Remove  Style
  function remove($style_id){
    return $this->style_mapper->delete($style_id);
  }

  // Get style Using it's id
  function get_style_by_id($style_id){
    $style_row = $this->style_mapper->read_one($style_id);
    // if element not found
    if (!isset($style_row['id']) || empty($style_row['id'])){return array();}
    $style = new Style();
    $style->init(
      $style_row['classname'],
      $style_row['element_id'],
      $style_row['style'],
      $style_row['class_id'],
      $style_row['active'],
      $style_row['title'],
      $style_row['custom'],
      $style_row['cal_id'],
      $style_row['category']

    );
    $style->set_id($style_row['id']);
    return $style;
  }

  // get All styles
  function get_all_styles(){

    $styles_list = array();
    $style_rows = $this->style_mapper->read_all();
    if (count($style_rows) == 0){return array();}

    for ($i=0; $i<count($style_rows); $i++){
        $style = new Style();
        $style->init(
          $style_rows[$i]['classname'],
          $style_rows[$i]['element_id'],
          $style_rows[$i]['style'],
          $style_rows[$i]['class_id'],
          $style_rows[$i]['active'],
          $style_rows[$i]['title'],
          $style_rows[$i]['custom'],
          $style_rows[$i]['cal_id'],
          $style_rows[$i]['category']

        );
        $style->set_id($style_rows[$i]['id']);
        array_push($styles_list, $style);
    }
    return $styles_list;
  }


  // services methods
  function get_styles_by_id($list_of_styles){
    $style_rows = $this->get_all_styles();

    $result = array();
    for ($i=0; $i<count($style_rows); $i++){
      if (in_array($style_rows[$i]->id, $list_of_ids)){
        array_push($result, $style_rows[$i]);
      }
    }
    return $result;

  }


  // add more than one db row
  function add_styles($style_data_list){
    $style_ids = array();

    if (count($style_data_list) < 1){
      return False;
    }

    $added = 0;

    $style = new Style();

    for ($i=0; $i<count($style_data_list); $i++){
      if (is_array($style_data_list[$i]) && count($style_data_list[$i]) == 9){

         $style->init(
           $style_data_list[$i][0],
           $style_data_list[$i][1],
           $style_data_list[$i][2],
           $style_data_list[$i][3],
           $style_data_list[$i][4],
           $style_data_list[$i][5],
           $style_data_list[$i][6],
           $style_data_list[$i][7],
           $style_data_list[$i][8]
         );
         $styleid = $this->style_mapper->insert($style);
         array_push($style_ids, $styleid);
         $added += 1;
      }
    }
    return $style_ids;
  }

  function delete_styles($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  style
  function update_one_column($column, $value, $id){
    return $this->style_mapper->update_column($column, $value, $id);
  }

  function get_total_styles(){
    return $this->style_mapper->get_total_styles();
  }

  function is_valid_css($str){
    $check_end = substr($str,-1) == ';';
    $check_sprator =  preg_match_all("/:/i", $str) == 1;
    $check_sprator1 =  preg_match_all("/;/i", $str) == 1;
    if ( $check_end && $check_sprator && $check_sprator1){
      return true;
    } else {
      return false;
    }
  }

  function check_css_block($str){
    $check_end = substr($str,-1) == ';';
    $check_sprator =  preg_match_all("/:/i", $str) == 1;
    $check_sprator1 =  preg_match_all("/;/i", $str) == 1;
    if ( $check_end && $check_sprator && $check_sprator1){
      return $str;
    } else {
      return false;
    }
  }
  function check_css_block_advanced($css_str){

    $result = array();
    $explode_rules = explode(";", $css_str);

    for ($index_rule=0; $index_rule < count($explode_rules); $index_rule++){
      $colon_num =  preg_match_all("/:/i", $explode_rules[$index_rule]) == 1;
      $check_end = substr($explode_rules[$index_rule],-1) != ':';
      if (!$colon_num || !$check_end){
        continue;
      } else {
        if (str_replace(" ","",$explode_rules[$index_rule]) == ''){
          continue;
        }
        array_push($result, $explode_rules[$index_rule].';');
      }
    }
    return implode("", $result);
  }

  function formatsignle_css($rule_str){
    $explode_rules = explode("|", $rule_str);
    return implode("", $explode_rules);
  }
  function mymap($function_name, $data){
    $allowed_functions = array('check_css_block', 'check_css_block_advanced');
    if (!in_array($function_name, $allowed_functions)){
      // secuirty dissalow unkown functions
      return false;
    }
    $result = array();
    $current_methods = get_class_methods($this);
    if (!in_array($function_name, $current_methods)){
      return false;
    }
    for ($i=0; $i<count($data); $i++){
      $function_result = $this->{$function_name}($data[$i]);
      if (isset($function_result) && !empty($function_result) && $function_result && $function_result != ''){
        array_push($result, $function_result);
      }
    }
    return $result;
  }

  //$data = array(array(),array($var1, $var2, $var9));
  //$function_result = $this->{$function_name}(...$data[$i]);
  function get_advanced_style_data($data)

  {
    return $this->mymap('check_css_block_advanced', $data);
  }

  function get_style_where($column, $value, $limit=''){
    return $this->style_mapper->get_styles_where($column, $value, $limit);
  }

  function get_all_styles_byclassname($periods_classnames){
    if (!isset($periods_classnames) || empty($periods_classnames)){return array();}
    $periods_data = array();
    for ($s=0; $s<count($periods_classnames); $s++){
      $row_data = $this->get_style_where('classname', $periods_classnames[$s]['element_class']);
      array_push($periods_data, $row_data[$s]);
    }
    return $periods_data;
  }



  function insert_group_fast($data){
    $style_objects = array();
    foreach($data as $item)
    {

      $style_obj = new Style();
      $style_obj->init(
        $item['element_class'],
        $item['element_id'],
        $item['style'],
        $item['class_id'],
        $item['active'],
        $item['title'],
        $item['custom'],
        $item['cal_id'],
        $item['category']
      );
      $style_objects[] = $style_obj;
    }
    return $this->style_mapper->insert_group_fast($style_objects);
  }

}

/* ##################### Test #################### */
/* #####################
global $pdo;
$dayService = new DayService($pdo);

echo "<pre>";

$dayService->add(1, 'Monday', '2020-01-11', 1);
$dayService->add(2, 'Monday', '2020-01-12', 1);
$dayService->remove(2);

$dayService->add_days(array(array('1', 'Monday', '2020-01-11', 1), array('1', 'Monday', '2020-01-11', 1), array('1', 'Monday', '2020-01-11', 1)));

echo "<h1>get_day_by_id</h1>";
print_r($dayService->get_day_by_id(9));
echo "<br /><br /><hr />";

echo "<h1>get_all_days</h1>";
echo count($dayService->get_all_days());
echo "<br /><br /><hr />";

echo "<h1>get_days_by_id</h1>";
print_r($dayService->get_days_by_id(array(9,23,11)));
echo "<br /><br /><hr />";


$dayService->delete_days(array(8,22,10));
echo "<h1>delete_days</h1><br /><br /><hr />";


echo "</pre>";
*/
