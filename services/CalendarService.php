<?php
require_once('../config.php');
require_once('../mappers/CalendarMapper.php');
require_once('../models/Calendar.php');

class CalendarService {
  protected $pdo;
  protected $calendar_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->calendar_mapper = new CalendarMapper($pdo);
  }
  // Add New Calendar
  function add($title, $start_year, $added_years, $periods_per_day, $slots_per_period){

    $calendar = new Calendar();
    $calendar->init($title, $start_year, $added_years, $periods_per_day, $slots_per_period);
    return $this->calendar_mapper->insert($calendar);
  }

  // Remove  Calendar
  function remove($calendar_id){
    return $this->calendar_mapper->delete($calendar_id);
  }

  // Get Calendar Using it's id
  function get_calendar_by_id($calendar_id){
    $calendar_row = $this->calendar_mapper->read_one($calendar_id);
    if (!isset($calendar_row['id']) || empty($calendar_row['id'])){return array();}
    $calendar = new Calendar();
    $calendar->init($calendar_row['title'], $calendar_row['start_year'], $calendar_row['added_years'], $calendar_row['periods_per_day'], $calendar_row['slots_per_period']);
    $calendar->set_id($calendar_row['id']);
    return $calendar;
  }

  // get All calendars
  function get_all_calendars(){

    $calendars_list = [];
    $calendar_rows = $this->calendar_mapper->read_all();

    for ($i=0; $i<count($calendar_rows); $i++){
      $calendar = new Calendar();
      $calendar->init(
        $calendar_rows[$i]['title'],
        $calendar_rows[$i]['start_year'],
        $calendar_rows[$i]['added_years'],
        $calendar_rows[$i]['periods_per_day'],
        $calendar_rows[$i]['slots_per_period']
      );
      $calendar->set_id($calendar_rows[$i]['id']);
      array_push($calendars_list, $calendar);
    }
    return $calendars_list;
  }


  // services methods
  function get_calendars_by_id($list_of_ids){
    // better performance instead of loop over id
    //and run alot query only 1 query and use this return calendar objects
    $calendar_rows = $this->get_all_calendars();

    $result = array();
    for ($i=0; $i<count($calendar_rows); $i++){
      if (in_array($calendar_rows[$i]->id, $list_of_ids)){
        array_push($result, $calendar_rows[$i]);
      }
    }

    return $result;

  }
  // add more than one db row
  function add_calendars($cal_data_list){

    $calendars_ids = array();
    if (count($cal_data_list) < 1){
      return False;
    }

    $added = 0;

    $calendar = new Calendar();
    for ($i=0; $i<count($cal_data_list); $i++){

      if (is_array($cal_data_list[$i]) && count($cal_data_list[$i]) == 3){
         $calendar->init(
           $cal_data_list[$i][0],
           $cal_data_list[$i][1],
           $cal_data_list[$i][2],
           $cal_data_list[$i][3],
           $cal_data_list[$i][4]
         );
         $cal_id = $this->calendar_mapper->insert($calendar);
         array_push($calendars_ids, $cal_id);
         $added += 1;
      }
    }
    return $calendars_ids;
  }

  function delete_calendars($list_of_ids){
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
$calendarService = new CalendarService($pdo);

echo "<pre>";
$calendarService->add('test calendar', '1998', 10, 3, 3);
$calendarService->add('test calendar', '1998', 10, 3, 3);
$calendarService->remove(3);

$calendarService->add_calendars(array(array('test calendar', '1998', 10, 3, 3), array('Super calendar', '2010', 10, 3, 3)));
$calendarService->add_calendars(array(array('test calendar', '1998', 10, 3, 3), array('Super calendar', '2010', 10, 3, 3)));

echo "<h1>get_calendar_by_id</h1>";
print_r($calendarService->get_calendar_by_id(1));
echo "<br /><br /><hr />";

echo "<h1>get_all_calendars</h1>";
echo count($calendarService->get_all_calendars());
echo "<br /><br /><hr />";

echo "<h1>get_calendars_by_id</h1>";
print_r($calendarService->get_calendars_by_id(array(1,2,3)));
echo "<br /><br /><hr />";

echo "<h1>delete_calendars</h1>";
$calendarService->delete_calendars(array(4,5,6));
echo "<br /><br /><hr />";


echo "</pre>";
*/
