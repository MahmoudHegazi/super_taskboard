<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\mappers\YearMapper.php');
require_once(dirname(__FILE__, 2) . '\models\Year.php');

class YearService {
  protected $pdo;
  protected $year_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->year_mapper = new YearMapper($pdo);
  }
  // Add New year
  function add($year, $cal_id){
    $year_obj = new Year();
    $year_obj->init($year, $cal_id);
    return $this->year_mapper->insert($year_obj);
  }

  // Remove  year
  function remove($year_id){
    return $this->year_mapper->delete($year_id);
  }

  // Get year Using it's id
  function get_year_by_id($year_id){

    $year_row = $this->year_mapper->read_one($year_id);
    // if element not found
    if (!isset($year_row['id']) || empty($year_row['id'])){return array();}
    $year = new Year();
    $year->init(
      $year_row['year'],
      $year_row['cal_id']
    );
    $year->set_id($year_row['id']);
    return $year;
  }

  function get_year_object($year_row){
    if (!isset($year_row['id']) || empty($year_row['id'])){return array();}
    $year = new Year();
    $year->init(
      $year_row['year'],
      $year_row['cal_id']
    );
    $year->set_id($year_row['id']);
    return $year;
  }
  // get All year
  function get_all_years(){

    $year_list = array();
    $year_rows = $this->year_mapper->read_all();
    if (count($year_rows) == 0){return array();}

    for ($i=0; $i<count($year_rows); $i++){
        $year = new Year();
        $year->init(
          $year_rows[$i]['year'],
          $year_rows[$i]['cal_id']
        );
        $year->set_id($year_rows[$i]['id']);
        array_push($year_list, $year);
    }
    return $year_list;
  }

  // get All year
  function get_all_years_where($column, $value, $limit=''){
    $limit = $limit && $limit != '' && is_numeric($limit) ? ' ORDER BY year LIMIT ' . $limit : ' ORDER BY year';
    $year_list = array();
    $year_rows = $this->year_mapper->read_all_where($column, $value, $limit);
    if (count($year_rows) == 0){return array();}
    for ($i=0; $i<count($year_rows); $i++){
        $year = new Year();
        $year->init(
          $year_rows[$i]['year'],
          $year_rows[$i]['cal_id']
        );
        $year->set_id($year_rows[$i]['id']);
        array_push($year_list, $year);
    }
    return $year_list;
  }


  // services methods
  function get_years_by_id($list_of_ids){

    $year_rows = $this->get_all_years();


    $result = array();
    for ($i=0; $i<count($year_rows); $i++){
      if (in_array($year_rows[$i]->id, $list_of_ids)){
        array_push($result, $year_rows[$i]);
      }
    }
    return $result;

  }


  // add more than one db row
  function add_years($year_data_list){
    $years_ids = array();
    if (count($year_data_list) < 1){
      return False;
    }

    $year = new Year();

    for ($i=0; $i<count($year_data_list); $i++){
      if (is_array($year_data_list[$i]) && count($year_data_list[$i]) == 2){

         $year->init(
           $year_data_list[$i][0],
           $year_data_list[$i][1]
         );
         $year_id = $this->year_mapper->insert($year);
         array($years_ids, $year_id);
      }
    }
    return $years_ids;
  }

  function delete_years($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  year
  function update_one_column($column, $value, $id){
    return $this->year_mapper->update_column($column, $value, $id);
  }

  function get_total_years(){
    return $this->year_mapper->get_total_years();
  }



  function insert_group_fast($data){
    $years_objects = array();
    foreach($data as $item)
    {
      $year_obj = new Year();
      $year_obj->init(
        $item['year'],
        $item['cal_id']
      );
      $years_objects[] = $year_obj;

    }

    return $this->year_mapper->insert_group_fast($years_objects);
  }

  // it will return single object if limit 1 else array
  function get_years_where($column, $value, $limit='', $and_column='', $and_val=''){
    $year_list = array();
    $year_rows = $this->year_mapper->get_years_where($column, $value, $limit, $and_column, $and_val);
    if (count($year_rows) == 0){return array();}
    for ($i=0; $i<count($year_rows); $i++){
        $year = new Year();
        $year->init(
          $year_rows[$i]['year'],
          $year_rows[$i]['cal_id']
        );
        $year->set_id($year_rows[$i]['id']);
        if (count($year_rows) == 1){
          return $year;
          break;
        } else {
          array_push($year_list, $year);
        }
    }
    return $year_list;
  }


  public function get_min_year(){
    return $this->year_mapper->get_min_year();
  }

  public function get_max_year(){
    return $this->year_mapper->get_max_year();
  }
}


/* ##################### Test #################### */
/* #####################
global $pdo;
$yearService = new YearService($pdo);


echo "<pre>";
$yearService->add(2010, 1);
$yearService->remove(1);

$yearService->add_years(array(array(2011, 1),array(2012, 1)));

print_r($yearService->get_year_by_id(2));
echo "<h1>get_year_by_id</h1><br /><br /><hr />";
echo count($yearService->get_all_years());
echo "<h1>get_all_years</h1><br /><br /><hr />";

print_r($yearService->get_years_by_id(array(4,17,10)));
echo "<h1>get_years_by_id</h1><br /><br /><hr />";

$yearService->delete_years(array(1,6,10));
echo "<h1>delete_years</h1><br /><br /><hr />";

echo "</pre>";
*/
