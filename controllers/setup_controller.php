<?php
require_once(dirname(__FILE__, 2) . '\config.php');
require_once(dirname(__FILE__, 2) . '\functions.php');
require_once(dirname(__FILE__, 2) . '\services\CalendarService.php');
require_once(dirname(__FILE__, 2) . '\services\YearService.php');
require_once(dirname(__FILE__, 2) . '\services\MonthService.php');
require_once(dirname(__FILE__, 2) . '\services\DayService.php');
require_once(dirname(__FILE__, 2) . '\services\PeriodService.php');
require_once(dirname(__FILE__, 2) . '\services\SlotService.php');
require_once(dirname(__FILE__, 2) . '\services\UserService.php');
require_once(dirname(__FILE__, 2) . '\models\Calendar.php');


$_SESSION['error_displayed'] = False;


$redirect_url = $_SERVER['HTTP_REFERER'];
/*
if (!isset($redirect_url)){
  header("Location: ../index.php");
}
*/

function create_calendar($cal_id,$calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $periods_data, $slots_data){
  global $pdo;
  $calendar_service = new CalendarService($pdo);
  $year_service = new YearService($pdo);
  $month_service = new MonthService($pdo);
  $day_service = new DayService($pdo);
  $period_service = new PeriodService($pdo);
  $slot_service = new SlotService($pdo);

  $calendar = new Calendar();
  $calendar->init($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description);
  $calendar->set_id($cal_id);

  $years = array();
  $months = array();
  $days = array();
  $periods = array();
  $slots = array();
/*
  $yearService->add_years($years);
  $dayService->add_days($days);
  $monthService->add_months($months);
*/
  $current_year = intval($start_year);

  for ($y=0; $y<$added_years; $y++){
    //add year
    $yearid = $year_service->add($current_year, $cal_id);


    // add 12 months
    for ($month=1; $month<=12; $month++){
      // add month
      $month_id = $month_service->add($month, $yearid);

      // get days in the month
      $month_days = cal_days_in_month(CAL_GREGORIAN,$month,$current_year);
      for ($day=1; $day<=$month_days; $day++){
        $month_string = $month <= 9 ? '0'.$month : $month;
        $day_string = $day <= 9 ? '0'.$day : $day;
        $full_date = $current_year . '-' . $month_string . '-' . $day_string;
        $jd=gregoriantojd($month,$day,$current_year);
        $dayname = jddayofweek($jd,1);
        // add day
        $day_id = $day_service->add($day, $dayname, $full_date, $month_id);
        if ($periods_per_day > 0 && $periods_per_day == $periods_per_day){
          // $slots_data

          for ($period=1; $period<=$periods_per_day; $period++){
            // get 3 dates array (Periods)
            $current_period = $periods_data[$period-1];
            $description = isset($current_period['description']) && !empty($current_period['description']) ? $current_period['description'] : NULL;
            $perioddate = isset($current_period['period_date']) && !empty($current_period['period_date']) ? $current_period['period_date'] : NULL;

            echo  '<br /><pre>';
            print_r($current_period);
            echo  '<br /></pre>';

            $period_id = $period_service->add($day_id, $perioddate, $description);
            if ($slots_per_period > 0 && count($slots_data) == $slots_per_period){

              for ($slot=1; $slot<=$slots_per_period; $slot++){
                $current_slot = $slots_data[$slot-1];

                echo  '<br /><pre>';
                print_r($current_slot);
                echo  '<br /></pre>';

                $description = $current_slot['start_from'];
                $perioddate = $current_slot['end_at'];

                $start_from = isset($current_slot['start_from']) && !empty($current_slot['start_from']) ? $current_slot['start_from'] : NULL;
                $end_at = isset($current_slot['end_at']) && !empty($current_slot['end_at']) ? $current_slot['end_at'] : NULL;

                $slot_id = $slot_service->add($start_from, $end_at, $period_id, True);
              }
            }

          }
        }

      }

    }

    $current_year += 1;


  }

}



