<?php
class Reservation {
  // Propertiess
  public $id;
  public $name;
  public $notes;
  public $day_id;
  public $slot_id;
  public $cal_id;


  function init($id, $name, $notes, $day_id, $slot_id, $cal_id){
    $this->id = $id;
    $this->notes = $notes;
    $this->name = $name;
    $this->day_id = $day_id;
    $this->slot_id = $slot_id;
    $this->cal_id = $cal_id;

  }
  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }

  function set_day_id($day_id) {
    $this->day_id = $day_id;
  }
  function get_day_id() {
    return $this->day_id;
  }

  function set_name($name) {
    $this->name = $name;
  }
  function get_name() {
    return $this->name;
  }

  function set_notes($notes) {
    $this->notes = $notes;
  }
  function get_notes() {
    return $this->notes;
  }



  function set_slot_id($slot_id) {
    $this->slot_id = $slot_id;
  }
  function get_slot_id() {
    return $this->slot_id;
  }

  function set_cal_id($cal_id) {
    $this->cal_id = $cal_id;
  }
  function get_cal_id() {
    return $this->cal_id;
  }

}
