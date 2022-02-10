<?php
class Slot {
  // Propertiess
  public $id;
  public $start_from;
  public $end_at;
  public $period_id;
  public $empty;
  public $slot_index;
  public $element_id;
  public $element_class;


  function init($start_from, $end_at, $period_id, $empty, $slot_index){
    $this->start_from = $start_from;
    $this->end_at = $end_at;
    $this->period_id = $period_id;
    $this->empty = $empty;
    $this->slot_index = $slot_index;
  }
  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }


  function set_start_from($start_from) {
    $this->start_from = $start_from;
  }
  function get_start_from() {
    return $this->start_from;
  }

  function set_end_at($end_at) {
    $this->id = $end_at;
  }
  function get_end_at() {
    return $this->end_at;
  }

  function set_period_id($period_id) {
    $this->period_id = $period_id;
  }
  function get_period_id() {
    return $this->period_id;
  }

  function set_empty($empty) {
    $this->empty = $empty;
  }
  function get_empty() {
    return $this->empty;
  }

  function set_slot_index($slot_index) {
    $this->id = $slot_index;
  }
  function get_slot_index() {
    return $this->slot_index;
  }

  function set_element_id($element_id) {
    $this->element_id = $element_id;
  }
  function get_element_id() {
    return $this->element_id;
  }

  function set_element_class($element_class) {
    $this->element_class = $element_class;
  }
  function get_element_class() {
    return $this->element_class;
  }

}
