<?php
class User {
  // Propertiess
  public $id;
  public $name;
  public $username;
  public $hashed_password;
  public $email;

  function init($name, $username, $hashed_password, $email){
    $this->name = $name;
    $this->username = $username;
    $this->hashed_password = $hashed_password;
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
    return $this->name;
  }

  function set_username($username) {
    $this->username = $username;
  }
  function get_username() {
    return $this->username;
  }

  function set_hashed_password($hashed_password) {
    $this->hashed_password = $hashed_password;
  }
  function get_hashed_password() {
    return $this->hashed_password;
  }

  function set_email($email) {
    $this->email = $email;
  }
  function get_email() {
    return $this->email;
  }
}