function add_new_calendar($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $periods_data, $slots_data){
  global $pdo;
  $can_upload = True;
  $reason = '';
  $calendar_service = new CalendarService($pdo);
  $used = empty($calendar_service->get_all_calendars(1)) ? 1 : 0;

  $calendar_id = $calendar_service->add($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $used);
  // rules
  if (!$calendar_id){
    $can_upload = False;
    $reason = 'Missing calendar id';
  }

  if (intval($start_year) < 1900 || intval($start_year) > 5000){
    $can_upload = False;
    $reason = 'Start Year can not be less than 1900 or highr than 5000 selected year is: '. $start_year;
  }

  if (intval($added_years) < 1){
    $can_upload = False;
    $reason = 'No Added Years Deatected Added Year must be positve number';
  }

  if (empty($calendar_name)){
    $can_upload = False;
    $reason = 'Please Prove Calendar Title is required';
  }

  if ($can_upload == True){
    create_calendar($calendar_id, $calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $periods_data, $slots_data);
  }

  return array('success'=> $can_upload, 'reason'=>$reason, 'calendar_id'=>$calendar_id);

}


/*
$redirect_url = add_query_parameters($redirect_url,array('message'), array('Missing Period values please fill all Period input.'));
header("Location: " . $redirect_url);
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

if (
  isset($_POST['calendar_title']) && !empty($_POST['calendar_title']) &&
  isset($_POST['start_year']) && !empty($_POST['start_year']) &&
  isset($_POST['add_new_year']) && !empty($_POST['add_new_year']) &&
  isset($_POST['period_per_day']) &&
  isset($_POST['slots_per_period'])
){
  //add_new_calendar('Super Calendar', '2022', 1, 3, 3);
  $calendar_title = test_input($_POST['calendar_title']);
  $start_year = test_input($_POST['start_year']);
  $add_new_year = intval(test_input($_POST['add_new_year']));
  $period_per_day = intval(test_input($_POST['period_per_day']));
  $slots_per_period = intval(test_input($_POST['slots_per_period']));
  $calendar_description = isset($_POST['calendar_description']) && !empty($_POST['calendar_description']) ? test_input($_POST['calendar_description']) : 'Calendar to book' ;
  $periods_data = array();
  $slots_data = array();

  $ready = True;


  // this how I get dynamic the slots and period data JS/PHP syncyed + easy way
  /* ##----------- periods -----------## */


  if ($period_per_day>0){
    for ($period_index=0; $period_index<$period_per_day; $period_index++){
      $dateInput = 'period_date_'.($period_index+1);
      $descInput = 'period_description_'.($period_index+1);

      $dateInput = isset($_POST[$dateInput]) && !empty($_POST[$dateInput]) ? test_input($_POST[$dateInput]) : '';
      $descInput = isset($_POST[$descInput]) && !empty($_POST[$descInput]) ? test_input($_POST[$descInput]) : '';


      array_push($periods_data, array("period_date"=> $dateInput,"description"=> $descInput) );
    }
  }

  /* ##----------- slots -----------## */

  if ($slots_per_period>0){
    for ($slots_index=0; $slots_index<$slots_per_period; $slots_index++){
      $startFromInput = 'start_at_slot_'.($slots_index+1);
      $endAtInput = 'end_at_slot_'.($slots_index+1);

      $startFromInput = isset($_POST[$startFromInput]) && !empty($_POST[$startFromInput]) ? test_input($_POST[$startFromInput]) : '';
      $endAtInput = isset($_POST[$endAtInput]) && !empty($_POST[$endAtInput]) ? test_input($_POST[$endAtInput]) : '';


      array_push($slots_data, array("start_from"=> $startFromInput,"end_at"=> $endAtInput) );
    }
  }


  if ($ready == True){



    $new_cal = add_new_calendar($calendar_title, $start_year, $add_new_year, $period_per_day, $slots_per_period, $calendar_description, $periods_data, $slots_data);
    if ($new_cal['success'] == True){
      //echo $new_cal['calendar_id'];
      $calendar_id = $new_cal['calendar_id'];
      if(
            isset($_FILES) && !empty($_FILES) &&
            isset($_FILES['background_image']) &&
            !empty($_FILES['background_image'])

        ) {
        $target_dir = "../uploads/images/";

        $uploadOk = upload_image(
          $_FILES,
          $target_dir,
          'background_image',
          array("jpg", "png", "jpeg", "gif"),
          500000,
          'image',
          $calendar_id
        );
        if ($uploadOk['success'] == True){
          // update default image if image uploaded
          global $pdo;
          $calender_service = new CalendarService($pdo);
          $updated_image = $calender_service->update_one_column('background_image', $uploadOk['image'], $calendar_id);
          $message =  $updated_image == 0 ? 'Your image not Uploaded But calendar Created With default image' : 'Calendar With id: '.$calendar_id.' Created successfully';
          $success =  $uploadOk['success'] == True ? 'true' : 'false';

          $redirect_url = addOrReplaceQueryParm($redirect_url,'success',$success);
          $redirect_url = addOrReplaceQueryParm($redirect_url,'message',$message);
          header("Location: " . $redirect_url);
          return False;
          die();
        } else {
          $redirect_url = replace_query_paremeters($redirect_url,array('success','message'), array('false', $uploadOk['reason']));
          header("Location: " . $redirect_url);
          return False;
          die();
        }
      }
    } else {
      $redirect_url = replace_query_paremeters($redirect_url,array('success','message'), array('false', $new_cal['reason']));
      header("Location: " . $redirect_url);
      return False;
      die();
    }
  } else{
    $redirect_url = replace_query_paremeters($redirect_url,array('success','message'), array('false', 'Unkown Error Calendar Can not setup please edit Configurations and try again'));
    header("Location: " . $redirect_url);
    return False;
    die();
  }

}

}

