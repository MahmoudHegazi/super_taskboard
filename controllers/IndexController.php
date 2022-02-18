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
require_once (dirname(__FILE__, 2) . '\services\UserService.php');
require_once (dirname(__FILE__, 2) . '\services\StyleService.php');
require_once (dirname(__FILE__, 2) . '\models\Calendar.php');




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

  /* Date and Time */
  protected $visit_date;
  protected $today_year;
  protected $today_month;
  protected $today_day;
  protected $today_date;
  protected $today_time;


  //$calendarMapper = new CalendarMapper($this->pdo);
  // build the pdo for global use in object only
  public function __construct(PDO $pdo)
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
    $current_month = $this->return_curent_month($this->get_current_year()->get_id(), $this->get_today_month());
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



    $this->set_current_weeks(array_distribution($this->monday_start_mange($current_days), 7, 5, $default=false));


    //echo "<pre>";
    //print_r($this->get_current_days());
    //echo "</pre>";
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
    if (empty($current_days)){return array();}
    $result = $current_days;
    $days_index=array(0=>"Monday",1=>"Tuesday",2=>"Wednesday",3=>'Thursday', 4=>'Friday', 5=>'Saturday', 6=>'Sunday');
    $missing_days_at_begin = array_search($current_days[0]->get_day_name(), $days_index);
    $last_id = $current_days[0]->get_id();
    // so here to solve the calendar known problem where so here will get the old days until it make it start monday
    // or u can disable the day to if u need easy just remove current prev and qury and insert false
    for ($mis=0; $mis<$missing_days_at_begin; $mis++){
      $current_prev = $last_id - 1;
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


  public function return_day_periods(){

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



  // load the ajax data for display periods and slots in selected day when user click + <
  // this will return data to used in add_reservation and add normal reservation
  public function return_day_periods_data($cal_id, $day_date){
  }

  // add reservation
  public function add_reservation($cal_id, $day_id, $slot_id, $notes){
  }
}
