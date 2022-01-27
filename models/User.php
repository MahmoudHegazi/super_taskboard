<?php
class User {
  // Propertiess
  public $id;
  public $name;
  public $username;
  public $password;
  public $email;

  function init($id, $name, $username, $password, $email){
    $this->id = $id;
    $this->name = $name;
    $this->username = $username;
    $this->password = $password;
    $this->email = $email;
  }
  // Setter and geter
  function set_id($id) {
    $this->id = $id;
  }
  function get_id() {
    return $this->id;
  }

  function set_name($name) {
    $this->title = $name;
  }
  function get_name() {
    return $this->namename;
  }

  function set_username($username) {
    $this->username = $username;
  }
  function get_username() {
    return $this->username;
  }

  function set_password($password) {
    $this->password = $password;
  }
  function get_password() {
    return $this->end_year;
  }
  function set_email($email) {
    $this->email = $email;
  }
  function get_email() {
    return $this->email;
  }
}