function setup_redirect($url, $success, $message){
  $url = addOrReplaceQueryParm($url,'success',$success);
  $url = addOrReplaceQueryParm($url,'message',$message);
  header("Location: " . $url);
  return False;
  die();
}

/* Remove Calendar */
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (isset($_POST['remove_calendar_id']) && !empty($_POST['remove_calendar_id'])) {
    global $pdo;
    $cal_id = intval(test_input($_POST['remove_calendar_id']));
    $calendar_service = new CalendarService($pdo);
    $cal = $calendar_service->get_calendar_by_id($cal_id);
    if ($cal){
      if ($calendar_service->remove($cal_id)){

        $is_used_calendar = $cal->get_used();
        $first_remain_cal = $calendar_service->get_all_calendars($limit=1,$offset=0);
        if (count($first_remain_cal) > 0){
          $calendar_service->update_one_column('used', 1, $first_remain_cal[0]->get_id());
        }
        setup_redirect($redirect_url, 'true', 'Calendar: '.$cal->get_title().' Removed successful.');
      } else {
        setup_redirect($redirect_url, 'false', 'Could not remove Calendar With ID: '.$cal_id.'.');
      }
    } else {
      setup_redirect($redirect_url, 'false', 'Calendar Not Found Please Refresh The Page');

    }
  }
}
/* Remove Calendar end */




/* Remove User */
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (isset($_POST['remove_user_id']) && !empty($_POST['remove_user_id'])) {
    global $pdo;
    $user_id = intval(test_input($_POST['remove_user_id']));
    $user_service = new UserService($pdo);

    if ($user_service->get_user_by_id($user_id)){
      if ($user_service->remove($user_id)){
        setup_redirect($redirect_url, 'true', "Successfully removed user with ID:".$user_id);
      } else {
        setup_redirect($redirect_url, 'false', 'Could not remove the user');
      }
    } else {
      setup_redirect($redirect_url, 'false', 'User Not Found Please Refresh The Page');
    }
  }
}
/* Remove User end */



/* Add User */
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (
       isset($_POST['fullname']) && !empty($_POST['fullname']) &&
       isset($_POST['email']) && !empty($_POST['email']) &&
       isset($_POST['username']) && !empty($_POST['username']) &&
       isset($_POST['password']) && !empty($_POST['password'])
     ) {
    global $pdo;

    $fullname = test_input($_POST['fullname']);
    $email = test_input($_POST['email']);
    $username = test_input($_POST['username']);
    $password = test_input($_POST['password']);
    $password_hash = password_hash($password, PASSWORD_DEFAULT, array('cost' => 9));

    //password_verify('anna', $expensiveHash); //Also returns true


    $user_service = new UserService($pdo);
    $new_user = $user_service->add($fullname, $username, $password_hash, $email);
    if ($new_user){
        setup_redirect($redirect_url, 'true', "Successfully Add user with ID:".$new_user . " and Name:".$fullname);
    } else {
      setup_redirect($redirect_url, 'false', 'User '.$fullname.' can not added');
    }
  }
}
/* add User end */

