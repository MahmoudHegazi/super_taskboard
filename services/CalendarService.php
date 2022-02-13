<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\mappers\CalendarMapper.php');
require_once(dirname(__FILE__, 2) . '\models\Calendar.php');

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
  function add($title, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $used=0){
    $calendar = new Calendar();
    $calendar->init($title, $start_year, $added_years, $periods_per_day, $slots_per_period, $description);
    $calendar->set_used($used);
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
    $calendar->init($calendar_row['title'], $calendar_row['start_year'], $calendar_row['added_years'], $calendar_row['periods_per_day'], $calendar_row['slots_per_period'], $calendar_row['description']);
    $calendar->set_id($calendar_row['id']);
    $calendar->set_used($calendar_row['used']);
    return $calendar;
  }

  // get All calendars
  function get_all_calendars($limit=0, $offset=0){

    $calendars_list = array();
    $calendar_rows = $this->calendar_mapper->read_all($limit, $offset);

    for ($i=0; $i<count($calendar_rows); $i++){
      $calendar = new Calendar();
      $calendar->init(
        $calendar_rows[$i]['title'],
        $calendar_rows[$i]['start_year'],
        $calendar_rows[$i]['added_years'],
        $calendar_rows[$i]['periods_per_day'],
        $calendar_rows[$i]['slots_per_period'],
        $calendar_rows[$i]['description']
      );
      $calendar->set_id($calendar_rows[$i]['id']);
      $calendar->set_background_image($calendar_rows[$i]['background_image']);
      $calendar->set_used($calendar_rows[$i]['used']);
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

      if (is_array($cal_data_list[$i]) && count($cal_data_list[$i]) == 6){
         $calendar->init(
           $cal_data_list[$i][0],
           $cal_data_list[$i][1],
           $cal_data_list[$i][2],
           $cal_data_list[$i][3],
           $cal_data_list[$i][4],
           $cal_data_list[$i][5]
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

  // update signle column  Calendar
  function update_one_column($column, $value, $id){
    return $this->calendar_mapper->update_column($column, $value, $id);
  }


  function get_total_calendars(){
    return $this->calendar_mapper->get_total_calendars();
  }

  function update_columns_where($column, $value, $new_value){
    return $this->calendar_mapper->upadate_where($column, $value, $new_value);
  }

  function free_group_query($sql){
    return $this->calendar_mapper->free_group_query($sql);
  }
  function free_single_query($sql){
    return $this->calendar_mapper->free_single_query($sql);
  }


  function excute_on_db($sql){
    return $this->calendar_mapper->free_db_command($sql);
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
