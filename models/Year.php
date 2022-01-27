<?php
class User {
  // Propertiess
  public $id;
  public $year;
  public $cal_id;

  function init($id, $year, $cal_id){
    $this->id = $id;
    $this->year = $year;
    $this->cal_id = $cal_id;
  }
  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }

  function set_year($year) {
    $this->year = $year;
  }
  function get_year() {
    return $this->year;
  }

  function set_cal_id($cal_id) {
    $this->cal_id = $cal_id;
  }
  function get_cal_id() {
    return $this->start_year;
  }
}
