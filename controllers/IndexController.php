<?php
ob_start();
require_once (dirname(__FILE__, 2) . '\config.php');
require_once (dirname(__FILE__, 2) . '\functions.php');
require_once (dirname(__FILE__, 2) . '\services\CalendarService.php');
require_once (dirname(__FILE__, 2) . '\services\YearService.php');
require_once (dirname(__FILE__, 2) . '\services\MonthService.php');
require_once (dirname(__FILE__, 2) . '\services\DayService.php');
require_once (dirname(__FILE__, 2) . '\services\PeriodService.php');
require_once (dirname(__FILE__, 2) . '\services\SlotService.php');
require_once (dirname(__FILE__, 2) . '\services\ReservationService.php');
require_once (dirname(__FILE__, 2) . '\services\UserService.php');
require_once (dirname(__FILE__, 2) . '\services\StyleService.php');
require_once (dirname(__FILE__, 2) . '\models\Calendar.php');


/*
setlocale(LC_TIME, 'sr_BA');
$month_name = date('F', mktime(0, 0, 0, $i));
echo $month_name;

https://stackoverflow.com/questions/13845554/php-date-get-name-of-the-months-in-local-language
*/

$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';


class IndexController {
  // connection and services
  protected $pdo;
  protected $calendar_service;
  protected $year_service;
  protected $month_service;
  protected $day_service;
  protected $period_service;
  protected $slot_service;
  protected $user_service;
  protected $style_service;
  protected $reservation_service;
  //protected $calendarService;
  protected $Calendar;

  // calendar meta data
  // current used cal id usefull
  protected $cal_id;
  protected $used_calendar;

  protected $years;
  protected $current_year;
  protected $current_month;
  protected $current_days;
  protected $current_months;
  protected $current_weeks;
  protected $current_min_year;
  protected $current_max_year;
  protected $has_years;

  /* Date and Time */
  protected $visit_date;
  protected $today_year;
  protected $today_month;
  protected $today_day;
  protected $today_date;
  protected $today_time;

