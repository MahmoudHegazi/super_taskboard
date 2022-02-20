<?php
class Reservation {
  // Propertiess
  private $id;
  private $name;
  private $notes;
  private $slot_id;
  private $user_id;
  private $reservation_date;

  function init($name, $notes, $slot_id, $user_id){
    $this->notes = $notes;
    $this->name = $name;
    $this->slot_id = $slot_id;
    $this->user_id = $user_id;
  }

  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
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

  function set_user_id($user_id) {
    $this->user_id = $user_id;
  }
  function get_user_id() {
    return $this->user_id;
  }

  function set_reservation_date($reservation_date) {
    $this->reservation_date = $reservation_date;
  }
  function get_reservation_date() {
    return $this->reservation_date;
  }
}
