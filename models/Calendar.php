<?php
class Calendar {
  // Properties
  public $id;
  public $title;
  public $start_year;
  public $end_year;

  function init($id, $title, $start_year, $end_year){
    $this->id = $id;
    $this->title = $title;
    $this->start_year = $start_year;
    $this->end_year = $end_year;
  }
  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }

  function set_title($title) {
    $this->title = $title;
  }
  function get_title() {
    return $this->title;
  }

  function set_start_years($start_year) {
    $this->start_year = $start_year;
  }
  function get_start_year() {
    return $this->start_year;
  }

  function set_end_year($end_year) {
    $this->end_year = $end_year;
  }
  function get_end_year() {
    return $this->end_year;
  }
}

//$calendar = new Calendar();
//$calendar->init('1', 'Bussnise Cal', '2000', '2100');



//echo $calendar->get_end_year();
//echo $calendar->get_title();
