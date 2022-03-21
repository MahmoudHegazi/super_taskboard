<?php
ob_start();
require_once (dirname(__FILE__, 2) . '/config.php');
require_once (dirname(__FILE__, 2) . '/functions.php');
require_once (dirname(__FILE__, 2) . '/services/CalendarService.php');
require_once (dirname(__FILE__, 2) . '/services/YearService.php');
require_once (dirname(__FILE__, 2) . '/services/MonthService.php');
require_once (dirname(__FILE__, 2) . '/services/DayService.php');
require_once (dirname(__FILE__, 2) . '/services/PeriodService.php');
require_once (dirname(__FILE__, 2) . '/services/SlotService.php');
require_once (dirname(__FILE__, 2) . '/services/ReservationService.php');
require_once (dirname(__FILE__, 2) . '/services/UserService.php');
require_once (dirname(__FILE__, 2) . '/services/StyleService.php');
require_once (dirname(__FILE__, 2) . '/services/ElementService.php');
require_once (dirname(__FILE__, 2) . '/models/Calendar.php');
require_once (dirname(__FILE__, 2) . '/services/BootstrapContainerService.php');
require_once (dirname(__FILE__, 2) . '/services/BootstrapElementService.php');

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
  protected $element_service;
  protected $bootstrap_container_service;
  protected $bootstrap_element_service;
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
  protected $current_styles;
  // calendar meta data
  // current used cal id usefull
  protected $cal_id;
  protected $used_calendar;
  protected $current_role;
  protected $calid;
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

  protected $used_styles_periods;
  protected $used_styles_slots;

  /* CP style editor */
  protected $calendar_elements;
  protected $element_ids;





  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo, $selected_month=null, $type='GET', $current_year=NULL, $logged_userid=NULL)
  {


    if (is_null($logged_userid)){
      throw new Exception( "You Have No Premssion To access This Page" );
    }
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
    $this->style_service = new StyleService($pdo);
    $this->element_service = new ElementService($pdo);
    $this->bootstrap_container_service = new BootstrapContainerService($pdo);
    $this->bootstrap_element_service = new BootstrapElementService($pdo);

    $used_cal = $this->assign_used_calendar();
    if (!$used_cal || empty($used_cal) || !isset($used_cal) || !isset($this->element_service)){
      // this error in case no used calendar, make sure not to use DB to delete calendars and if u did np but with next add/remove will solved
      throw new Exception( "Sorry, no used calendar found to display Please add a new calendar and it will be marked as used by default..." );
    }
    $this->set_used_calendar($used_cal);
    $this->set_calid($this->set_calid($used_cal->get_id()));
    $this->set_years($this->return_all_years($used_cal->get_id()));
    // you can change time zone from here
    $this->set_time_zone($time_zone='Europe/Rome');

    // current year

    $current_year  = $current_year;
    if (isset($current_year) && !empty($current_year) && !is_null($current_year)){
      $current_year  = $this->year_service->get_year_by_year($current_year, $used_cal->get_id());
    }
    if (empty($current_year) || is_null($current_year)) {
      $current_year = $this->return_current_year($used_cal->get_id());
    }



    if (empty($current_year) || is_null($current_year)){
      throw new Exception( "No years were found for the specified calendar If u can not solve this add years to calendar or delete the calendar and add new one" );
    }
    $this->set_current_year($current_year);



    // current month
    $target_month = !is_null($selected_month) ? $selected_month : intval($this->get_today_month());
    $selected_month = $this->return_curent_month($this->get_current_year()->get_id(), $target_month);
    if (empty($selected_month) || is_null($selected_month)){
      throw new Exception( "We Can not Get the Month Error Error:01" );
    }
    $this->set_current_month($selected_month);
    $current_month = $selected_month;

    // current months
    $current_months = $this->return_current_months($this->get_current_year()->get_id());
    if (empty($current_months) || is_null($current_months)){
      throw new Exception( "We Can not Get the current Month List make sure calendar setup is valid or try with other calendar Error:02" );
    }
    $this->set_current_months($current_months);

    //$current_periods_styles = $this->get_periods_styles($current_year->get_year(), $this->get_current_month()->get_month(), $used_cal->get_id());
    //$current_slots_styles = $this->get_slots_styles($current_year->get_year(), $this->get_current_month()->get_month(), $used_cal->get_id());

    $current_styles =  $this->load_current_styles($current_year->get_year(), $this->get_current_month()->get_month(), $used_cal->get_id());
    $this->set_current_styles($current_styles);


    $current_days = $this->return_current_days($current_month->get_id(), $used_cal->get_id());
    if (!$current_days && empty($current_days)){
      throw new Exception( "We Can not Load the current dats of calendar Error Error:03" );
    }

    $this->set_current_days($current_days);
    //$this->set_current_weeks(array_distribution($this->monday_start_mange($current_days), 7, 5, $default=false));


    $projectData = $this->return_project_data($current_days, $used_cal->get_id());
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


    /*get style ids for better pefromance */
    $elements_data = $this->element_service->read_all_cal_elements($used_cal->get_id());
    if (isset($elements_data['elements_objects'])){
      $this->set_calendar_elements($elements_data['elements_objects']);
    } else {
      $this->set_calendar_elements(array());
    }

    if (isset($elements_data['elements_ids'])){
      $this->set_element_ids($elements_data['elements_ids']);
    } else {
      $this->set_element_ids(array());
    }
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


  public function set_used_styles_periods($used_styles_periods){
    $this->used_styles_periods = $used_styles_periods;
  }

  public function get_used_styles_periods(){
    return $this->used_styles_periods;
  }

  public function set_used_styles_slots($used_styles_slots){
    $this->used_styles_slots = $used_styles_slots;
  }

  public function get_used_styles_slots(){
    return $this->used_styles_slots;
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

  public function set_current_styles($current_styles){
    $this->current_styles = $current_styles;
  }

  public function get_current_styles(){
    return $this->current_styles;
  }

  public function set_calendar_elements($calendar_elements){
    $this->calendar_elements = $calendar_elements;
  }

  public function get_calendar_elements(){
    return $this->calendar_elements;
  }


  public function set_element_ids($element_ids){
    $this->element_ids = $element_ids;
  }

  public function get_element_ids(){
    return $this->element_ids;
  }





  public function return_current_logged_role($loged_uid){
    if (!isset($this->user_service) || empty($this->user_service)){return 'user';}

    $currentuser = $this->user_service->get_user_by_id($loged_uid);
    if (isset($loged_uid) && !empty($loged_uid)){
      return $currentuser->get_role();
    } else {
      return 'user';
    }
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

  public function return_current_days($month_id, $cal_id){

    if (!is_null($month_id) && isset($month_id) && isset($this->day_service)){
      $current_days = $this->day_service->get_all_days_where($cal_id, 'month_id', $month_id);
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

  public function get_day_cal_id($dayid){
    $getcal = $this->day_service->get_day_by_id_force($dayid);
    return $getcal;
  }

  // this function very important as it will make weeks day start right with monday start required
  // and will give final easy and direct weeks and can later add previous days which not good but can added
  // update to get previous days
  public function monday_start_mange($current_days, $cal_id){
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


  public function return_day_periods($day_id, $calid){
    if (!isset($this->period_service) || !is_numeric($day_id)){return array();}
    return $this->period_service->get_day_periods($day_id, $calid);
  }

  public function return_period_slots($period_id){
    if (!isset($this->slot_service) || !is_numeric($period_id)){return array();}
    $data = $this->slot_service->get_period_slots($period_id);
    return $data;
  }

  public function return_project_data($current_days, $cal_id){
    $projectData = array();
    $view_days = $this->monday_start_mange($current_days, $cal_id);
    for ($d=0; $d<count($view_days); $d++){
      // day data and inital day object same as before nothing changed
      $day_obj = $view_days[$d];
      if (!$day_obj || empty($day_obj)){
        // this happend 1 per live when u select first year which has no monday so it will add empty days to complete the week 7 days begin and end
        continue;
      }
      $day_periods = $this->return_day_periods($day_obj->get_id(), $cal_id);
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

  public function get_reservation_data_byslot($slot_id){
    if (!isset($this->reservation_service) || empty($this->reservation_service)){return false;}
    return $this->reservation_service->get_reservation_data_byslot($slot_id);
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
  public function add_reservation($slot_id, $logged_uid, $name='', $notes=''){
    $logged_uid = test_input($logged_uid);
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


    if (!empty($get_reservation)){
      /// herea
      if ($get_slot->get_empty() == 1){
        $update_slot = $this->slot_service->update_one_column('empty', 0, $get_slot->get_id());
      };
      return array('success'=>false, 'message'=>'Reservation Can not Added There are a reservation in that slot The System Has recover and fix the problem.');
    }

    $reservation_id = $this->reservation_service->add($slot_id, $name, $notes, $logged_uid);
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
      $check_request_data = isset($index_controller_obj) &&
      isset($post_obj['reservation_slot_id']) && !empty($post_obj['reservation_slot_id']) &&
      isset($post_obj['secuirty_token']) && !empty($post_obj['secuirty_token']) &&
      isset($post_obj['reservation_name']) && isset($post_obj['reservation_comment']);


      $check_admin_map = isset($post_obj['admin_select_userid_add']) && !empty($post_obj['admin_select_userid_add']) && is_numeric($post_obj['admin_select_userid_add']);
      $check_session_uid = isset($session_obj['logged_id']) && !empty($session_obj['logged_id']);
      $reserv_uid = $check_admin_map ? test_input($post_obj['admin_select_userid_add']) : $check_session_uid ? test_input($session_obj['logged_id']) : 0;


      if (!$check_request_data){
        return array('success'=>false, 'Can not Add reservation missing required data.');
      }

      if (!$reserv_uid && $check_session_uid){
        return array('success'=>false, 'Invalid user id please make sure you logged still logged in refresh page');
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


      $admin_select_user = isset($post_obj['admin_select_userid_add']) && !empty($post_obj['admin_select_userid_add']);
      //print_r($post_obj['admin_select_userid_add']);
      //die();
      // session user id incase no admin
      $session_logged_id = isset($session_obj['logged_id']) && !empty($session_obj['logged_id']) ? test_input($session_obj['logged_id']) : 0;
      $admin_select_uid = isset($post_obj['admin_select_userid_add']) && !empty($post_obj['admin_select_userid_add']) ? test_input($post_obj['admin_select_userid_add']) : 0;

      $logged_userid = $session_logged_id;
      $is_admin_user = $index_controller_obj->return_current_logged_role($session_logged_id) === 'admin';
      if ($is_admin_user && $admin_select_uid){
        $logged_userid = $admin_select_uid;
      } else if (!$is_admin_user && $session_logged_id) {
        $logged_userid = $session_logged_id;
      } else {
        return array('success'=>false, 'Invalid user id and premsions');
      }

      /* secuirty end */
      $reservation_slot_id = test_input($post_obj['reservation_slot_id']);
      $reservation_name = !empty($post_obj['reservation_name']) ? test_input($post_obj['reservation_name']) : '';
      $reservation_notes = !empty($post_obj['reservation_comment']) ? test_input($post_obj['reservation_comment']) : '';
      /* this for admin ads if rule is admin let him select user without effect anything */
      $new_reservation = $index_controller_obj->add_reservation($reservation_slot_id, $logged_userid, $reservation_name, $reservation_notes);
      return $new_reservation;
  }

  ###################### Post Handler ############################
  public function postHandler($index_controller, $post_obj, $session_obj, $redirect_url, $error){
    $ajax_request = false;


    if ($error || !isset($index_controller)){
      $_SESSION['message'] = 'The calendar cannot be executed. The procedure is not set up correctly';
      $_SESSION['success'] = 'false';
      header("Location: " . $redirect_url);
      die();
    }
    if (!isset($session_obj['logged_id']) || empty($session_obj['logged_id'])){
      // if not logged for some reasone eg session end back
      $_SESSION['message'] = 'You Have no access to perform action';
      $_SESSION['success'] = False;
      header("Location: " . $redirect_url);
      die();
    }

    // add new reservation
    $is_add_reservation = isset($post_obj['reservation_slot_id']) && !empty($post_obj['reservation_slot_id']) &&
    isset($post_obj['secuirty_token']) && !empty($post_obj['secuirty_token']) &&
    isset($post_obj['reservation_name']) && isset($post_obj['reservation_comment']);

    if ($is_add_reservation){
      $new_reservation = $index_controller->handle_add_reservation($post_obj, $session_obj, $index_controller);
      $_SESSION['message'] = $new_reservation['message'];
      $_SESSION['success'] = $new_reservation['success'];
      header("Location: " . $redirect_url);
      die();
    }

    // edit reservation
    $is_edit_reservation = isset($post_obj['edit_reservation_id']) && !empty($post_obj['edit_reservation_id']) &&
    isset($post_obj['edit_reservation_name']) && !empty($post_obj['edit_reservation_name']) &&
    isset($post_obj['edit_reservation_comment']) && isset($post_obj['edit_reservation_comment']);
    if ($is_edit_reservation){

      $edit_reservation_data = $this->edit_reservation_handle($index_controller, $post_obj, $session_obj);
      $_SESSION['message'] = $edit_reservation_data['message'];
      $_SESSION['success'] = $edit_reservation_data['success'];
      header("Location: " . $redirect_url);
      die();
    }


    // cancel reservation (Delete By Admin Only remove from db)
    $is_cencel_reservation = isset($index_controller) &&
    isset($post_obj['cancel_reservation_id']) && !empty($post_obj['cancel_reservation_id']) &&
    isset($post_obj['cancel_reservation_slotid']) && !empty($post_obj['cancel_reservation_slotid']);

    if ($is_cencel_reservation){
      $cancel_reservation_data = $this->cancel_reservation_handle($index_controller, $post_obj, $session_obj);
      $_SESSION['message'] = $cancel_reservation_data['message'];
      $_SESSION['success'] = $cancel_reservation_data['success'];
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

      if (!isset($selected_calid) || empty($selected_calid) || !is_numeric($selected_calid)){
        $data = array('code'=>404, 'data'=>array(), 'message'=>'No calendar Id provided in request make sure you not deleted all calendars from setup and not restart the page yet');
        print_r(json_encode($data));
        die();
      }

      $dayperiods = $this->return_day_periods($dayid, $selected_calid);
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

    /* style setup container Bootstrap 5 1 */
    $is_add_container = isset($index_controller) && isset($data['setup_containers']);
    if ($is_add_container){
      if (empty($data['setup_containers'])){
        print_r(
          json_encode(array('code'=> 400, 'message'=> 'can not setup containers bad request'))
        );
        die();
      }
      // setup containers
      $arrayphp = array($data['setup_containers']);
      $total_updated_classes = 0;

      $containerdata = count($arrayphp) > 0 ? $arrayphp[0] : array();
      for ($bsCont=0; $bsCont<count($containerdata); $bsCont++){
        $dataRow = $containerdata[$bsCont];
        if (
          isset($dataRow['element_id']) && !empty($dataRow['element_id']) &&
          isset($dataRow['html_class']) && !empty($dataRow['html_class']) &&
          isset($dataRow['c_cal_id']) && !empty($dataRow['c_cal_id'])
          )
          {




          $elmid = test_input($dataRow['element_id']);
          $elmclass = test_input($dataRow['html_class']);
          $c_cal_id = test_input($dataRow['c_cal_id']);

          if (!isset($c_cal_id) || empty($c_cal_id)){
            print_r(
              json_encode(array('code'=> 404, 'message'=> 'Sorry no Calendar exist you may deleted it from setup page'))
            );
            die();
          }

          $data_group = isset($dataRow['data_group']) && !empty($dataRow['data_group']) ? test_input($dataRow['data_group']) : NULL;
          $is_elmexist = $index_controller->element_service->getElement($elmid, $type='container');
          if (empty($is_elmexist) || $is_elmexist == false){
            $new_element_id = $index_controller->element_service->add($elmid, $elmclass, $c_cal_id, 'container', $default_bootstrap='', $default_style = '', $group=$data_group, $bootstrap_classes='', $innerHTML=NULL, $innerText=NULL, $data=NULL);
            $new_bootstrap_containerid = $index_controller->bootstrap_container_service->add(
              $new_element_id, $c_cal_id, $bg='', $text_color='', $p='', $m='', $border='', $border_size='', $border_color='', $border_round='', $width='', $height='',
              $m_t='', $m_b='', $m_r='', $m_l='', $p_t='', $p_b='', $p_r='', $p_l='', $visibility='', $box_shadow='', $justify_content='justify-content-center', $align_items='align-items-center',
              $ratio='', $flex_flow='', $flex_type='d-flex', $flex_wrap='', $align_content='align-content-center'
            );
            if (!$new_element_id){
              print_r(
                json_encode(array('code'=> 200, 'message'=> $new_element_id, 'total'=>$total_updated_classes))
              );
              die();
            }
            /* this way I made very large complcated table work very smooth and add and edit and remove then I update 1 single column
            in the element and only query with this so it very good for performance and also will not use the large table only in update or delete and later update the element bootclass +
             it give u 1 column contain values in large table this easy but imagine u have 100 column how speed will this reduce */
            $elm_bootstrap_class = $index_controller->bootstrap_container_service->get_bootstrap_classes_by_element($new_element_id);
            $bs_contid = $index_controller->element_service->update_one_column('bootstrap_classes', $elm_bootstrap_class, $new_element_id);
            $total_updated_classes += $bs_contid ? 1 : 0;
          }
        }
      }
      print_r(
        json_encode(array('code'=> 200, 'message'=> 'setup done', 'total'=>$total_updated_classes))
      );
      die();
    }
    // ende setup element
    $is_add_element = isset($index_controller) && isset($data['setup_elements']);
    if ($is_add_element){
      if (empty($data['setup_elements'])){
        print_r(
          json_encode(array('code'=> 400, 'message'=> 'can not setup elements bad request'))
        );
        die();
      }

      // setup elements
      $elements_ar = array($data['setup_elements']);
      $total_updated_classes_elm = 0;
      $elementsdata = count($elements_ar) > 0 ? $elements_ar[0] : array();

      for ($bsElm=0; $bsElm<count($elementsdata); $bsElm++){
        $elmRow = $elementsdata[$bsElm];
        if (
          isset($elmRow['element_id']) && !empty($elmRow['element_id']) &&
          isset($elmRow['html_class']) && !empty($elmRow['html_class']) &&
          isset($elmRow['c_cal_id']) && !empty($elmRow['c_cal_id'])
          )
          {

            $e_elmid = test_input($elmRow['element_id']);
            $e_elmclass = test_input($elmRow['html_class']);
            $e_c_cal_id = test_input($elmRow['c_cal_id']);
            if (!isset($e_c_cal_id) || empty($e_c_cal_id)){
              print_r(
                json_encode(array('code'=> 404, 'message'=> 'Sorry no Calendar exist you may deleted it from setup page'))
              );
              die();
            }
            $e_data_group = isset($elmRow['data_group']) && !empty($elmRow['data_group']) ? test_input($elmRow['data_group']) : NULL;
            $e_is_elmexist = $index_controller->element_service->getElement($e_elmid, $type='element');
            if (empty($e_is_elmexist) || $e_is_elmexist == false){
              $e_newelm_id = $index_controller->element_service->add($e_elmid, $e_elmclass, $e_c_cal_id, 'element', $default_bootstrap='', $default_style = '', $group=$e_data_group, $bootstrap_classes='', $innerHTML=NULL, $innerText=NULL, $data=NULL);
              try {
                // note you can add default styles for any element in the app from here if
                $new_bs_elmid = $index_controller->bootstrap_element_service->add(
                  $e_newelm_id, $e_c_cal_id, $bg='', $text_color='', $p='', $m='', $border='', $border_size='', $border_color='', $border_round='', $width='', $height='',
                  $m_t='', $m_b='', $m_r='', $m_l='', $p_t='', $p_b='', $p_r='', $p_l='', $visibility='', $box_shadow='', $flex_fill='', $flex_grow='', $ms_auto='',
                  $flex_order='', $vertical_align='', $col_sm='', $h='', $display='', $text_wrap='', $font_weight='', $text_case='', $badge='',
                  $float_position='', $text_align='text-center', $text_break='', $center_content=''
                );
                $elm_bootstrap_class_e = $index_controller->bootstrap_element_service->get_bootstrap_classes_by_element($e_newelm_id);
                $updateds = $index_controller->element_service->update_one_column('bootstrap_classes', $elm_bootstrap_class_e, $e_newelm_id);
                $total_updated_classes_elm += $updateds ? 1 : 0;
              } catch(Exception $e){
                print_r(
                  json_encode(array('code'=> 404, 'message'=> json_encode($e->getMessage())))
                );
                die();
              }
            }
          }
        }
        print_r(
          json_encode(array('code'=> 200, 'message'=> 'done setup elements', 'total'=>$total_updated_classes_elm))
        );
        die();
    }

    // load bs continaers styles
    $is_add_bs_container = isset($index_controller) && isset($data['bs_container_load_elmid']);
    if ($is_add_bs_container){
      $selectors_elmid = test_input($data['bs_container_load_elmid']);
      $bs_data = $index_controller->loadContainerBSData($selectors_elmid);
      if (isset($bs_data) && !empty($bs_data)){
        print_r(json_encode(array('code'=>200, 'data'=>$bs_data)));
        die();
      } else {
        print_r(json_encode(array('code'=>404, 'data'=>$bs_data, 'message'=>$selectors_elmid)));
        die();
      }
    }

    // load bs element styles classes
    $is_add_bs_element = isset($index_controller) && isset($data['bs_element_load_elmid']);
    if ($is_add_bs_element){
      $selectors_elmid = test_input($data['bs_element_load_elmid']);
      $bs_data = $index_controller->loadElementBSData($selectors_elmid);
      if (isset($bs_data) && !empty($bs_data)){
        print_r(json_encode(array('code'=>200, 'data'=>$bs_data)));
        die();
      } else {
        print_r(json_encode(array('code'=>404, 'data'=>$bs_data, 'message'=>$selectors_elmid)));
        die();
      }
    }

    // update bs styles
    $is_updatebs_style = isset($index_controller) && isset($data['updateBsId']) && isset($data['updateBsname']) && isset($data['updateBSvalue']);
    if ($is_updatebs_style){
      if (empty($data['updateBsId']) || empty($data['updateBsname'])){
        print_r(json_encode(array('code'=>400, 'data'=>array(), 'message'=>'can not update style missing Name Or Id of current element')));
        die();
      }
      $bsid = test_input($data['updateBsId']);
      $bs_column = test_input($data['updateBsname']);
      $bs_value = empty($data['updateBSvalue']) ? '' : test_input($data['updateBSvalue']);
      $elm_id = $index_controller->bootstrap_container_service->get_bs_element_id($bsid);
      if (!$elm_id || empty($elm_id)){
        print_r(json_encode(array('code'=>404, 'data'=>array() , 'message'=>'element not found please install it')));
        die();
      }



      // check group update so u can update one element or apply style on all same group also u can control the groups depend on template used   ver9.1
      $group_status = (isset($data['updateBSGroupStatus']) && !empty($data['updateBSGroupStatus'])) ? test_input($data['updateBSGroupStatus']) : 'off';
      $group_index = (isset($data['updateBSGroup']) && !empty($data['updateBSGroup'])) ? test_input($data['updateBSGroup']) : '';

      $applyongroup = false;
      if ($group_status == 'on' && !empty($group_index) && $group_index != ''){
        $applyongroup = true;
      }


      // check group update so u can update one element or apply style on all same group also u can control the groups depend on template used1



      // this method help to validate the column name as this not normal handle column by column it dynamic  and give u best dynamic to add new easy column
      $valid_column_name = $index_controller->bootstrap_container_service->is_valid_key($bs_column);
      if ($valid_column_name == false){
        print_r(json_encode(array('code'=>400, 'data'=>array() , 'message'=>'The select column name provided unkown by the system please add it to database')));
        die();
      }
      // this method to complete the dynamic and handle errors u maybe decide add new bootstrap options and not know rules so it handle everything also this help the core function which update unkown columns with unkown unum values without do it randomly it cover evey coomon issues and keep dynamic
      $valid_enum_value = $index_controller->bootstrap_container_service->is_valid_column_enum_value($bs_column, $bs_value);
      if (!$valid_enum_value){
        print_r(json_encode(array('code'=>400, 'data'=>array() , 'message'=>'The select option value provided unkown by the system please add it to the column enum values but you must know BS5 if unkown bs nothing happends.')));
        die();
      }

      // update one or group


      $old = $index_controller->bootstrap_container_service->get_column_value($bs_column, $bsid);
      $update_col = false;
      $updated_group = false;
      if ($applyongroup){
        $updated_group = $index_controller->bootstrap_container_service->update_bs_by_contgroup($bs_column, $bs_value, $group_index);
      } else {
        $update_col = $index_controller->bootstrap_container_service->update_one_column($bs_column, $bs_value, $bsid);
      }

      if ($update_col){
        // this is how the idea built on big table with simple async with string column
        $asyncd_elm = $index_controller->async_element_bs($index_controller, $elm_id, 'container');
        if (empty($asyncd_elm)){
          print_r(json_encode(array('code'=>400, 'data'=>array() , 'message'=>'The select option value provided unkown by the system please add it to the column enum values but you must know BS5 if unkown bs nothing happends.')));
          die();
        }else {
          $message = 'Updated ' . $bs_column . 'Successfully';
          print_r(json_encode(
              array('code'=>200,
              'data'=>array(
                'new'=>$bs_value,
                'old'=>$old,
                'bsid'=>$bsid,
                'group_on'=>false,
                'group'=>'',
                'col'=>$bs_column
              ),'message'=>$message
            )));
          die();
        }
      } else if ($updated_group && $updated_group) {
        // update the group
        $asyncd_elements_by_group = $index_controller->async_element_bs_by_group($index_controller, $group_index, 'container');
        if (empty($asyncd_elements_by_group)){
          print_r(json_encode(array('code'=>400, 'data'=>array() , 'message'=>'The select option value provided unkown by the system please add it to the column enum values but you must know BS5 if unkown bs nothing happends.')));
          die();
        } else {
          $message = 'Updated Container Group: ' . $group_index . ' Class: ' . $bs_column . ' Successfully';
          print_r(json_encode(
              array('code'=>200,
              'data'=>array(
                'new'=>$bs_value,
                'old'=>$old,
                'bsid'=>$bsid,
                'group_on'=>true,
                'group'=>$group_index,
                'col'=>$bs_column
              ),'message'=>$message
            )));
          die();
        }
      } else {
        print_r(json_encode(array('code'=>422, 'data'=>array('new'=>'', 'old'=>'') , 'message'=>'unkown error happend' . $updated_group && $updated_group)));
        die();
      }
    }

    // update bs element style classes
    $is_updatebs_style = isset($index_controller) && isset($data['updateElmBsId']) && isset($data['updateElmBsname']) && isset($data['updateElmBSvalue']);
    if ($is_updatebs_style){

      // check group update so u can update one element or apply style on all same group also u can control the groups depend on template used   ver9.1
      $group_status = (isset($data['updateElmGroupStatus']) && !empty($data['updateElmGroupStatus'])) ? test_input($data['updateElmGroupStatus']) : 'off';
      $group_index = (isset($data['updateElmGroup']) && !empty($data['updateElmGroup'])) ? test_input($data['updateElmGroup']) : '';

      $applyongroup = false;
      if ($group_status == 'on' && !empty($group_index) && $group_index != ''){
        $applyongroup = true;
      }


      // check group update so u can update one element or apply style on all same group also u can control the groups depend on template used1


      if (empty($data['updateElmBsId']) || empty($data['updateElmBsname'])){
        print_r(json_encode(array('code'=>400, 'data'=>array(), 'message'=>'can not update style missing Name Or Id of current element')));
        die();
      }

      $bsid = test_input($data['updateElmBsId']);
      $bs_column = test_input($data['updateElmBsname']);
      $bs_value = empty($data['updateElmBSvalue']) ? '' : test_input($data['updateElmBSvalue']);
      $elm_id = $index_controller->bootstrap_element_service->get_bs_element_id($bsid);
      if (!$elm_id || empty($elm_id)){
        print_r(json_encode(array('code'=>404, 'data'=>array() , 'message'=>'element not found please install it')));
        die();
      }

      // this method help to validate the column name as this not normal handle column by column it dynamic  and give u best dynamic to add new easy column
      $valid_column_name = $index_controller->bootstrap_element_service->is_valid_key($bs_column);
      if ($valid_column_name == false){
        print_r(json_encode(array('code'=>400, 'data'=>array() , 'message'=>'The select column name provided unkown by the system please add it to database')));
        die();
      }
      // this method to complete the dynamic and handle errors u maybe decide add new bootstrap options and not know rules so it handle everything also this help the core function which update unkown columns with unkown unum values without do it randomly it cover evey coomon issues and keep dynamic
      $valid_enum_value = $index_controller->bootstrap_element_service->is_valid_column_enum_value($bs_column, $bs_value);
      if (!$valid_enum_value){
        print_r(json_encode(array('code'=>400, 'data'=>array() , 'message'=>'The select option value provided unkown by the system please add it to the column enum values but you must know BS5 if unkown bs nothing happends.')));
        die();
      }


      $old = $index_controller->bootstrap_element_service->get_column_value($bs_column, $bsid);

      $update_col = false;
      $updated_group = false;
      if ($applyongroup){
        $updated_group = $index_controller->bootstrap_element_service->update_bs_by_elmgroup($bs_column, $bs_value, $group_index);
      } else {
        $update_col = $index_controller->bootstrap_element_service->update_one_column($bs_column, $bs_value, $bsid);
      }

      if ($update_col){
        // this is how the idea built on big table with simple async with string column
        $asyncd_elm = $index_controller->async_element_bs($index_controller, $elm_id, 'element');
        if (empty($asyncd_elm)){
          print_r(json_encode(array('code'=>400, 'data'=>array() , 'message'=>'The select option value provided unkown by the system please add it to the column enum values but you must know BS5 if unkown bs nothing happends.')));
          die();
        } else {
          $message = 'Updated ' . $bs_column . 'Successfully';
          print_r(json_encode(
              array('code'=>200,
              'data'=>array(
                'new'=>$bs_value,
                'old'=>$old,
                'bsid'=>$bsid,
                'group_on'=>false,
                'group'=>'',
                'col'=>$bs_column
              ),'message'=>$message
            )));
          die();
        }
      } else if ($updated_group && $group_index) {
        // update elements by group
        $asyncd_elms = $index_controller->async_element_bs_by_group($index_controller, $group_index, 'element');
        if (empty($asyncd_elms)){
          print_r(json_encode(array('code'=>400, 'data'=>array() , 'message'=>'The select option value provided unkown by the system please add it to the column enum values but you must know BS5 if unkown bs nothing happends.')));
          die();
        } else {
          $message = 'Updated Elements Group: ' . $group_index . ' Class: ' . $bs_column . ' Successfully';
          print_r(json_encode(
              array('code'=>200,
              'data'=>array(
                'new'=>$bs_value,
                'old'=>$old,
                'bsid'=>$bsid,
                'group_on'=>true,
                'group'=>$group_index,
                'col'=>$bs_column
              ),'message'=>$message
            )));
          die();
        }
      } else {
        print_r(json_encode(array('code'=>422, 'data'=>array('new'=>'', 'old'=>'') , 'message'=>'unkown error happend')));
        die();
      }
    }

    // update element style
    $is_update_element_bgcolor = isset($index_controller) && isset($data['styleUpdateElmid']) && isset($data['styleUpdateGroupOn']) && isset($data['styleUpdateGroup']) && isset($data['styleUpdatebg']);
    if ($is_update_element_bgcolor){
      $newbg_color = !empty($data['styleUpdatebg']) ? test_input($data['styleUpdatebg']) : 0;
      if (!$newbg_color){
        print_r(json_encode(array('code'=> 400, 'message'=> 'No color found', 'data'=>array())));
        die();
      }

      $elmid = !empty($data['styleUpdateElmid']) ? test_input($data['styleUpdateElmid']) : false;
      $group_on = !empty($data['styleUpdateGroupOn']) ? test_input($data['styleUpdateGroupOn']) : false;
      $group = !empty($data['styleUpdateGroup']) ? test_input($data['styleUpdateGroup']) : false;
      if (!$elmid && !$group_on && !$group){
        print_r(json_encode(array('code'=> 400, 'message'=> 'Element Data not provided', 'data'=>array())));
        die();
      }
      if (!empty($group_on) && !empty($group) && $group_on != false){
        $update_elements = $this->element_service->update_elements_by_group('default_style', $newbg_color, $group);
        if ($update_elements){
          print_r(json_encode(array('code'=> 200, 'message'=> 'Updated elements background', 'data'=>array(
            'group'=>$group,
            'group_on'=>$group_on,
            'bg'=>$newbg_color,
            'id'=>$elmid
          ))));
          die();
        } else {
          print_r(json_encode(array('code'=> 422, 'message'=> 'Elements bg could not updated', 'data'=>array())));
          die();
        }
        // update bg with group
      } else if (!empty($elmid) && !empty($newbg_color) && (empty($group_on) || empty($group))) {
        $update_element = $this->element_service->update_one_column('default_style', $newbg_color, $elmid);
        if ($update_element){
          print_r(json_encode(array('code'=> 200, 'message'=> 'Updated element background', 'data'=>array(
            'group'=>$group,
            'group_on'=>$group_on,
            'bg'=>$newbg_color,
            'id'=>$elmid
          ))));
          die();
        } else {
          print_r(json_encode(array('code'=> 422, 'message'=> 'Element bg could not updated', 'data'=>array())));
          die();
        }
        // update single element
      }
    }

    $is_back_all_bs_default = isset($index_controller) && isset($data['backbs_default_id']);
    if ($is_back_all_bs_default){
      $cal_id = test_input($data['backbs_default_id']);
      if (empty($cal_id)){
        print_r(
          json_encode(array('code'=> 400, 'message'=> 'no calendar id sent'))
        );
        die();
      } else {
        $deleted_cal_elms = $index_controller->element_service->delete_all_cal_elements($cal_id);
        if ($deleted_cal_elms){
          print_r(
            json_encode(array('code'=> 200, 'message'=> 'Calendar bootstrap classes have been successfully reset'))
          );
          die();
        } else {
          print_r(
            json_encode(array('code'=> 422, 'message'=> 'Unable to reset bootstrap style classes To HTML elements found installed in system Try to install the elements first then try again.'))
          );
          die();
        }
      }
    }



    print_r(
      json_encode(array('code'=> 422, 'message'=> 'unkown request'))
    );
    die();
    /* style setup container end */
  }

  public function get_dayid_by_date($day_date, $cal_id){
    $day_date = test_input($day_date);
    if (!isset($this->day_service) || !empty($day_date)){
      return false;
    }
    return $this->day_service->get_dayid_by_date($day_date, $cal_id);
  }
  ###################### Post Handler End ############################

  // Cancel reservation handle


  public function cancel_reservation_handle($index_controller, $post_obj, $session_obj){

      $result = array('success'=>False, 'message'=>'');
      if (!isset($index_controller->reservation_service) || !isset($index_controller->slot_service)){
        return $result;
      }
      $cancel_reservation_id = test_input($post_obj['cancel_reservation_id']);
      $cancel_slot_id = test_input($post_obj['cancel_reservation_slotid']);
      $logged_id = $session_obj['logged_id'];
      $is_admin_user = $index_controller->return_current_logged_role($logged_id) === 'admin';

      $reservation = $index_controller->reservation_service->get_reservation_by_id($cancel_reservation_id);
      if (!isset($reservation) || empty($reservation)){
        $result = array('success'=>False, 'message'=>'Reservation Not Found Or Deleted.');
        return $result;
      }
      $is_owned_reserv = $logged_id === $reservation->get_user_id();
      if ($is_owned_reserv || $is_admin_user){
        $update_slot = $index_controller->slot_service->update_one_column('empty', 1, $cancel_slot_id);
        $remove_reservation = $index_controller->reservation_service->remove($cancel_reservation_id);
        if ($update_slot && $remove_reservation){
          $result = array('success'=>True, 'message'=>'Reservation Canceled Successfully');
          return $result;
        } else {
          $result = array('success'=>False, 'message'=>'Can not Cancel Reservation');
          return $result;
        }
      } else {
        $result = array('success'=>False, 'message'=>'You Have No Premssions To Remove this Reservation');
      }
    return $result;
  }

  public function edit_reservation_handle($index_controller, $post_obj, $session_obj){

      $result = array('success'=>False, 'message'=>'');
      if (!isset($index_controller->reservation_service) || !isset($index_controller->slot_service)){
        return $result;
      }
      $edit_reservation_id = test_input($post_obj['edit_reservation_id']);
      $req_reservation_name = test_input($post_obj['edit_reservation_name']);
      $req_reservation_notes = test_input($post_obj['edit_reservation_comment']);
      $logged_id = $session_obj['logged_id'];
      $is_admin_user = $index_controller->return_current_logged_role($logged_id) === 'admin';


      $reservation = $index_controller->reservation_service->get_reservation_by_id($edit_reservation_id);
      if (!isset($reservation) || empty($reservation)){
        $result = array('success'=>False, 'message'=>'Reservation Not Found Or Deleted.');
        return $result;
      }

      $is_owned_reserv = $logged_id === $reservation->get_user_id();
      if ($is_owned_reserv || $is_admin_user){
        if ($reservation->get_name() != $req_reservation_name){
          $update_reservation_name = $index_controller->reservation_service->update_one_column('name', $req_reservation_name, $edit_reservation_id);
          $result['message'] = 'Name';
          $result['success'] = True;
        }
        if ($reservation->get_notes() != $req_reservation_notes){
          $update_reservation_name = $index_controller->reservation_service->update_one_column('notes', $req_reservation_notes, $edit_reservation_id);
          $result['message'] .= empty($updated_text) ? 'notes' : $result['message'] . ', notes';
          $result['success'] = True;
        }

        if (!empty($result['message'])){
          $result['message'] = 'Updates Successfully ' . $result['message'];
        } else {
          $result['message'] = 'No Changes detected';
        }
      } else{
        $result = array('success'=>False, 'message'=>'You Have no access To edit this reservation');
      }
    return $result;

  }

  public function return_users_public_data(){
    if (!isset($this->user_service) || empty($this->user_service)) {return array();}
    $all_users = $this->user_service->read_all_public();
    if (isset($all_users) && !empty($all_users) && is_array($all_users)){
      return $all_users;
    } else {
      return array();
    }
  }

  public function return_public_user_data($user_id){
    if (!isset($this->user_service) || empty($this->user_service)) {return array();}
    $user = $this->user_service->read_one_public($user_id);
    if (isset($user) && !empty($user)){
      return $user;
    } else {
      return array();
    }
  }

  /* styles */
  public function get_periods_styles($year, $month, $cal_id){
    if (isset($this->style_service) || !empty($this->style_service)){
      return $this->style_service->read_class_styles($year, $month, $cal_id, 'period');
    } else {
      return array();
    }
  }

  public function get_slots_styles($year, $month, $cal_id){
    if (isset($this->style_service) || !empty($this->style_service)){
      return $this->style_service->read_class_styles($year, $month, $cal_id, 'slot');
    } else {
      return array();
    }
  }
/*
SELECT * FROM style JOIN period ON style.class_id=period.id JOIN day ON period.day_id=day.id JOIN month ON month.id = day.month_id JOIN year ON year.id = month.year_id JOIN calendar ON calendar.id = year.cal_id WHERE style.cal_id=289 AND style.custom=0 AND period.period_index = 2

UPDATE style style JOIN period ON style.class_id=period.id JOIN day ON period.day_id=day.id JOIN month ON month.id = day.month_id JOIN year ON year.id = month.year_id JOIN calendar ON calendar.id = year.cal_id set style = 'background: red !important;' WHERE style.cal_id=289 AND style.custom=0 AND period.period_index = 2
*/

  // this part is very important and by changing it u change the app optiobs and get diffrent good results for example which heighest proity style and how styles loaded is fast or load everything also if needed cache method will writen here ex after full styles edites u need create small css file and not load from db
  // you have to study this app in order to make good calendars
  public function load_current_styles($year, $month, $cal_id){
    $this->set_used_styles_periods(array());
    $this->set_used_styles_slots(array());
    $used_periods = $this->get_used_styles_periods();
    $used_slots = $this->get_used_styles_slots();

    $current_periods_styles = $this->get_periods_styles($year, $month, $cal_id);
    $current_slots_styles = $this->get_slots_styles($year, $month, $cal_id);
    $styles = '';
    for ($pindex=0; $pindex<count($current_periods_styles); $pindex++){
      $style_role = $current_periods_styles[$pindex]->get_style();
      $classname = $current_periods_styles[$pindex]->get_classname();
      $element_id = $current_periods_styles[$pindex]->get_element_id();
      $custom = $current_periods_styles[$pindex]->get_custom();

      if (!empty($style_role) && $custom == 0){
        $important_check = strpos($style_role, '!important') !== false;
        if (!$important_check){
          // here can override css Specificity I can ignore important or control it or change parts of style editors like main styles override everything and custom style more stronger than main becuase it use id so if u used important now u can override the main but still smaller than bs which is after main (color has focus )
          $style_role = substr($style_role, 0, -1) . ' !important;';
        }

        $style_block = '
        .' . $classname . '{' . $style_role . '}
        ';
        if (in_array($style_block, $used_periods)){
          continue;
        }
        array_push($used_periods, $style_block);

        $styles .= $style_block;
      } else if (!empty($style_role) && $custom == 1){

        $important_check = strpos($style_role, '!important') !== false;
        if (!$important_check){
          // better to add important here but leave for css users
          $style_role = substr($style_role, 0, -1) . '';
        }

        $style_block = '
        #' . $element_id . '{' . $style_role . '}
          ';
        if (in_array($style_block, $used_periods)){
          continue;
        }
        array_push($used_periods, $style_block);

        $styles .= $style_block;
      } else {
        continue;
      }
    }
    for ($sindex=0; $sindex<count($current_slots_styles); $sindex++){
      $style_role = $current_slots_styles[$sindex]->get_style();
      array_push($used_slots, $style_role);
      $classname = $current_slots_styles[$sindex]->get_classname();
      $element_id = $current_slots_styles[$sindex]->get_element_id();
      $custom = $current_slots_styles[$sindex]->get_custom();
      if (!empty($style_role) && $custom == 0){
        $important_check1 = strpos($style_role, '!important') !== false;
        if (!$important_check1){
          $style_role = substr($style_role, 0, -1) . ' !important;';
        }

        $style_block =  '
        .' . $classname . '{' . $style_role . '}
        ';
        // custom for call flex desgin control color of slots (note this style for slots and periods only)
        if (strpos($style_role, 'color:') !== false && strpos($element_id, 'slot') !== false){
          $style_block .=  '
          .' . $classname . ' * {' . $style_role . '}
          ';
        }

        if (in_array($style_block, $used_slots)){
          continue;
        } else {
          array_push($used_slots, $style_block);
        }


        $styles .= $style_block;
      } else if (!empty($style_role) && $custom == 1){

        $style_block = '
        #' . $element_id . '{' . $style_role . '}
          ';

        if (in_array($style_block, $used_slots)){
          continue;
        } else {
          array_push($used_slots, $style_block);
        }

        $styles .= $style_block;
      } else {
        continue;
      }
    }
    return $styles;
  }
  // load the ajax data for display periods and slots in selected day when user click + <
  // this will return data to used in add_reservation and add normal reservation
  public function return_day_periods_data($cal_id, $day_date){
  }

  /* style editor */
  public function get_element_classes($cal_elements, $elm_id, $cal_id, $default_classes){
    if (!isset($cal_elements) || empty($cal_elements)){
      echo $default_classes;
      return 'no';
    } else {
      if (!isset($cal_elements[$elm_id]) || empty($cal_elements[$elm_id])){
        echo $default_classes;
        return 'no';
      } else {
        echo $cal_elements[$elm_id]->get_bootstrap_classes();
        return 'yes';
      }
    }
  }

  public function getElement($element_id, $type='container'){
    $used_cal = $this->calendar_service->get_used_calendar('used', 1);
    $calid = isset($used_cal) && !empty($used_cal) ? $used_cal->get_id() : 0;
    if (!$calid){return false;}

    $element = $this->element_service->getElement($element_id, $type);
    if (!isset($element) || empty($element)){
      return false;
    } else {
      return $element;
    }
  }

  public function getElementId($element_id, $type='container'){
    $element = $this->element_service->getElementId($element_id, $type);
    if (!isset($element) || empty($element)){
      return false;
    } else {
      return $element;
    }
  }



  public function getBsElementId($element_id, $type){

    if ($type == 'container'){
      $containerid = $this->getElementId($element_id, $type);
      if (isset($containerid) && !empty($containerid)){
        $bsContainerid = $this->bootstrap_container_service->get_bscontainerid_by_element($containerid);
        return $bsContainerid;
      } else {
        return false;
      }
    } else {
      $containerid = $this->getElementId($element_id, $type);
      if (isset($containerid) && !empty($containerid)){
        $bsElementid = $this->bootstrap_element_service->get_bselm_id_by_element($containerid);
        return $bsElementid;
      } else {
        return false;
      }
    }
  }

  public function getBsId($inC, $element_id, $type){
    $bsElementId = $inC->getBsElementId($element_id, $type);
    if (isset($bsElementId) && !empty($bsElementId)){
      return 'data-bs-id="' . $bsElementId . '"';
    } else {
      return '';
    }
  }

  public function loadContainerBSData($bs_id){
    $bs_container = $this->bootstrap_container_service->get_public_bs_container_by_id($bs_id);
    if (!isset($bs_container) || empty($bs_container)){
      return array();
    }
    return $bs_container;
  }

  public function loadElementBSData($bs_id){
    $bs_element = $this->bootstrap_element_service->get_public_bs_element_by_id($bs_id);
    if (!isset($bs_element) || empty($bs_element)){
      return array();
    }
    return $bs_element;
  }

  // ver9.1
  public function async_element_bs($index_controller, $elm_id, $type='container'){
    if ($type == 'element'){
      $bootstrap_class_string = $index_controller->bootstrap_element_service->get_bootstrap_classes_by_element($elm_id);
      $updated = $index_controller->element_service->update_one_column('bootstrap_classes', $bootstrap_class_string, $elm_id);
      return $updated;
    } else {
      $bootstrap_class_string = $index_controller->bootstrap_container_service->get_bootstrap_classes_by_element($elm_id);
      $updated = $index_controller->element_service->update_one_column('bootstrap_classes', $bootstrap_class_string, $elm_id);
      return $updated;
    }
  }

  public function async_element_bs_by_group($index_controller, $data_group, $type='element'){
    $updated = false;
    $all_group_elm = $index_controller->element_service->get_elements_ids_where('data_group', $data_group);
    if ($type == 'element'){
      if (isset($all_group_elm) && !empty($all_group_elm)){
        for ($elmi=0; $elmi<count($all_group_elm); $elmi++){
          $elm_id = $all_group_elm[$elmi];
          if (isset($elm_id) && !empty($elm_id)){
            $bootstrap_class_string = $index_controller->bootstrap_element_service->get_bootstrap_classes_by_element($elm_id);
            $updated = $index_controller->element_service->update_one_column('bootstrap_classes', $bootstrap_class_string, $elm_id);
          }
        }
      }
      return $updated;
    } else {
      if (isset($all_group_elm) && !empty($all_group_elm)){
        for ($elmi=0; $elmi<count($all_group_elm); $elmi++){
          $elm_id = $all_group_elm[$elmi];
          if (isset($elm_id) && !empty($elm_id)){
            $bootstrap_class_string = $index_controller->bootstrap_container_service->get_bootstrap_classes_by_element($elm_id);
            $updated = $index_controller->element_service->update_one_column('bootstrap_classes', $bootstrap_class_string, $elm_id);
          }
        }
      }
      return $updated;
    }
  }


  public function load_element_style($elm_id, $default=''){
    $default = !empty($default) ? $default . ';' : '';
    if (!isset($this->element_service)){
      echo '';
      return false;
    }
    $elm_styles = $this->element_service->get_element_styles($elm_id);
    if ($elm_styles && isset($elm_styles) && !empty($elm_styles)){
      echo 'style="'. $elm_styles . ';' . $default . '"';
      return true;
    } else {
      if (empty($default)){
        echo '';
        return false;
      } else {
        echo 'style="'. $default . '"';
        return false;
      }
    }
  }

}
