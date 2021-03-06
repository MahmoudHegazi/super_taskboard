<?php
require_once(dirname(__FILE__, 2) . '/config.php');
require_once(dirname(__FILE__, 2) . '/mappers/ElementMapper.php');
require_once(dirname(__FILE__, 2) . '/models/Element.php');

require_once(dirname(__FILE__, 2) . '/services/CalendarService.php');


class ElementService {
  protected $pdo;
  protected $element_mapper;
  protected $calendar_service;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->element_mapper = new ElementMapper($pdo);
    $this->calendar_service = new CalendarService($pdo);
  }
  // Add New Day
  public function add($element_id, $class_name, $cal_id, $type, $default_bootstrap='', $default_style = '', $group=NULL, $bootstrap_classes='', $innerHTML=NULL, $innerText=NULL, $data=NULL){
    $element_obj = new Element();
    $element_obj->init($element_id, $class_name, $cal_id, $type, $default_bootstrap, $default_style, $group, $bootstrap_classes, $innerHTML, $innerText, $data);
    $lastid = $this->element_mapper->insert($element_obj);
    return $lastid;
  }

  // Remove  day
  public function remove($element_id){
    return $this->element_mapper->delete($element_id)->rowCount() ? 1 : 0;
  }

  public function delete_all_cal_elements($cal_id){
    return $this->element_mapper->delete_all_cal_elements($cal_id)->rowCount() ? 1 : 0;
  }

  // Get element Using it's id
  public function get_element_by_id($element_id){
    $element_row = $this->element_mapper->read_one($element_id);
    // if element not found
    if (!isset($element_row['id']) || empty($element_row['id'])){return array();}
    $element = new Element();
    $element->init(
      $element_row['element_id'],
      $element_row['class_name'],
      $element_row['cal_id'],
      $element_row['type'],
      $element_row['default_bootstrap'],
      $element_row['default_style'],
      $element_row['data_group'],
      $element_row['bootstrap_classes'],
      $element_row['innerHTML'],
      $element_row['innerText'],
      $element_row['data']
    );
    $element->set_id($element_row['id']);
    return $element;
  }

  public function get_elements_by_group($group){
  }

  // get All days
  public function get_all_elements(){

    $elements_list = array();
    $element_rows = $this->element_mapper->read_all();
    if (count($element_rows) == 0){return array();}

    for ($i=0; $i<count($element_rows); $i++){
        $element = new Element();
        $element->init(
          $element_rows[$i]['element_id'],
          $element_rows[$i]['class_name'],
          $element_rows[$i]['cal_id'],
          $element_rows[$i]['type'],
          $element_rows[$i]['default_bootstrap'],
          $element_rows[$i]['default_style'],
          $element_rows[$i]['data_group'],
          $element_rows[$i]['bootstrap_classes'],
          $element_rows[$i]['innerHTML'],
          $element_rows[$i]['innerText'],
          $element_rows[$i]['data']
        );
        $element->set_id($element_rows[$i]['id']);
        array_push($elements_list, $element);
    }
    return $elements_list;
  }


  // services methods
  // services methods



  // add more than one db row
  public function add_elements($element_data_list){
    $element_ids = array();

    if (count($element_data_list) < 1){
      return False;
    }

    $added = 0;

    $element = new Element();
    for ($i=0; $i<count($element_data_list); $i++){
      if (is_array($element_data_list[$i]) && count($element_data_list[$i]) == 11){
         $element->init(
           $element_data_list[$i][0],
           $element_data_list[$i][1],
           $element_data_list[$i][2],
           $element_data_list[$i][3],
           $element_data_list[$i][4],
           $element_data_list[$i][5],
           $element_data_list[$i][6],
           $element_data_list[$i][7],
           $element_data_list[$i][8],
           $element_data_list[$i][9],
           $element_data_list[$i][10]
         );
         $elementid = $this->element_mapper->insert($element);
         array_push($element_ids, $elementid);
         $added += 1;
      }
    }
    return $element_ids;
  }

  public function delete_elements($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  day
  public function update_one_column($column, $value, $id){
    return $this->element_mapper->update_column($column, $value, $id);
  }

  public function update_elements_by_group($column, $value, $data_group){
    return $this->element_mapper->update_columns_by_group($column, $value, $data_group);
  }


  public function get_total_elements(){
    return $this->element_mapper->get_total_elements();
  }
  // load HTML element style
  public function get_element_styles($id){
    $element_styles_row = $this->element_mapper->get_element_styles($id);
    if ($element_styles_row && isset($element_styles_row['default_style']) && !empty($element_styles_row['default_style'])){
      return $element_styles_row['default_style'];
    } else {
      return '';
    }
  }




  public function insert_group_fast($data){
    $elements_objects = array();
    foreach($data as $item)
    {
      $element_object = new Element();
      $element_object->init(
        $item['element_id'],
        $item['class_name'],
        $item['cal_id'],
        $item['type'],
        $item['default_bootstrap'],
        $item['default_style'],
        $item['data_group'],
        $item['bootstrap_classes'],
        $item['innerHTML'],
        $item['innerText'],
        $item['data']
      );
      $elements_objects[] = $element_object;

    }
    return $this->element_mapper->insert_group_fast($elements_objects);
  }

  public function get_elements_ids_where($column, $value, $limit='', $and_column='', $and_val=''){
    $element_rows = $this->element_mapper->get_element_ids_where($column, $value, $limit, $and_column, $and_val);
    if (count($element_rows) == 0){return array();}
    $elements_ids = array();
    for ($i=0; $i<count($element_rows); $i++){
        if (!empty($element_rows[$i]) && isset($element_rows[$i]['id']) && !empty($element_rows[$i]['id'])){
          $elmid = $element_rows[$i]['id'];
          array_push($elements_ids, $elmid);
        }
    }
    return $elements_ids;
  }


  public function get_element_where($column, $value, $limit='', $and_column='', $and_val=''){
    $element_row = $this->element_mapper->get_element_where($column, $value, $limit, $and_column, $and_val);
    if (!isset($element_row['id']) || empty($element_row['id'])){return array();}
    $element = new Element();
    $element->init(
      $element_row['element_id'],
      $element_row['class_name'],
      $element_row['cal_id'],
      $element_row['type'],
      $element_row['default_bootstrap'],
      $element_row['default_style'],
      $element_row['data_group'],
      $element_row['bootstrap_classes'],
      $element_row['innerHTML'],
      $element_row['innerText'],
      $element_row['data']
    );
    $element->set_id($element_row['id']);
    return $element;
  }

  public function getElement($element_id, $type='container'){
    $current_cal = $this->calendar_service->get_used_calendar('used', 1);
    $element_row = $this->element_mapper->get_element($element_id, $type, $current_cal->get_id());
    if (!isset($element_row['id']) || empty($element_row['id'])){return array();}
    $element = new Element();
    $element->init(
      $element_row['element_id'],
      $element_row['class_name'],
      $element_row['cal_id'],
      $element_row['type'],
      $element_row['default_bootstrap'],
      $element_row['default_style'],
      $element_row['data_group'],
      $element_row['bootstrap_classes'],
      $element_row['innerHTML'],
      $element_row['innerText'],
      $element_row['data']
    );
    $element->set_id($element_row['id']);
    return $element;
  }

  public function getElementId($element_id, $type='container'){
    $element_row = $this->element_mapper->get_elementid($element_id, $type);
    if (isset($element_row['id']) && !empty($element_row['id'])){
      return $element_row['id'];
    } else {
      return 0;
    }
  }




  public function read_all_cal_elements($cal_id){
    $elements_list = array();
    $elements_ids = array();
    $element_rows = $this->element_mapper->read_all_cal_elements($cal_id);
    if (count($element_rows) == 0){return array();}
    for ($i=0; $i<count($element_rows); $i++){
        $element = new Element();
        $element->init(
          $element_rows[$i]['element_id'],
          $element_rows[$i]['class_name'],
          $element_rows[$i]['cal_id'],
          $element_rows[$i]['type'],
          $element_rows[$i]['default_bootstrap'],
          $element_rows[$i]['default_style'],
          $element_rows[$i]['data_group'],
          $element_rows[$i]['bootstrap_classes'],
          $element_rows[$i]['innerHTML'],
          $element_rows[$i]['innerText'],
          $element_rows[$i]['data']
        );
        $element->set_id($element_rows[$i]['id']);
        $htmlid = $element_rows[$i]['element_id'];
        if (!isset($elements_list[$htmlid]) && !in_array($htmlid, $elements_ids)){
          $elements_list[$element_rows[$i]['element_id']] = $element;
          array_push($elements_ids, $htmlid);
        }
    }
    return array('elements_objects'=>$elements_list, 'elements_ids'=>$elements_ids);
  }
}





  //global $pdo;
  //$element_service = new ElementService($pdo);
  //$elmid = $element_service->add('new_elm12','new_test2', 290, '', '', NULL, '', NULL, NULL, NULL);
  //$removed = $element_service->remove($elmid);
  /*
  $elm = $element_service->read_all_cal_elements(290);
  echo "<pre>";
  print_r($elm);
  echo "</pre>";
  */
  //echo $removed;

/* ##################### Test #################### */
/* #####################
global $pdo;
$dayService = new DayService($pdo);
$dayService->add(1, 'Monday', '2020-01-11', 1);
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
