<?php
class Month {
  // Properties
  public $id;
  public $month;
  public $year_id;

  function init($month, $year_id){
    $this->month = $month;
    $this->year_id = $year_id;
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

}
