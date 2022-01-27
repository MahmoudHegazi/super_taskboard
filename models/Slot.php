<?php
class Slot {
  // Propertiess
  public $id;
  public $start_from;
  public $end_at;
  public $period_id;
  public $day_id;
  public $cal_id;

  function init($id, $start_from, $end_at, $period_id, $day_id, $cal_id){
    $this->id = $id;
    $this->start_from = $start_from;
    $this->end_at = $end_at;
    $this->peropd_id = $period_id;
    $this->day_id = $day_id;
    $this->cal_id = $cal_id;


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
  function get_peropd_id() {
    return $this->period_id;
  }

  function set_day_id($day_id) {
    $this->day_id = $day_id;
  }
  function get_day_id() {
    return $this->day_id;
  }

  function set_cal_id($cal_id) {
    $this->cal_id = $cal_id;
  }
  function get_cal_id() {
    return $this->cal_id;
  }
}
