<!DOCTYPE html>
<html>
<body>

<?php
$cars=array(
  "Volvo","BMW","Toyota","Honda","Mercedes","Opel","osa",
  "Volvo","BMW","Toyota","Honda","Mercedes","Opel","osa",
  "Volvo","BMW","Toyota","Honda","Mercedes","Opel","osa",
  "Volvo","BMW","Toyota","Honda","Mercedes","Opel","osa"
);

function check_counts($arr, $elm_counts, $max_array){
  $new_arr = $arr;
  for ($i=0; $i<count($new_arr); $i++){
    $current_count = count($new_arr);
    // add array to complete the size
    if ($current_count < $max_array){
      $remaining = $max_array - $current_count;
      $remaining = $remaining > 0 ? $remaining : 0;
      for ($newindex=0; $newindex<$remaining; $newindex++){
        array_push($new_arr, array());
      }
      print_r($new_arr);
      die();
      /*
      for ($a=0; $a<($max_array - $current_count); $a){
        array_push($new_arr, array());
      }*/
    }

    /* check how many elements per array
    $1 we need loop on arrays
    2- for each array check count of it
    3-if count < elm_counts loop on the result
    of substrict remain and push on the current
    array empty or false
    4-result when have 31 or 30 have empty slots
    and most important give me valid weeks always 1 time
    */

    echo $current_count < $max_array ? 'yes' : $max_array;
    echo "<pre>";
    print_r($new_arr);
    echo "</pre>";
    //return array();
    die();
    if ($current_count < $elm_counts){
      for ($x=$current_count; $x< $elm_counts; $x++){
        $new_arr[$current_count] = false;
        echo "yes<br />";
      }
    }
  }
  return $new_arr;
}
function array_distribution($arr, $num, $max_array){
  if (count($arr) < 1){return false;}
  if ($num < 1){return $arr;}
  $result = array();
  $index_added = 0;
  $mini_array = array();
  for ($i=0; $i<count($arr); $i++){
    if ($index_added<$num && $i < (count($arr)-1)){
      array_push($mini_array, $arr[$i]);
      $index_added += 1;
    } else if ($i == count($arr)-1){
      array_push($mini_array, $arr[$i]);
      array_push($result, $mini_array);
    } else {
      array_push($result, $mini_array);
      $mini_array = array();
      array_push($mini_array, $arr[$i]);
      $index_added = 1;
    }


  }

  return check_counts($result, $num, $max_array);
}


echo "<pre>";
print_r(array_distribution($cars, 7, 5));
echo "</pre>";
?>

</body>
</html>
