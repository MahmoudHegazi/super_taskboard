<?php
class Style {
  // Properties
  public $id;
  public $classname;
  public $element_id;
  public $style;
  public $class_id;
  public $active;
  public $title;
  public $custom;
  public $cal_id;
  public $category;

  function init($classname, $element_id, $style, $class_id, $active, $title, $custom, $cal_id, $category){
    $this->classname = $classname;
    $this->element_id = $element_id;
    $this->style = $style;
    $this->class_id = $class_id;
    $this->active = $active;
    $this->title = $title;
    $this->custom = $custom;
    $this->cal_id = $cal_id;
    $this->category = $category;
  }
  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }


  function set_classname($classname) {
    $this->classname = $classname;
  }
  function get_classname() {
    return $this->classname;
  }

  function set_element_id($element_id) {
    $this->element_id = $element_id;
  }
  function get_element_id() {
    return $this->element_id;
  }


  function set_style($style) {
    $this->style = $style;
  }
  function get_style() {
    return $this->style;
  }

  function set_class_id($class_id) {
    $this->class_id = $class_id;
  }
  function get_class_id() {
    return $this->class_id;
  }

  function set_active($active) {
    $this->active = $active;
  }
  function get_active() {
    return $this->active;
  }

  function set_title($title) {
    $this->active = $title;
  }
  function get_title() {
    return $this->title;
  }

  function set_custom($custom) {
    $this->custom = $custom;
  }
  function get_custom() {
    return $this->custom;
  }


  function set_cal_id($cal_id) {
    $this->cal_id = $cal_id;
  }
  function get_cal_id() {
    return $this->cal_id;
  }


  function set_category($category) {
    $this->category = $category;
  }
  function get_category() {
    return $this->category;
  }

}
