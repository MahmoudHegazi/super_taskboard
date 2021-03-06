<?php
class Day {
  // Properties
  private $id;
  private $day;
  private $day_name;
  private $day_date;
  private $month_id;

  function init($day, $day_name, $day_date, $month_id){
    $this->day = $day;
    $this->day_name = $day_name;
    $this->day_date = $day_date;
    $this->month_id = $month_id;
  }
  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }

  function set_day($day) {
    $this->day = $day;
  }
  function get_day() {
    return $this->day;
  }

  function set_day_name($day_name) {
    $this->day_name = $day_name;
  }
  function get_day_name() {
    return $this->day_name;
  }

  function set_day_date($day_date) {
    $this->day_date = $day_date;
  }
  function get_day_date() {
    return $this->day_date;
  }

  function set_month_id($month_id) {
    $this->month_id = $month_id;
  }
  function get_month_id() {
    return $this->month_id;
  }


}
