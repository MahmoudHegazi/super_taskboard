<?php
class Element {
  // Properties
  private $id;
  private $element_id;
  private $class_name;
  private $bootstrap_classes;
  private $cal_id;
  private $data_group;

  // advanced other verios update
  private $default_bootstrap;
  private $default_style;
  private $innerHTML;
  private $innerText;
  private $data;

  function init($element_id, $class_name, $cal_id, $default_bootstrap='', $default_style = '', $data_group=NULL, $bootstrap_classes='', $innerHTML=NULL, $innerText=NULL, $data=NULL){
    $this->element_id = $element_id;
    $this->class_name = $class_name;
    $this->cal_id = $cal_id;
    $this->default_bootstrap = $default_bootstrap;
    $this->default_style = $default_style;
    $this->data_group = $data_group;
    $this->bootstrap_classes = $bootstrap_classes;
    $this->innerHTML = $innerHTML;
    $this->innerText = $innerText;
    $this->data = $data;
  }

  // Setter and geter

  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }

  function set_element_id($element_id) {
    $this->element_id = $element_id;
  }
  function get_element_id() {
    return $this->element_id;
  }

  function set_class_name($class_name) {
    $this->class_name = $class_name;
  }
  function get_class_name() {
    return $this->class_name;
  }

  function set_cal_id($cal_id) {
    $this->cal_id = $cal_id;
  }
  function get_cal_id() {
    return $this->cal_id;
  }

  function set_default_bootstrap($default_bootstrap) {
    $this->default_bootstrap = $default_bootstrap;
  }
  function get_default_bootstrap() {
    return $this->default_bootstrap;
  }

  function set_default_style($default_style) {
    $this->default_style = $default_style;
  }
  function get_default_style() {
    return $this->default_style;
  }


  function set_data_group($data_group) {
    $this->data_group = $data_group;
  }
  function get_data_group() {
    return $this->data_group;
  }

  function set_bootstrap_classes($bootstrap_classes) {
    $this->bootstrap_classes = $bootstrap_classes;
  }
  function get_bootstrap_classes() {
    return $this->bootstrap_classes;
  }

  function set_innerHTML($innerHTML) {
    $this->innerHTML = $innerHTML;
  }
  function get_innerHTML() {
    return $this->innerHTML;
  }

  function set_innerText($innerText) {
    $this->innerText = $innerText;
  }
  function get_innerText() {
    return $this->innerText;
  }

  function set_data($data) {
    $this->data = $data;
  }
  function get_data() {
    return $this->data;
  }

}
