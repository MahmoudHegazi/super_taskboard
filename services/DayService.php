<?php
require_once('../config.php');
require_once('../mappers/DayMapper.php');
require_once('../models/Day.php');

class DayService {
  protected $pdo;
  protected $day_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->day_mapper = new DayMapper($pdo);
  }
  // Add New Day
  function add($day, $day_name, $day_date, $month_id){

    $day_obj = new Day();
    $day_obj->init($day, $day_name, $day_date, $month_id);
    return $this->day_mapper->insert($day_obj);
  }

  // Remove  day
  function remove($day_id){
    return $this->day_mapper->delete($day_id);
  }

  // Get day Using it's id
  function get_day_by_id($day_id){
    $day_row = $this->day_mapper->read_one($day_id);
    // if element not found
    if (!isset($day_row['id']) || empty($day_row['id'])){return array();}
    $day = new Day();
    $day->init(
      $day_row['day'],
      $day_row['day_name'],
      $day_row['day_date'],
      $day_row['month_id']
    );
    $day->set_id($day_row['id']);
    return $day;
  }

  // get All days
  function get_all_days(){

    $days_list = [];
    $day_rows = $this->day_mapper->read_all();
    if (count($day_rows) == 0){return array();}

    for ($i=0; $i<count($day_rows); $i++){
        $day = new Day();
        $day->init(
          $day_rows[$i]['day'],
          $day_rows[$i]['day_name'],
          $day_rows[$i]['day_date'],
          $day_rows[$i]['month_id']
        );
        $day->set_id($day_rows[$i]['id']);
        array_push($days_list, $day);
    }
    return $days_list;
  }


  // services methods
  function get_days_by_id($list_of_ids){
    $day_rows = $this->get_all_days();

    $result = array();
    for ($i=0; $i<count($day_rows); $i++){
      if (in_array($day_rows[$i]->id, $list_of_ids)){
        array_push($result, $day_rows[$i]);
      }
    }
    return $result;

  }


  // add more than one db row
  function add_days($day_data_list){
    $day_ids = array();

    if (count($day_data_list) < 1){
      return False;
    }

    $added = 0;

    $day = new Day();

    for ($i=0; $i<count($day_data_list); $i++){
      if (is_array($day_data_list[$i]) && count($day_data_list[$i]) == 4){

         $day->init(
           $day_data_list[$i][0],
           $day_data_list[$i][1],
           $day_data_list[$i][2],
           $day_data_list[$i][3]
         );
         $dayid = $this->day_mapper->insert($day);
         array_push($day_ids, $dayid);
         $added += 1;
      }
    }
    return $day_ids;
  }

  function delete_days($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
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
