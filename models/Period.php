<?php
class Period {
  // Properties
  public $id;
  public $day_id;
  public $period_date;
  public $description;

  function init($day_id, $period_date, $description){
    $this->day_id = $day_id;
    $this->period_date = $period_date;
    $this->description = $description;
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

  function set_period_date($period_date) {
    $this->period_date = $period_date;
  }
  function get_period_date() {
    return $this->period_date;
  }

  function set_description($description) {
    $this->description = $description;
  }
  function get_description() {
    return $this->description;
  }

}
