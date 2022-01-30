<?php
class Slot {
  // Propertiess
  public $id;
  public $start_from;
  public $end_at;
  public $period_id;
  public $empty;


  function init($start_from, $end_at, $period_id, $empty){
    $this->start_from = $start_from;
    $this->end_at = $end_at;
    $this->period_id = $period_id;
    $this->empty = $empty;
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
}
