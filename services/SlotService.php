<?php
require_once('../config.php');
require_once('../mappers/SlotMapper.php');
require_once('../models/Slot.php');

class SlotService {
  protected $pdo;
  protected $slot_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->slot_mapper = new SlotMapper($pdo);
  }
  // Add New slot
  function add($start_from, $end_at, $period_id, $empty){
    $slot_obj = new Slot();
    $slot_obj->init($start_from, $end_at, $period_id, $empty);
    return $this->slot_mapper->insert($slot_obj);
  }

  // Remove  slot
  function remove($slot_id){
    return $this->slot_mapper->delete($slot_id);
  }

  // Get slot Using it's id
  function get_slot_by_id($slot_id){

    $slot_row = $this->slot_mapper->read_one($slot_id);
    // if element not found
    if (!isset($slot_row['id']) || empty($slot_row['id'])){return array();}
    $slot = new Slot();
    $slot->init(
      $slot_row['start_from'],
      $slot_row['end_at'],
      $slot_row['period_id'],
      $slot_row['empty']
    );
    $slot->set_id($slot_row['id']);
    return $slot;
  }

  // get All slot
  function get_all_slots(){

    $slot_list = [];
    $slot_rows = $this->slot_mapper->read_all();
    if (count($slot_rows) == 0){return array();}

    for ($i=0; $i<count($slot_rows); $i++){
        $slot = new Slot();
        $slot->init(
          $slot_rows[$i]['start_from'],
          $slot_rows[$i]['end_at'],
          $slot_rows[$i]['period_id'],
          $slot_rows[$i]['empty']
        );
        $slot->set_id($slot_rows[$i]['id']);
        array_push($slot_list, $slot);
    }
    return $slot_list;
  }

  // services methods
  function get_slots_by_id($list_of_ids){

    $slot_rows = $this->get_all_slots();


    $result = array();
    for ($i=0; $i<count($slot_rows); $i++){
      if (in_array($slot_rows[$i]->id, $list_of_ids)){
        array_push($result, $slot_rows[$i]);
      }
    }
    return $result;

  }


  // add more than one db row
  function add_slots($slot_data_list){
    $slots_ids = array();
    if (count($slot_data_list) < 1){
      return False;
    }

    $slot = new Slot();

    for ($i=0; $i<count($slot_data_list); $i++){
      if (is_array($slot_data_list[$i]) && count($slot_data_list[$i]) == 4){

         $slot->init(
           $slot_data_list[$i][0],
           $slot_data_list[$i][1],
           $slot_data_list[$i][2],
           $slot_data_list[$i][3]
         );
         $slot_id = $this->slot_mapper->insert($slot);
         array($slots_ids, $slot_id);
      }
    }
    return $slots_ids;
  }

  function delete_slots($list_of_ids){
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
$slotService = new SlotService($pdo);


echo "<pre>";
$slotService->add('10:22', '12:22',1 ,1);
$slotService->remove(1);

$slotService->add_slots(array(array('09:30', '11:30',2 ,1),array('05:00', '06:30',2 ,0)));

print_r($slotService->get_slot_by_id(2));
echo "<h1>get_slot_by_id</h1><br /><br /><hr />";
echo count($slotService->get_all_slots());
echo "<h1>get_all_slots</h1><br /><br /><hr />";

print_r($slotService->get_slots_by_id(array(4,17,10)));
echo "<h1>get_slots_by_id</h1><br /><br /><hr />";

$slotService->delete_slots(array(1,6,10));
echo "<h1>delete_slots</h1><br /><br /><hr />";

echo "</pre>";
*/
