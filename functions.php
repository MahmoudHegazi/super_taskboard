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
    if ($i != count($parms) -1){
      $newurl .= '&';
    }
  }
  return $newurl;
}

function replace_query_paremeters($url, $parms, $values){
  if (count($parms) != count($values)){return false;}
  $urllist = explode("?",$url);
  if (count($urllist) < 1){
    return $url;
  }
  $newurl = $urllist[0] . '?';

  for ($i=0; $i<count($parms); $i++){
    $newurl .= $parms[$i] . '=' . $values[$i];
    if ($i != count($parms) -1){
      $newurl .= '&';
    }
  }
  return $newurl;
}

function addOrReplaceQueryParm($url, $parm, $new_value){
$result = '';
if (!strpos($url,$parm.'=')){
  if (!strpos($url,'?')){
    $result = $url . '?' . $parm . '=' . $new_value;
  } else {
    $result = $url . '&' . $parm . '=' . $new_value;
  }

} else {

  $lenparm = strlen($parm."=");
  $found_parm = False;
  for($uindex=0; $uindex<strlen($url); $uindex++){


    if ($found_parm != True){
      $result .= $url[$uindex];
    }
    if ($uindex > $lenparm && $found_parm == True && $url[$uindex] == '&'){
      $found_parm = False;
      $result .= '&';
    }

    if ($uindex > $lenparm && $url[$uindex] == '='){
      $target_parm = substr($result,abs($lenparm)*-1,-1);
      if ($target_parm == $parm){
        $found_parm = True;
        $result .= $new_value;
      }
    }

  }
}
  return $result;
}


function implode_column_names($spreator=', ', $colnames){
  if (count($colnames) > 0){
    return implode(", ",$colnames);
  } else {
    return '';
  }
}

function upload_image($FILES, $target_dir, $input_name, $allowed_extensions, $max_size, $type, $calendar_id){
  $result = array('success'=>False, 'reason'=> '');
  $uploadOk = 1;

  $target_file = $target_dir . basename($FILES[$input_name]["name"]);
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

  $system_file_name = test_input('cal_background_'.$calendar_id.'.'.strtolower($imageFileType));
  $system_file = $target_dir . basename($system_file_name);


  // Check if image file is a actual image or fake image
  $check = getimagesize($FILES[$input_name]["tmp_name"]);
  if($check == false) {
    $uploadOk = 0;
    return array('success'=>False, 'reason'=> "File is not an ". $type .".", 'image'=>'');
  }


  // Check if file already exists
  if (file_exists($system_file)) {
    //echo "Sorry, file already exists.";
    // if file exist and this system name replace the file so remove it
    if (!unlink($system_file)) {
      $uploadOk = 0;
      return array(
        'success'=>False,
        'reason'=> "Sorry, file already exists: ". basename($FILES[$input_name]["name"]),
        'image'=>''
      );
    }
  }

  // Check file size 500000
  if ($_FILES[$input_name]["size"] > $max_size) {
    $uploadOk = 0;
    return array(
      'success'=>False,
      'reason'=> "Sorry, ".$type." is too large MAX size: ". $max_size ."KB",
      'image'=>''
    );
  }

  // Allow certain file formats array
  if( in_array($imageFileType, $allowed_extensions) == False ) {
    $reason = "Sorry, only " . strtoupper(implode(" ",$allowed_extensions)) . " files are allowed.";
    return array('success'=>False, 'reason'=> $reason, 'image'=>'');
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    return array('success'=>False, 'reason'=> "Sorry, File Could not uploaded", 'image'=>'');
  // if everything is ok, try to upload file
  } else {
    $success = move_uploaded_file($FILES[$input_name]["tmp_name"], $system_file);

    if ($success) {
      $reason = "The file ". htmlspecialchars( $system_file_name ). " has been uploaded.";
      return array('success'=>$success, 'reason'=> $reason, 'image'=>$system_file_name);

    } else {
      return array('success'=>$success, 'reason'=>'Files Could not uploaded Please Desk Space Issue', 'image'=>'');
    }


  }
}


function display_html_erro($GETobj){
    $server_message = isset($GETobj['success']) && !empty($GETobj['success']) && isset($GETobj['message']) && !empty($GETobj['message']);
    if ($server_message){
      $alert_type = test_input($GETobj['success']) == 'true' ? 'success' : 'danger';
      echo '
      <div class="row d-flex justify-content-center" id="server_message">
       <div class="text-black col-sm-11   alert alert-' . $alert_type . ' alert-dismissible">
         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         ' . test_input($GETobj['message']) . '
       </div>
      </div>
      ';
      return True;
    } else {
      return '';
    }

}
?>