  private $request_secert;




  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo, $selected_month=null, $type='GET')
  {
    // incase no used calendar which mean no calendars in app or deleted by DB (IMP), so will raise error
    $this->pdo = $pdo;
    $this->calendar_service = new CalendarService($pdo);
    $this->year_service = new YearService($pdo);
    $this->month_service = new MonthService($pdo);
    $this->day_service = new DayService($pdo);
    $this->period_service = new PeriodService($pdo);
    $this->slot_service = new SlotService($pdo);
    $this->user_service = new UserService($pdo);
    $this->style_service = new StyleService($pdo);
    $this->reservation_service = new ReservationService($pdo);

    $used_cal = $this->assign_used_calendar();
    if (!$used_cal || empty($used_cal) || !isset($used_cal)){
      // this error in case no used calendar, make sure not to use DB to delete calendars and if u did np but with next add/remove will solved
      throw new Exception( "Sorry, no used calendar found to display Please add a new calendar and it will be marked as used by default..." );
    }
    $this->set_used_calendar($used_cal);
    $this->set_calid($this->set_calid($used_cal->get_id()));
    $this->set_years($this->return_all_years($used_cal->get_id()));
    // you can change time zone from here
    $this->set_time_zone($time_zone='Europe/Rome');

    // current year
    $current_year = $this->return_current_year($used_cal->get_id());
    if (empty($current_year) || is_null($current_year)){
      throw new Exception( "No years were found for the specified calendar If u can not solve this add years to calendar or delete the calendar and add new one" );
    }
    $this->set_current_year($current_year);

    // current month
    $target_month = !is_null($selected_month) ? $selected_month : $this->get_today_month();
    $current_month = $this->return_curent_month($this->get_current_year()->get_id(), $target_month);
    if (empty($current_month) || is_null($current_month)){
      throw new Exception( "We Can not Get the Month Error Error:01" );
    }
    $this->set_current_month($current_month);

    // current months
    $current_months = $this->return_current_months($this->get_current_year()->get_id());
    if (empty($current_months) || is_null($current_months)){
      throw new Exception( "We Can not Get the current Month List make sure calendar setup is valid or try with other calendar Error:02" );
    }
    $this->set_current_months($current_months);

    $current_days = $this->return_current_days($current_month->get_id());
    if (!$current_days && empty($current_days)){
      throw new Exception( "We Can not Load the current dats of calendar Error Error:03" );
    }
    $this->set_current_days($current_days);
    //$this->set_current_weeks(array_distribution($this->monday_start_mange($current_days), 7, 5, $default=false));


    $projectData = $this->return_project_data($current_days);
    $this->set_current_weeks(array_distribution($projectData, 7, 5, $default=false));

    if ($type == 'GET'){
      $this->set_request_secert($this->getnerate_request_secert());
      $_SESSION['supcal_token'] = $this->get_request_secert();
    }

    // set start and end year
    $year_data = $this->return_year_data();
    $this->set_has_years($year_data ? true : false);
    if ($this->get_has_years() && isset($year_data['min_year']) && isset($year_data['max_year'])){
      $this->set_current_min_year($year_data['min_year']);
      $this->set_current_max_year($year_data['max_year']);
    }


    /*
    echo "<pre>";
    print_r(array_distribution($projectData, 7, 5, $default=false));
    //print_r(array_distribution($this->monday_start_mange($current_days), 7, 5, $default=false));
    echo "</pre>";/*
    die();
    $day_periods = $this->return_day_periods($current_days[0]->get_id());
    $period_slots = $this->return_period_slots($day_periods[0]->get_id());
    */
  }


  /* end getter and setter */

  // function redirect to the url with message understood from client and bootstrap
  public function setup_redirect($url, $success, $message)
  {
      ob_start();
      $url = addOrReplaceQueryParm($url, 'success', $success);
      $url = addOrReplaceQueryParm($url, 'message', $message);
      header("Location: " . $url);
      return ob_get_clean();
  }

  public function assign_used_calendar(){
    // used has 2 values 1 or 0  , 0 for any calendar not used 1 added automatic to the first added cal when all unused or when remove used cal
    $cal = $this->calendar_service->get_used_calendar("used", 1);
    if (!empty($cal)){
      return $cal;
    } else {
      return array();
    }
  }



  public function set_used_calendar($used_calendar){
    $this->used_calendar = $used_calendar;
  }

  public function get_used_calendar(){
    return $this->used_calendar;
  }



  public function set_calid($calid){
    $this->calid = $calid;
  }

  public function get_calid(){
    return $this->calid;
  }

  public function set_years($years){
    $this->years = $years;
  }

  public function get_years(){
    return $this->years;
  }

  public function set_current_year($current_year){
    $this->current_year = $current_year;
  }

  public function get_current_year(){
    return $this->current_year;
  }


  public function set_current_year_months($current_months){
    $this->current_months = $current_months;
  }

  public function get_current_year_months(){
    return $this->current_months;
  }


  public function set_current_months($current_months){
    $this->current_months = $current_months;
  }

  public function get_current_months(){
    return $this->current_months;
  }

  public function set_current_month($current_month){
    $this->current_month = $current_month;
  }

  public function get_current_month(){
    return $this->current_month;
  }

  public function set_current_days($current_days){
    $this->current_days = $current_days;
  }

  public function get_current_days(){
    return $this->current_days;
  }

  public function set_current_weeks($current_weeks){
    $this->current_weeks = $current_weeks;
  }

  public function get_current_weeks(){
    return $this->current_weeks;
  }

  public function set_request_secert($request_secert){
    $this->request_secert = $request_secert;
  }

  public function get_request_secert(){
    return $this->request_secert;
  }

  public function set_current_min_year($current_min_year){
    $this->current_min_year = $current_min_year;
  }

  public function get_current_min_year(){
    return $this->current_min_year;
  }

  public function set_current_max_year($current_max_year){
    $this->current_max_year = $current_max_year;
  }

  public function get_current_max_year(){
    return $this->current_max_year;
  }


  public function set_has_years($has_years){
    $this->has_years = $has_years;
  }

  public function get_has_years(){
    return $this->has_years;
  }


  public function get_style(){

  }


  public function set_visit_date($visit_date){
    $this->visit_date = $visit_date;
  }

  public function get_visit_date(){
    return $this->visit_date;
  }

  public function set_today_year($today_year){
    $this->today_year = $today_year;
  }

  public function get_today_year(){
    return $this->today_year;
  }

  public function set_today_month($today_month){
    $this->today_month = $today_month;
  }

  public function get_today_month(){
    return $this->today_month;
  }


  public function set_today_day($today_day){
    $this->today_day = $today_day;
  }

  public function get_today_day(){
    return $this->today_day;
  }

  public function set_today_date($today_date){
    $this->today_date = $today_date;
  }

  public function get_today_date(){
    return $this->today_date;
  }

  public function set_today_time($today_time){
    $this->today_time = $today_time;
  }

  public function get_today_time(){
    return $this->today_time;
  }



  public function return_all_years($cal_id){
    if (!is_null($cal_id) && isset($cal_id) && isset($this->year_service)){
      $current_years = $this->year_service->get_all_years_where('cal_id', $cal_id);
      $current_years = is_array($current_years) ? $current_years : array();
      return $current_years;
    } else {
      return array();
    }
  }

  public function return_current_months($year_id){
    if ($year_id && isset($this->month_service)){
      $current_months = $this->month_service->get_all_months_where('year_id', $year_id, 12);
      $current_months = !empty($current_months) ? $current_months : array();
      return $current_months;
    } else {
      return array();
    }
  }

  public function return_current_days($month_id){
    if (!is_null($month_id) && isset($month_id) && isset($this->day_service)){
      $current_days = $this->day_service->get_all_days_where('month_id', $month_id);
      $current_days = !empty($current_days) ? $current_days : array();
      return $current_days;
    } else {
      return array();
    }
  }

  public function isValidTimezoneId($timezoneId) {
    @$tz=timezone_open($timezoneId);
    return $tz!==FALSE;
  }

  public function set_time_zone($time_zone){
    $selected_app_timezone = $this->isValidTimezoneId($time_zone) ? $time_zone : 'Europe/Rome';
    date_default_timezone_set($selected_app_timezone);
    $this->set_visit_date(date("Y-m-d H:i:s"));
    $this->set_today_date(date("Y-m-d"));
    $this->set_today_year(date("Y"));
    $this->set_today_month(date("m"));
    $this->set_today_day(date("d"));
    $this->set_today_time(date("H:i"));
    return true;
  }

  // get year equal to today if not found get first year
  public function return_current_year($cal_id){

    if (!empty($cal_id) && isset($this->today_year) && isset($this->year_service) && isset($this->calendar_service)){
      $today_year = $this->today_year;
      $year_exist = $this->year_service->get_years_where('year', $today_year, '1', 'cal_id', $cal_id);
      if (isset($year_exist) && !empty($year_exist)){
        // today year found and returned
        return $year_exist;
      } else {
        // get min year becuase today year not found in calendar years
        $sql = "SELECT MIN(year), id, year, cal_id FROM `year` WHERE cal_id=".$cal_id." GROUP BY id";
        $min_year = $this->calendar_service->free_single_query($sql);
        if (empty($min_year)){
          return null;
        }
        $min_year = $this->year_service->get_year_object($min_year);
        return $min_year;
      }
    } else {
      $today_year = null;
    }
  }

  public function return_curent_month($year_id, $today_month){

    $today_month = strlen($today_month) > 1 && $today_month[0] == '0' ? substr($today_month,1) : $today_month;
    if (isset($year_id) && isset($today_month) && isset($this->month_service)){
      $current_month = $this->month_service->get_all_months_where('year_id', $year_id, $limit='1', 'month', $today_month);
      if (empty($current_month)){
        return null;
      }
      return $current_month;
    } else {
      return null;
    }
  }

  // this function very important as it will make weeks day start right with monday start required
  // and will give final easy and direct weeks and can later add previous days which not good but can added
  // update to get previous days
  public function monday_start_mange($current_days){
    if (empty($current_days) || !isset($this->day_service)){return array();}
    $result = $current_days;
    // if changed month name take care of this do not change inital names
    $days_index=array(0=>"Monday",1=>"Tuesday",2=>"Wednesday",3=>'Thursday', 4=>'Friday', 5=>'Saturday', 6=>'Sunday');
    $missing_days_at_begin = array_search($current_days[0]->get_day_name(), $days_index);
    $last_id = $current_days[0]->get_id();
    $current_prev = $last_id;
    // so here to solve the calendar known problem where so here will get the old days until it make it start monday
    // or u can disable the day to if u need easy just remove current prev and qury and insert false
    for ($mis=0; $mis<$missing_days_at_begin; $mis++){
      $current_prev--;
      // only one case the day will not found if first year not start at monday but np other solution wait it for example try add unkown id here below
      $get_day = $this->day_service->get_day_by_id($current_prev);
      array_unshift($result, $get_day);
    }
    return $result;
  }



