<?php
class Calendar {
  // Properties
  private $id;
  private $title;
  private $start_year;
  private $added_years;
  private $periods_per_day;
  private $slots_per_period;
  private $description;
  private $used;
  private $background_image;
  private $sign_background;

  function init($title, $start_year, $added_years, $periods_per_day, $slots_per_period, $description){
    $this->title = $title;
    $this->start_year = $start_year;
    $this->added_years = $added_years;
    $this->periods_per_day = $periods_per_day;
    $this->slots_per_period = $slots_per_period;
    $this->description = $description;
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

  function set_added_years($added_years) {
    $this->added_years = $added_years;
  }
  function get_added_years() {
    return $this->added_years;
  }

  function set_periods_per_day($periods_per_day) {
    $this->periods_per_day = $periods_per_day;
  }
  function get_periods_per_day() {
    return $this->periods_per_day;
  }

  function set_slots_per_period($slots_per_period) {
    $this->slots_per_period = $slots_per_period;
  }
  function get_slots_per_period() {
    return $this->slots_per_period;
  }

  function set_description($description) {
    $this->description = $description;
  }
  function get_description() {
    return $this->description;
  }

  function set_used($used) {
    $this->used = $used;
  }
  function get_used() {
    return $this->used;
  }

  function set_background_image($background_image) {
    $this->background_image = $background_image;
  }
  function get_background_image() {
    return $this->background_image;
  }

  function set_sign_background($sign_background) {
    $this->sign_background = $sign_background;
  }
  function get_sign_background() {
    return $this->sign_background;
  }

}

//$calendar = new Calendar();
//$calendar->init('1', 'Bussnise Cal', '2000', '2100');



//echo $calendar->get_end_year();
//echo $calendar->get_title();
