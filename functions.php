<?php
function url(){
    if(isset($_SERVER['HTTP_ORIGIN'])){
        return $_SERVER['HTTP_ORIGIN'];
    }
    else{
        return 'localhost/supercalendar';
    }
}

function create_this_link($app_name, $suburl = ''){
  $current_url = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
  $url_list = explode("/", $current_url);
  $newurl = '';
  if (count($url_list) < 2){
    return $current_url;
  } else {
    for ($i=0; $i<count($url_list); $i++){
      if ($url_list[$i] == ''){
        continue;
      } else {
        if (strpos($url_list[$i], 'http') !== False) {
          $newurl .= $url_list[$i] . '//';
        } else {
          $newurl .= $url_list[$i] . '/';
        }
      }
      if ($url_list[$i] == $app_name){
        break;
      }
    }
  }
  return $newurl . strval($suburl);
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
// advanced function I created for array distribution with default text and muliti used better than chunk and more wider to serve the calendar
function array_distribution($data_array, $max_per_array, $max_array, $default=false){
  $result = array();
  $project_data = $data_array;
  // loop over num of arrays needed
  for ($arr=0; $arr<$max_array; $arr++){
    // create new array which can accept the max elements or add specafied value in case no again
    $new_child = array();
    for($elm=0; $elm<$max_per_array; $elm++){
      if (count($project_data) > 0){
        array_push($new_child, $project_data[0]);
        array_shift($project_data);
      } else {
        array_push($new_child, $default);
        array_shift($project_data);
      }
      // now remove the added elem note in case it removed all np it will add the default else no more loops

    }
    array_push($result, $new_child);
  }
  return $result;
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

function upload_image($FILES, $target_dir, $input_name, $allowed_extensions, $max_size, $type, $calendar_id, $name_start='cal_background_'){
  $result = array('success'=>False, 'reason'=> '');
  $uploadOk = 1;

  $target_file = $target_dir . basename($FILES[$input_name]["name"]);
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

  $system_file_name = test_input($name_start . $calendar_id.'.'.strtolower($imageFileType));
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

function getDynamicBaseUrl(){
  $is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
  $scheme = $is_https ? 'https' : 'http';
  $url_list = explode("/", "/supercalendar/controllers/index_controller.php");
  if (count($url_list) > 1 ){
    $current_url  = $url_list[0] != '' ? $url_list[0] : $url_list[1];
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . $current_url . '/';
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

/* some other secuirtes Cloud auth tequnique */
function encryptString($plaintext, $password, $encoding = null) {
    $iv = openssl_random_pseudo_bytes(16);
    $ciphertext = openssl_encrypt($plaintext, "AES-256-CBC", hash('sha256', $password, true), OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext.$iv, hash('sha256', $password, true), true);
    return $encoding == "hex" ? bin2hex($iv.$hmac.$ciphertext) : ($encoding == "base64" ? base64_encode($iv.$hmac.$ciphertext) : $iv.$hmac.$ciphertext);
}

function decryptString($ciphertext, $password, $encoding = null) {
    $ciphertext = $encoding == "hex" ? hex2bin($ciphertext) : ($encoding == "base64" ? base64_decode($ciphertext) : $ciphertext);
    if (!hash_equals(hash_hmac('sha256', substr($ciphertext, 48).substr($ciphertext, 0, 16), hash('sha256', $password, true), true), substr($ciphertext, 16, 32))) return null;
    return openssl_decrypt(substr($ciphertext, 48), "AES-256-CBC", hash('sha256', $password, true), OPENSSL_RAW_DATA, substr($ciphertext, 0, 16));
}
?>
