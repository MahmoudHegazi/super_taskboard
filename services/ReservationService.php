<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\mappers\ReservationMapper.php');
require_once(dirname(__FILE__, 2) . '\models\Reservation.php');


class ReservationService {
  protected $pdo;
  protected $reservation_mapper;

  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->reservation_mapper = new ReservationMapper($pdo);
  }
  // Add New reservation
  function add($slot_id, $name, $notes, $user_id){
    $reservation_obj = new Reservation();
    $reservation_obj->init($name, $notes, $slot_id, $user_id);
    return $this->reservation_mapper->insert($reservation_obj);
  }

  // Remove  reservation
  function remove($reservation_id){
    return $this->reservation_mapper->delete($reservation_id);
  }

  function get_reservation_by_slot($slot_id){
    return $this->reservation_mapper->get_reservation_by_slot($slot_id);
  }


  // Get reservation Using it's id
  function get_reservation_by_id($reservation_id){

    $reservation_row = $this->reservation_mapper->read_one($reservation_id);
    // if element not found
    if (!isset($reservation_row['id']) || empty($reservation_row['id'])){return array();}
    $reservation = new Reservation();
    $reservation->init(
      $reservation_row['name'],
      $reservation_row['notes'],
      $reservation_row['slot_id'],
      $reservation_row['user_id']
    );
    $reservation->set_id($reservation_row['id']);
    $reservation->set_reservation_date($reservation_row['reservation_date']);
    return $reservation;
  }


  function get_reservation_data_byslot($slot_id){

    $reservation_row = $this->reservation_mapper->get_reservation_by_slot($slot_id);
    // if element not found
    if (!isset($reservation_row['id']) || empty($reservation_row['id'])){return array();}
    $reservation = new Reservation();
    $reservation->init(
      $reservation_row['name'],
      $reservation_row['notes'],
      $reservation_row['slot_id'],
      $reservation_row['user_id']
    );
    $reservation->set_id($reservation_row['id']);
    $reservation->set_reservation_date($reservation_row['reservation_date']);
    return $reservation;
  }

  // get All reservation
  function get_all_reservations(){

    $reservation_list = array();
    $reservation_rows = $this->reservation_mapper->read_all();
    if (count($reservation_rows) == 0){return array();}

    for ($i=0; $i<count($reservation_rows); $i++){
        $reservation = new Reservation();
        $reservation->init(
          $reservation_rows[$i]['name'],
          $reservation_rows[$i]['notes'],
          $reservation_rows[$i]['slot_id'],
          $reservation_rows[$i]['user_id']

        );
        $reservation->set_id($reservation_rows[$i]['id']);
        $reservation->set_reservation_date($reservation_rows[$i]['reservation_date']);
        array_push($reservation_list, $reservation);
    }
    return $reservation_list;
  }

  // services methods
  function get_reservations_by_id($list_of_ids){

    $reservation_rows = $this->get_all_reservations();


    $result = array();
    for ($i=0; $i<count($reservation_rows); $i++){
      if (in_array($reservation_rows[$i]->id, $list_of_ids)){
        array_push($result, $reservation_rows[$i]);
      }
    }
    return $result;

  }


  // add more than one db row
  function add_reservations($reservation_data_list){
    $reservations_ids = array();
    if (count($reservation_data_list) < 1){
      return False;
    }

    $reservation = new Reservation();

    for ($i=0; $i<count($reservation_data_list); $i++){

      if (is_array($reservation_data_list[$i]) && count($reservation_data_list[$i]) == 4){

         $reservation->init(
           $reservation_data_list[$i][0],
           $reservation_data_list[$i][1],
           $reservation_data_list[$i][2],
           $reservation_data_list[$i][3]
         );
         $reservation_id = $this->reservation_mapper->insert($reservation);
         array($reservations_ids, $reservation_id);
      }
    }
    return $reservations_ids;
  }

  function delete_reservations($list_of_ids){
    $deleted = 0;
    for ($i=0; $i<count($list_of_ids); $i++){
      if (is_numeric($list_of_ids[$i])){
        $deleted += $this->remove($list_of_ids[$i]) ? 1 : 0;
      }
    }
    return $deleted;
  }

  // update signle column  reservation
  function update_one_column($column, $value, $id){
    return $this->reservation_mapper->update_column($column, $value, $id);
  }

  function get_total_reservations(){
    return $this->reservation_mapper->get_total_reservations();
  }

}

/* ##################### Test #################### */
/* #####################

global $pdo;
$reservationService = new ReservationService($pdo);

echo "<pre>";
$reservationService->add('myname', 'mynotes', 1, 1);
$reservationService->remove(1);

$reservationService->add_reservations(array(array('myname2', 'mynotes2', 1, 1),array('myname3', 'mynotes3', 1, 1)));

print_r($reservationService->get_reservation_by_id(2));
echo "<h1>get_reservation_by_id</h1><br /><br /><hr />";
echo count($reservationService->get_all_reservations());
echo "<h1>get_all_reservations</h1><br /><br /><hr />";

print_r($reservationService->get_reservations_by_id(array(4,17,10)));
echo "<h1>get_reservations_by_id</h1><br /><br /><hr />";

$reservationService->delete_reservations(array(1,6,10));
echo "<h1>delete_reservations</h1><br /><br /><hr />";

echo "</pre>";

*/
