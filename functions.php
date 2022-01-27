<?php
function url(){
    if(isset($_SERVER['HTTP_ORIGIN'])){
        return $_SERVER['HTTP_ORIGIN'];
    }
    else{
        return 'localhost/supercalendar';
    }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function implode_column_names($spreator=', ', $colnames){
  if (count($colnames) > 0){
    return implode(", ",$colnames);
  } else {
    return '';
  }
}



?>
