<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\mappers\MonthMapper.php');
require_once(dirname(__FILE__, 2) . '\models\Month.php');


class MonthService {
  protected $pdo;
  protected $month_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->month_mapper = new MonthMapper($pdo);
  }
  // Add New Month
  function add($month, $year_id){
    $month_obj = new Month();
    $month_obj->init($month, $year_id);
    return $this->month_mapper->insert($month_obj);
  }

  // Remove  month
  function remove($month_id){
    return $this->month_mapper->delete($month_id);
  }

  // Get month Using it's id
  function get_month_by_id($month_id){

    $month_row = $this->month_mapper->read_one($month_id);
    // if element not found
    if (!isset($month_row['id']) || empty($month_row['id'])){return array();}
    $month = new Month();
    $month->init(
      $month_row['month'],
      $month_row['year_id']
    );
    $month->set_id($month_row['id']);
    return $month;
  }

  // get All months
  function get_all_months(){

    $months_list = array();
    $months_rows = $this->month_mapper->read_all();
    if (count($months_rows) == 0){return array();}

    for ($i=0; $i<count($months_rows); $i++){
        $month = new Month();
        $month->init(
          $months_rows[$i]['month'],
          $months_rows[$i]['year_id']
        );
        $month->set_id($months_rows[$i]['id']);
        array_push($months_list, $month);
    }
    return $months_list;
  }

  // services methods
  function get_months_by_id($list_of_ids){

    $month_rows = $this->get_all_months();


    $result = array();
    for ($i=0; $i<count($month_rows); $i++){
      if (in_array($month_rows[$i]->id, $list_of_ids)){
        array_push($result, $month_rows[$i]);
      }
    }
    return $result;

  }


  // add more than one db row
  function add_months($month_data_list){
    $months_ids = array();
    if (count($month_data_list) < 1){
      return False;
    }

    $month = new Month();
    for ($i=0; $i<count($month_data_list); $i++){

      if (is_array($month_data_list[$i]) && count($month_data_list[$i]) == 2){

         $month->init(
           $month_data_list[$i][0],
           $month_data_list[$i][1]
         );
         $month_id = $this->month_mapper->insert($month);
         array_push($months_ids, $month_id);
      }
    }
    return $months_ids;
  }

  function delete_months($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  month
  function update_one_column($column, $value, $id){
    return $this->month_mapper->update_column($column, $value, $id);
  }

  function get_total_months(){
    return $this->month_mapper->get_total_months();
  }


}


/* ##################### Test #################### */
/* #####################

global $pdo;
$monthService = new MonthService($pdo);


echo "<pre>";
$monthService->add(1, 2);
$monthService->remove(1);

$monthService->add_months(array(array(2,2),array(3,2)));

print_r($monthService->get_month_by_id(2));
echo "<h1>get_month_by_id</h1><br /><br /><hr />";
echo count($monthService->get_all_months());
echo "<h1>get_all_months</h1><br /><br /><hr />";

print_r($monthService->get_months_by_id(array(4,17,10)));
echo "<h1>get_months_by_id</h1><br /><br /><hr />";

$monthService->delete_months(array(1,6,10));
echo "<h1>delete_months</h1><br /><br /><hr />";

echo "</pre>";
*/
