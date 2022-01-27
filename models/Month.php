<?php
class Month {
  // Properties
  public $id;
  public $month;
  public $year_id;
  public $cal_id;

  function init($id, $month, $year_id, $cal_id){
    $this->id = $id;
    $this->month = $month;
    $this->year_id = $year_id;
    $this->cal_id = $cal_id;
  }
  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }

  function set_month($month) {
    $this->title = $month;
  }
  function get_month() {
    return $this->month;
  }

  function set_year_id($year_id) {
    $this->year_id = $year_id;
  }
  function get_year_id() {
    return $this->year_id;
  }

  function set_cal_id($cal_id) {
    $this->cal_id = $cal_id;
  }
  function get_cal_id() {
    return $this->cal_id;
  }
}