/*
$index_controller->return_current_year($current_calendar->get_id())
    $italy_current_time = date("Y-m-d H:i:s");
echo date("Y");

  }
*/


  public function return_day_periods($day_id){
    if (!isset($this->period_service) || !is_numeric($day_id)){return array();}
    return $this->period_service->get_day_periods($day_id);
  }

  public function return_period_slots($period_id){
    if (!isset($this->slot_service) || !is_numeric($period_id)){return array();}
    $data = $this->slot_service->get_period_slots($period_id);
    return $data;
  }

  public function return_project_data($current_days){
    $projectData = array();
    $view_days = $this->monday_start_mange($current_days);
    for ($d=0; $d<count($view_days); $d++){
      // day data and inital day object same as before nothing changed
      $day_obj = $view_days[$d];
      if (!$day_obj || empty($day_obj)){
        // this happend 1 per live when u select first year which has no monday so it will add empty days to complete the week 7 days begin and end
        continue;
      }
      $day_periods = $this->return_day_periods($day_obj->get_id());
      $day_array = array ('day'=>$day_obj, 'day_data'=>array());
      for ($p=0; $p<count($day_periods); $p++){
        // get periods of day if any cascadse and get it's data and it's slots to
        $current_period = $day_periods[$p];
        $period_slots = $this->return_period_slots($current_period->get_id());
        $period_data = array('day_period'=> $current_period, 'day_slot'=> $period_slots);
        // now no need loops we have slot data
        array_push($day_array['day_data'], $period_data);
      }
      array_push($projectData, $day_array);
    }
    return $projectData;
  }





  // ajax method for load slot data when add new reservation
  public function return_slot_data_ajax($slot_id){

  }

  public function map_periods_slots($periods){

  }


  public function next_month(){

  }

  public function prev_month(){

  }

  // insert reservation It return data for used in AJAX
  public function add_reservation($slot_id, $name='', $notes=''){
    $slot_id = test_input($slot_id);
    $name = test_input($name);
    $notes = test_input($notes);
    if (!isset($this->reservation_service) || !isset($this->slot_service) || !isset($slot_id) || !is_numeric($slot_id)){
      return array('success'=>'false', 'message'=>'Reservation Can not Added Invalid Data Sent.');
    }

    //check slot
    $get_slot = $this->slot_service->get_slot_by_id($slot_id);
    if (!isset($get_slot) || empty($get_slot)){
      return array('success'=>false, 'message'=>'Slot Selected Not Found Reservation Can not Added.');
    }

    $get_reservation = $this->reservation_service->get_reservation_by_slot($slot_id);
    if (!empty($get_reservation)){
      if ($get_slot->get_empty() == 1){
        $update_slot = $this->slot_service->update_one_column('empty', 0, $get_slot->get_id());
      };
      return array('success'=>false, 'message'=>'Reservation Can not Added There are a reservation in that slot The System Has recover and fix the problem.');
    }

    $reservation_id = $this->reservation_service->add($slot_id, $name, $notes, 2);
    if ($reservation_id != false){
        $update_slot = $this->slot_service->update_one_column('empty', 0, $slot_id);
        if (!$update_slot){
          $this->reservation_service->remove($reservation_id);
          return array('success'=>false, 'message'=>'Slot Data Cound not Updated.');
        }
        $get_reservation_added = $this->reservation_service->get_reservation_by_id($reservation_id);
        if (!$get_reservation_added){
          return array('success'=>false, 'message'=>'Reservation created could not found.');
        }

        return array('success'=>true, 'message'=>'Added Reservation Successfully. ID:'.$get_reservation_added->get_id());

      } else {
        return array('success'=>false, 'message'=>'Reservation Can not Added to the system.');
      }
  }



    public function getnerate_request_secert(){
      $token = bin2hex(openssl_random_pseudo_bytes(16));
      $token = isset($token) && !empty($token) ? $token : $token = bin2hex(random_bytes(16));
      return $token;
    }

    public function handle_add_reservation($post_obj, $session_obj, $index_controller_obj){
      $check_request_data = isset($index_controller) &&
      isset($_POST['reservation_slot_id']) && !empty($_POST['reservation_slot_id']) &&
      isset($_POST['secuirty_token']) && !empty($_POST['secuirty_token']) &&
      isset($_POST['reservation_name']) && isset($_POST['reservation_comment']);

      if ($check_request_data){
        return array('success'=>false, 'Can not Add reservation missing required data.');
      }
      /* secuirty */
      $request_token = test_input($post_obj['secuirty_token']);
      $session_token = $session_obj['supcal_token'];

      if (!isset($session_obj['supcal_token']) || empty($session_obj['supcal_token'])){
        return array('success'=>false, 'Access Deined.');
      }
      if ($session_token != $request_token){
        return array('success'=>false, 'You have no premssions to Make this request');
      }
      /* secuirty end */
      $reservation_slot_id = test_input($post_obj['reservation_slot_id']);
      $reservation_name = !empty($post_obj['reservation_name']) ? test_input($post_obj['reservation_name']) : '';
      $reservation_notes = !empty($post_obj['reservation_comment']) ? test_input($post_obj['reservation_comment']) : '';

      $new_reservation = $index_controller_obj->add_reservation($reservation_slot_id, $reservation_name, $reservation_notes);
      return $new_reservation;

  }

  ###################### Post Handler ############################
  public function postHandler($index_controller, $post_obj, $session_obj, $redirect_url, $error){
    $ajax_request = false;

    if ($error){
      $_SESSION['message'] = 'The calendar cannot be executed. The procedure is not set up correctly';
      $_SESSION['success'] = 'false';
      header("Location: " . $redirect_url);
      die();
    }
    // add new reservation
    $is_add_reservation = isset($index_controller) &&
    isset($post_obj['reservation_slot_id']) && !empty($post_obj['reservation_slot_id']) &&
    isset($post_obj['secuirty_token']) && !empty($post_obj['secuirty_token']) &&
    isset($post_obj['reservation_name']) && isset($post_obj['reservation_comment']);

    if ($is_add_reservation){
      $new_reservation = $index_controller->handle_add_reservation($post_obj, $session_obj, $index_controller);
      $_SESSION['message'] = $new_reservation['message'];
      $_SESSION['success'] = $new_reservation['success'];
      header("Location: " . $redirect_url);
      die();
    }

    // this bridge for direct post AJAX requests to AJAX handler
    try{
      $data = json_decode(file_get_contents('php://input'), true);
      $index_controller->ajaxHandler($index_controller, $data);
    }catch(Exception $ex){
       $ajax_request = false;
    }

  }

  public function return_year_data(){
    if (!isset($this->year_service)){return array();}

    $minyear = $this->year_service->get_min_year();
    if (!$minyear){return array();}
    if ($minyear){
      $maxyear = $this->year_service->get_max_year();
    }
    if (
      !isset($minyear['min_year']) || !isset($maxyear['max_year']) ||
      empty($minyear['min_year']) || empty($maxyear['max_year'])
    ){
      return array();
    }
    return array('min_year'=> $minyear['min_year'], 'max_year'=>$maxyear['max_year']);
  }

  ###################### AJAX Handler ############################
  public function ajaxHandler($index_controller, $data){
    /* Map New reservation AJAX start */
    $is_get_periods_and_slots = isset($index_controller) && isset($data['map_reservation_date']) && isset($data['map_cal_id']);
    if ($is_get_periods_and_slots){
      $day_data = array();
      $day_date = test_input($data['map_reservation_date']);
      $selected_calid = test_input($data['map_cal_id']);
      $dayid = $this->day_service->get_dayid_by_date($day_date, $selected_calid);
      if (!isset($dayid) || empty($dayid) || !is_numeric($dayid)){
        $data = array('code'=>400, 'data'=>array(), 'message'=>'Day Date selected Not Avail Calendar Id or Day date invalid Please check other calendar');
        print_r(json_encode($data));
        die();
      }
      $dayperiods = $this->return_day_periods($dayid);
      for ($i=0; $i<count($dayperiods); $i++){
        if (isset($dayperiods[$i]) && !empty($dayperiods[$i])){
          // for secuirty objects data is private and fast for ajax send needed only
          $period_data = array(
            'id'=>$dayperiods[$i]->get_id(),
            'period_title'=>$dayperiods[$i]->get_description(),
            'period_index'=>$dayperiods[$i]->get_period_index()
          );
          $period = array('period'=>$period_data, 'slots'=>array());
          $slots_data = $this->return_period_slots($dayperiods[$i]->get_id());
          for ($s=0; $s<count($slots_data); $s++){
            if (isset($slots_data[$s]) && !empty($slots_data[$s])){
              $slot_data = array(
                'id'=>$slots_data[$s]->get_id(),
                'start_from'=>$slots_data[$s]->get_start_from(),
                'end_at'=>$slots_data[$s]->get_end_at(),
                'slot_index'=>$slots_data[$s]->get_slot_index(),
                'empty'=>$slots_data[$s]->get_empty()
              );
              array_push($period['slots'], $slot_data);
            }
          }
          array_push($day_data, $period);
        }
      }
      $data = array('code'=>200, 'data'=>$day_data, 'message'=>'Successfully Found Data');
      print_r(json_encode($data));
      die();
    }
    /* Map New reservation AJAX end */
  }

  public function get_dayid_by_date($day_date, $cal_id){
    $day_date = test_input($day_date);
    if (!isset($this->day_service) || !empty($day_date)){
      return false;
    }
    return $this->day_service->get_dayid_by_date($day_date, $cal_id);
  }
  ###################### Post Handler End ############################

  // load the ajax data for display periods and slots in selected day when user click + <
  // this will return data to used in add_reservation and add normal reservation
  public function return_day_periods_data($cal_id, $day_date){
  }


}
