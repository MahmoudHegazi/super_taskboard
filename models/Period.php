<?php
class Period {
  // Properties
  private $id;
  private $day_id;
  private $period_date;
  private $description;
  private $period_index;
  private $element_id;
  private $element_class;
  private $period_end;


  function init($day_id, $period_date, $description, $period_index, $period_end=NULL){
    // if null variable it should be null default in class
    $this->day_id = $day_id;
    $this->period_date = $period_date;
    $this->description = $description;
    $this->period_index = $period_index;
    $this->period_end = $period_end;
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

  function set_period_index($period_index) {
    $this->period_index = $period_index;
  }
  function get_period_index() {
    return $this->period_index;
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

  function set_period_end($period_end) {
    $this->period_end = $period_end;
  }
  function get_period_end() {
    return $this->period_end;
  }

}
