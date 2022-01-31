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

function add_query_parameters($url, $parms, $values){
  if (count($parms) != count($values)){return false;}
  $newurl = count(explode("?",$url)) > 1 ? $url . '&' : $url .'?';
  for ($i=0; $i<count($parms); $i++){
    $newurl .= $parms[$i] . '=' . $values[$i];
  }
  return $newurl;
}

function implode_column_names($spreator=', ', $colnames){
  if (count($colnames) > 0){
    return implode(", ",$colnames);
  } else {
    return '';
  }
}



?>