/* update user */
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (
       isset($_POST['fullname_edit']) && !empty($_POST['fullname_edit']) &&
       isset($_POST['email_edit']) && !empty($_POST['email_edit']) &&
       isset($_POST['username_edit']) && !empty($_POST['username_edit']) &&
       isset($_POST['userid_edit']) && !empty($_POST['userid_edit'])
     ) {

       global $pdo;
       $user_service = new UserService($pdo);
       $req_uid = test_input($_POST['userid_edit']);
       $selected_user = $user_service->get_user_by_id($req_uid);
       echo $selected_user->get_id();
       if ($selected_user){

         $req_email = test_input($_POST['email_edit']);
         $req_username = test_input($_POST['username_edit']);
         $req_name = test_input($_POST['fullname_edit']);
         $update_string = '';

         if ($selected_user->get_name() != $req_name){
           $user_service->update_one_column('name', $req_name, $selected_user->get_id());
           $update_string .= 'name,';
         }
         if ($selected_user->get_username() != $req_username){
           $user_service->update_one_column('username', $req_username, $selected_user->get_id());
           $update_string .= 'username,';
         }
         if ($selected_user->get_email() != $req_email){
           $user_service->update_one_column('email', $req_email, $selected_user->get_id());
           $update_string .= 'email,';
         }

         if (isset($_POST['password_edit']) && !empty($_POST['password_edit'])){
           $update_string .= 'password,';
           // password not enabled by default so when he come it need change pass
           $req_password = test_input($_POST['password_edit']);
           $password_hash = password_hash($req_password, PASSWORD_DEFAULT, array('cost' => 9));
           $user_service->update_one_column('hashed_password', $password_hash, $selected_user->get_id());
         }
         $update_string = '[' . substr($update_string, 0, -1) . ']';
         setup_redirect($redirect_url, 'true', 'User ID:'.$selected_user->get_id().' Updated Successfully Data Updated: ' . $update_string);
       } else {
         setup_redirect($redirect_url, 'false', 'User Not Found');
       }
     }
}
/* update user end */


/* set calendar as used */

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (isset($_POST['calendar_used_id']) && !empty($_POST['calendar_used_id'])){
    global $pdo;
    $calendar_service = new CalendarService($pdo);
    $success = True;

    $cal_used_id = test_input($_POST['calendar_used_id']);
    // remove all used calendars
    $remove_all_used = $calendar_service->update_columns_where('used', 1, 0);
    if (!$remove_all_used){
      $success = False;
    }
    if ($success == True){
      $update_cal = $calendar_service->update_one_column('used', 1, $cal_used_id);
      if (!$update_cal){
        $success = False;
      }
    }
    if ($success){
      setup_redirect($redirect_url, 'true', 'Calendar With ID:'.$cal_used_id.' Marked As Used');
    } else {
      setup_redirect($redirect_url, 'false', 'Calendar Could not updated try refresh the page');
    }

  }
}

/* set calendar as used  */



/* set calendar as used */

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (isset($_POST['calendar_userid_edit']) && !empty($_POST['calendar_userid_edit'])){
    $cal_edit_id = test_input($_POST['calendar_userid_edit']);
    $success = True;
    $reason = '';
    $update_string = '';

    global $pdo;
    $calendar_service = new CalendarService($pdo);

    $get_calendar = $calendar_service->get_calendar_by_id($cal_edit_id);

    if (!$cal_edit_id){
      setup_redirect($redirect_url, 'false', 'Calendar With ID:'.$cal_edit_id. ' Is Not Found');
    }

    if (isset($_POST['calendar_title_edit']) && !empty($_POST['calendar_title_edit'])){

      $title = test_input($_POST['calendar_title_edit']);
      if ($get_calendar->get_title() != $title){
        $update_cal = $calendar_service->update_one_column('title', $title, $cal_edit_id);
        if (!$update_cal){
          $success = False;
          $reason = 'Could not update the title';
        } else {
          $update_string .= 'title,';
        }
      }

    }
    if (isset($_POST['calendar_description_edit']) && !empty($_POST['calendar_description_edit'])){

      $description = test_input($_POST['calendar_description_edit']);
      if ($get_calendar->get_description() != $description){
        $update_cal = $calendar_service->update_one_column('description', $description, $cal_edit_id);
        if (!$update_cal){
          $success = False;
          $reason = 'Could not update the description';
        } else {
          $update_string .= 'description,';
        }
      }
    }
    if (
      isset($_FILES) && !empty($_FILES) &&
      isset($_FILES['background_image_edit']) &&
      !empty($_FILES['background_image_edit'])
    ){
      $image_edit = $_FILES['background_image_edit']['size'] > 0 ? True : False;
      if ($image_edit){
        $target_dir = "../uploads/images/";
        $replaceImage = upload_image(
          $_FILES,
          $target_dir,
          'background_image_edit',
          array("jpg", "png", "jpeg", "gif"),
          500000,
          'image',
          $cal_edit_id
        );

        if (!$replaceImage){
          $success = False;
          $reason = 'Could not update the background';
        } else {
          $update_cal = $calendar_service->update_one_column('background_image', $replaceImage['image'], $cal_edit_id);
          if (!$update_cal){
            $success = False;
            $reason = 'Could not save new background URL';
          } else {
            $update_string .= 'background_image,';
          }
        }
      }
    }

    if ($success == True) {
      $update_string = '[' . substr($update_string, 0, -1) . ']';
      setup_redirect($redirect_url, 'true', 'Update Calendar With ID:'. $cal_edit_id .'Changed: '.$update_string);
    } else {
      setup_redirect($redirect_url, 'false', $reason);
    }


  }
}
/* Edit calendar end */


