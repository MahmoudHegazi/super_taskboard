<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\mappers\PeriodMapper.php');
require_once(dirname(__FILE__, 2) . '\models\Period.php');


class PeriodService {
  protected $pdo;
  protected $period_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->period_mapper = new PeriodMapper($pdo);
  }


  // Add New Period
  function add($day_id, $period_date, $description, $period_index, $element_id='', $element_class=''){
    $period_obj = new Period();
    $period_obj->init($day_id, $period_date, $description, $period_index);
    $period_obj->set_element_id($element_id);
    $period_obj->set_element_class($element_class);
    return $this->period_mapper->insert($period_obj);
  }

  // Remove  period
  function remove($period_id){
    return $this->period_mapper->delete($period_id);
  }

  // Get period Using it's id
  function get_period_by_id($period_id){

    $period_row = $this->period_mapper->read_one($period_id);
    // if element not found
    if (!isset($period_row['id']) || empty($period_row['id'])){return array();}
    $period = new Period();
    $period->init(
      $period_row['day_id'],
      $period_row['period_date'],
      $period_row['description'],
      $period_row['period_index']
    );
    $period->set_id($period_row['id']);
    $period->set_element_id($period_row['element_id']);
    $period->set_element_class($period_row['element_class']);
    return $period;
  }
  // get All period
  function get_all_periods(){

    $period_list = array();
    $period_rows = $this->period_mapper->read_all();
    if (count($period_rows) == 0){return array();}

    for ($i=0; $i<count($period_rows); $i++){
        $period = new Period();
        $period->init(
          $period_rows[$i]['day_id'],
          $period_rows[$i]['period_date'],
          $period_rows[$i]['description'],
          $period_rows[$i]['period_index']
        );
        $period->set_id($period_rows[$i]['id']);
        $period->set_element_id($period_rows[$i]['element_id']);
        $period->set_element_class($period_rows[$i]['element_class']);
        array_push($period_list, $period);
    }
    return $period_list;
  }

  // services methods
  function get_periods_by_id($list_of_ids){

    $period_rows = $this->get_all_periods();


    $result = array();
    for ($i=0; $i<count($period_rows); $i++){
      if (in_array($period_rows[$i]->id, $list_of_ids)){
        array_push($result, $period_rows[$i]);
      }
    }
    return $result;

  }


  // add more than one db row
  function add_periods($period_data_list){
    $periods_ids = array();

    if (count($period_data_list) < 1){
      return False;
    }

    $period = new Period();

    for ($i=0; $i<count($period_data_list); $i++){
      if (is_array($period_data_list[$i]) && count($period_data_list[$i]) == 6){

         $period->init(
           $period_data_list[$i][0],
           $period_data_list[$i][1],
           $period_data_list[$i][2],
           $period_data_list[$i][3],
           $period_data_list[$i][4],
           $period_data_list[$i][5]
         );
         $period_id = $this->period_mapper->insert($period);
         array_push($periods_ids, $period_id);
      }
    }
    return $periods_ids;
  }

  function delete_periods($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  period
  function update_one_column($column, $value, $id){
    return $this->period_mapper->update_column($column, $value, $id);
  }

  function get_total_periods(){
    return $this->period_mapper->get_total_periods();
  }

  function get_periods_where($column, $value, $limit=''){
    return $this->period_mapper->get_periods_where($column, $value, $limit);
  }

  function update_periods_where($column, $where, $value){
    return $this->period_mapper->get_periods_where($column, $where, $value);
  }

  function get_distinct_periods($cal_id){
    return $this->period_mapper->get_distinct_periods($cal_id);
  }

  function get_distinct_periods_data($periods_data_rows){
    if (!isset($periods_data_rows) || empty($periods_data_rows)){return array();}
    $periods_data = array();
    for ($s=0; $s<count($periods_data_rows); $s++){
      $row_data = $this->get_periods_where('period_index', intval($periods_data_rows[$s]['period_index']), 1);
      if (count($row_data) > 0){
        array_push($periods_data,array(
          'id'=> $row_data[0]['id'],
          'period_index'=> $row_data[0]['period_index'],
          'period_date'=> $row_data[0]['period_date'],
          'description'=> $row_data[0]['description'],
          'element_id'=> $row_data[0]['element_id'],
          'element_class'=> $row_data[0]['element_class']
        )
        );
      }
    }
    return $periods_data;
  }


  function get_distinct_periods_classnames($periods_data_rows){
    if (!isset($periods_data_rows) || empty($periods_data_rows)){return array();}
    $periods_data = array();
    for ($s=0; $s<count($periods_data_rows); $s++){
      $row_data = $this->get_periods_where('period_index', intval($periods_data_rows[$s]['period_index']), 1);
      if (count($row_data) > 0){
        array_push($periods_data,array(
          'element_class'=> $row_data[0]['element_class']
        )
        );
      }
    }
    return $periods_data;
  }


  function insert_group_fast($data){
    $periods_objects = array();
    foreach($data as $item)
    {
      $period_obj = new Period();
      $period_obj->init(
        $item['day_id'],
        $item['period_date'],
        $item['description'],
        $item['period_index']
      );
      $period_obj->set_element_id($item['element_id']);
      $period_obj->set_element_class($item['element_class']);
      $periods_objects[] = $period_obj;

    }

    return $this->period_mapper->insert_group_fast($periods_objects);
  }
}
/* ##################### Test #################### */
/* #####################

global $pdo;
$periodService = new PeriodService($pdo);



echo "<pre>";
$periodService->add(1, '2020-10-12', 'hello world mvc');
$periodService->add(2, '2020-10-11', 'hello world mvc');
$periodService->remove(2);

$periodService->add_periods(array(array(3, '2010-10-11', 'hello world mvc111'),array(3, '2010-10-11', 'hello world mvc11')));

print_r($periodService->get_period_by_id(1));
echo "<h1>get_period_by_id</h1><br /><br /><hr />";
echo count($periodService->get_all_periods());
echo "<h1>get_all_periods</h1><br /><br /><hr />";

print_r($periodService->get_periods_by_id(array(1,2,3)));
echo "<h1>get_periods_by_id</h1><br /><br /><hr />";

$periodService->delete_periods(array(1,6,10));
echo "<h1>delete_periods</h1><br /><br /><hr />";

echo "</pre>";
*/