/* add new years to calendar */

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  if (
    isset($_POST['add_new_year_edit']) && !empty($_POST['add_new_year_edit']) &&
    isset($_POST['years_added_calid']) && !empty($_POST['years_added_calid'])
    ){
    global $pdo;
    $calendar_service = new CalendarService($pdo);

    $added_years = intval(test_input($_POST['add_new_year_edit']));
    $calid = intval(test_input($_POST['years_added_calid']));

    $cal_data = $calendar_service->get_calendar_by_id($calid);
    print_r($cal_data);
    if (!$cal_data || empty($cal_data)){
      setup_redirect($redirect_url, 'false', 'Calendar Not Found Please Refresh the Page.');
    }

    $periods_sql = "SELECT DISTINCT period.description, period.period_date FROM calendar
    JOIN year ON calendar.id = year.cal_id JOIN month ON month.year_id =
    year.id JOIN day ON day.month_id = month.id JOIN period ON day.id =
    period.day_id JOIN slot ON period.id = slot.period_id WHERE
    calendar.id=".$cal_data->get_id();
    $periods_data_rows = $calendar_service->free_group_query($periods_sql);
    $periods_data=array();
    for ($p=0; $p<count($periods_data_rows); $p++){
      array_push($periods_data,
         array(
           'description'=> $periods_data_rows[$p]['description'],
           'period_date'=> $periods_data_rows[$p]['period_date']
        )
      );
    }


    $slots_sql = "SELECT DISTINCT slot.start_from, slot.end_at FROM calendar
    JOIN year ON calendar.id = year.cal_id JOIN month ON month.year_id =
    year.id JOIN day ON day.month_id = month.id JOIN period ON day.id =
    period.day_id JOIN slot ON period.id = slot.period_id WHERE
    calendar.id=".$cal_data->get_id();

    $slots_data_rows = $calendar_service->free_group_query($slots_sql);
    $slots_data = array();
    print_r($slots_data_rows);
    for ($s=0; $s<count($slots_data_rows); $s++){
      array_push($slots_data,array(
        'start_from'=> $slots_data_rows[$s]['start_from'],
        'end_at'=> $slots_data_rows[$s]['end_at']
        )
      );
    }
    create_calendar($cal_data->get_id(), $cal_data->get_title(), intval($cal_data->get_start_year())+1, $added_years, $cal_data->get_periods_per_day(), $cal_data->get_slots_per_period(), $cal_data->get_description(), $periods_data, $slots_data);

    //echo $calendar_service->get_arrayperiodss_where($column, $value);
    echo $added_years . ':' . $cal_data->get_id();
    echo '<pre>';
    print_r($periods_data);
    echo '</pre><br /><pre>';
    print_r($slots_data);
    echo '</pre>';
  }
}
/* end add new years */

//SELECT day.id AS day_id, calendar.id, year.id, month.id FROM calendar JOIN year ON calendar.id = year.cal_id JOIN month ON month.year_id = year.id JOIN day ON day.month_id = month.id WHERE calendar.id=40

?>
