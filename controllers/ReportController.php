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

$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

class ReportController {
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
  protected $used_calendar;


  public function __construct(PDO $pdo, $logged_userid=NULL, $logged_role='user')
  {
    if (is_null($logged_userid) || is_null($logged_role) || $logged_role != 'admin'){
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

    try{
      $success = true;
      $message = '';
      $current_calendar = $this->assign_used_calendar();
      if (!isset($current_calendar) || empty($current_calendar)){
        $success = false;
        $message = 'Sorry, No used calendar Found Can not Give You meaningful reasult! Please create One from setup it will marked as used automatic';
        throw new Exception( $message );
      }

      $check_cal = $this->calendar_service->get_calendar_by_id($current_calendar->get_id());
      if (!isset($check_cal) || empty($check_cal)){
        $success = false;
        $message = 'Selected Calendar not found or deleted Please add Calendar First For see functional results';
        throw new Exception( $message );
      } else {
        $this->set_used_calendar($check_cal);
      }
    } catch(Exception $ex) {
      throw new Exception( $ex->getMessage() );
    }

    if (!$success && !empty($message)){
      throw new Exception( $message );
    }
    if (!$success && empty($message)){
      throw new Exception( 'Can not find used calendar' );
    }

}
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

  /* POST Handler */


  public function postHandler($report_controller, $post_obj, $session_obj, $redirect_url, $error, $role){
    $ajax_request = false;

    if ($error || !isset($report_controller)){
      $_SESSION['message'] = 'The reports cannot be loaded. The procedure is not set up correctly';
      $_SESSION['success'] = 'false';
      header("Location: " . $redirect_url);
      die();
    }
    // extra check for secure post requests even if i use variable check session other source for valdation all post requests
    if (!isset($session_obj['logged_id']) || empty($session_obj['logged_id'])){
      // if not logged for some reasone eg session end back
      $_SESSION['message'] = 'Please login, you do not have access to do any action because you are not logged in';
      $_SESSION['success'] = False;
      header("Location: " . $redirect_url);
      die();
    }    /*

        print_r($this->reservations_perusers_anlysis());
        die();
    */
    // this request have diffrent role or something passed the role variable and came here so return hacker stop message [the admin check made in top of php view file] and this include any post so it has ajax
    $session_role = return_current_logged_role($session_obj['logged_id']);
    if ($session_role != 'admin' || $session_role != $role){
      $_SESSION['message'] = 'Can not load data only admin allowed..';
      $_SESSION['success'] = False;
      header("Location: " . $redirect_url);
      die();
    }

    try{
      $data = json_decode(file_get_contents('php://input'), true);
      $report_controller->ajaxHandler($report_controller, $data);
      $ajax_request = true;
      // close connection after ajax handler do its job it it not closed or hacked
      die();
    }catch(Exception $ex){
       $ajax_request = false;
    }

    echo 'Nothing here!.';
    die();
  }

  public function ajaxHandler($report_controller, $data){

    $is_load_chart = isset($report_controller) && isset($data['chart']);
    $chart = test_input($data['chart']);
    if (empty($report_controller) || empty($data) || empty($chart)){
      $data = array('code'=>400, 'data'=>array(), 'message'=>'Bad Request Data');
      print_r(json_encode($data));
      die();
    }

    if ($is_load_chart){
      // small update to make error handle in switch faster instead of default
      /*################# if u add new enum case to switch add it in the allowed values first ################ */
      $allowed_values = array('reserv_per_users','reserv_per_year','reserv_per_month','reserv_per_periods','reserv_top_period_slot','reserv_top_period_slot', 'prefered_periods', 'prefered_slots', 'calendars_performance_review');
      if (!in_array($chart, $allowed_values)){
        print_r(json_encode(array('code'=>400, 'message'=>'invalid request data provided', 'data'=>array())));
        die();
      }
      switch ($chart) {
        case "reserv_per_users":
          print_r(json_encode($this->reservations_perusers_anlysis()));
          die();
          break;
        case "reserv_per_year":
          print_r(json_encode($this->reservations_peryear_anlysis()));
          die();
          break;
        case "reserv_per_month":
          print_r(json_encode($this->reservations_permonth_anlysis()));
          die();
          break;
        case "reserv_per_periods":
          print_r(json_encode($this->reservations_perperiods_anlysis()));
          die();
          break;
        case "reserv_top_period_slot":
          print_r(json_encode($this->reservations_top_period_slot_anlysis()));
          die();
          break;
        case "prefered_periods":
          print_r(json_encode($this->prefered_periods_anlysis()));
          die();
          break;
        case "prefered_slots":
          print_r(json_encode($this->prefered_slots_anlysis()));
          die();
          break;
        case "calendars_performance_review":
          print_r(json_encode($this->calendar_reservation_anlysis()));
          die();
          break;
       default:
          print_r(json_encode(array('code'=>520, 'message'=>'!unkown request not allowed.', 'data'=>array())));
          die();
      }

  }
}
  /* POST Handler end */


  /* Chart  1 */
  public function reservations_perusers_anlysis(){
    $response = array('code'=>200, 'message'=>'Successfully get dataset', 'labels'=>array(), 'datasets'=>array(), 'title'=> 'Reservations Per Users', 'cal_id'=> 0);
    $current_calendar = $this->get_used_calendar();
    if (!isset($current_calendar) || empty($current_calendar)){
      $response['code'] = 404;
      $response['message'] = 'No Used Calendar!';
      return $response;
    }
    $response['cal_id'] = $current_calendar->get_id();
    $reservations_perusers_s = 'SELECT COUNT(reservation.id) AS total_resv, user.name, reservation.user_id, calendar.id AS cal_id FROM `reservation` JOIN `user` ON
    reservation.user_id = user.id JOIN `slot` ON reservation.slot_id = slot.id JOIN `period` ON period.id = slot.period_id JOIN `day` ON
    day.id = period.day_id JOIN `month` ON month.id = day.month_id JOIN `year` ON year.id = month.year_id JOIN `calendar` ON
    calendar.id = year.cal_id WHERE calendar.id='.$response['cal_id'].' GROUP BY user.id';
    $reservations_perusers = $this->calendar_service->free_group_query($reservations_perusers_s);
    if (!isset($reservations_perusers) || empty($reservations_perusers)){
      $response['code'] = 404;
      $response['message'] = 'No users or reservations';
      return $response;
    }

    // generate dynamic chart.js dataset for complex 2 sets charts it important provide fullset from server also easier
    // chart 1 config and data and setup
    $backgroundcolor_length = count($reservations_perusers);
    $border_color_length = $backgroundcolor_length;
    $labels=array();
    $data=array();
    for ($di=0; $di<count($reservations_perusers); $di++){
      $current_row = $reservations_perusers[$di];
      // checks for free use
      if (!isset($current_row) || empty($current_row)){continue;}
      if (
        !isset($current_row['name']) || empty($current_row['name']) ||
        !isset($current_row['total_resv']) || empty($current_row['total_resv'])
      ){
        continue;
      }
      array_push($labels, $current_row['name']);
      array_push($data, $current_row['total_resv']);
    }

    $data_set = $this->giveMeDataSet($response['cal_id'], $labels, $data, $title='Reservations Pers Users', $backgroundcolor_length, $border_color_length, 1);
    if (isset($data_set) && !empty($data_set)){
      $response['labels'] = $labels;
      array_push($response['datasets'], $data_set);
      $response['code'] = 200;
      $response['cal_id'] = $response['cal_id'];
      return $response;
    } else {
      $response['code'] = 404;
      $response['cal_id'] = $response['cal_id'];
      $response['message'] = 'No Data Found For this Chart!';
      return $response;
    }
  }
  /* Chart 1 end  */

  /* Chart  2 */
  public function reservations_peryear_anlysis(){
    $response = array('code'=>200, 'message'=>'Successfully get dataset', 'labels'=>array(), 'datasets'=>array(), 'title'=> 'Reservations Per Year', 'cal_id'=> 0);
    $current_calendar = $this->get_used_calendar();
    if (!isset($current_calendar) || empty($current_calendar)){
      $response['code'] = 404;
      $response['message'] = 'No Used Calendar!';
      return $response;
    }

    $response['cal_id'] = $current_calendar->get_id();
    $reservations_per_years_s = 'SELECT COUNT(reservation.id) AS total_resv, year.year FROM reservation JOIN slot ON
    reservation.slot_id = slot.id JOIN period ON slot.period_id = period.id JOIN day on period.day_id = day.id JOIN month ON
    day.month_id = month.id JOIN year ON month.year_id = year.id JOIN calendar ON
    year.cal_id = calendar.id WHERE calendar.id='.$response['cal_id'].' GROUP BY year.year ORDER BY year.year';

    $reservations_per_years = $this->calendar_service->free_group_query($reservations_per_years_s);
    if (!isset($reservations_per_years) || empty($reservations_per_years)){
      $response['code'] = 404;
      $response['message'] = 'No users or reservations';
      return $response;
    }

    $backgroundcolor_length = count($reservations_per_years);
    $border_color_length = $backgroundcolor_length;
    $labels=array();
    $data=array();

    for ($di=0; $di<count($reservations_per_years); $di++){
      $current_row = $reservations_per_years[$di];
      // checks for free use
      if (!isset($current_row) || empty($current_row)){continue;}
      if (
        !isset($current_row['year']) || empty($current_row['year']) ||
        !isset($current_row['total_resv']) || empty($current_row['total_resv'])
      ){
        continue;
      }
      array_push($labels, $current_row['year']);
      array_push($data, $current_row['total_resv']);
    }

    $data_set = $this->giveMeDataSet($response['cal_id'], $labels, $data, $title='Reservations Per Year', $backgroundcolor_length, $border_color_length, 1);
    if (isset($data_set) && !empty($data_set)){
      $response['labels'] = $labels;
      array_push($response['datasets'], $data_set);
      $response['code'] = 200;
      $response['cal_id'] = $response['cal_id'];
      return $response;
    } else {
      $response['code'] = 404;
      $response['cal_id'] = $response['cal_id'];
      $response['message'] = 'No Data Found For this Chart!';
      return $response;
    }
  }
  /* Chart 2 end  */

  /* Chart  3 */
  public function reservations_permonth_anlysis(){
    $response = array('code'=>200, 'message'=>'Successfully get dataset', 'datasets'=>array(), 'title'=> 'Reservations Per Month', 'cal_id'=> 0, 'labels'=>array(),);
    $current_calendar = $this->get_used_calendar();
    if (!isset($current_calendar) || empty($current_calendar)){
      $response['code'] = 404;
      $response['message'] = 'No Used Calendar!';
      return $response;
    }

    $response['cal_id'] = $current_calendar->get_id();
    $reservations_per_month_s = 'SELECT COUNT(reservation.id) AS total_resv, month.month FROM reservation JOIN slot ON
    reservation.slot_id = slot.id JOIN period ON slot.period_id = period.id JOIN day on period.day_id = day.id JOIN month ON
    day.month_id = month.id JOIN year ON month.year_id = year.id JOIN calendar ON
    year.cal_id = calendar.id WHERE calendar.id='.$response['cal_id'].' GROUP BY month.month ORDER BY month.month';

    $reservations_per_months = $this->calendar_service->free_group_query($reservations_per_month_s);
    if (!isset($reservations_per_months) || empty($reservations_per_months)){
      $response['code'] = 404;
      $response['message'] = 'No users or reservations';
      return $response;
    }

    $backgroundcolor_length = count($reservations_per_months);
    $border_color_length = $backgroundcolor_length;
    $labels=array();
    $data=array();

    for ($di=0; $di<count($reservations_per_months); $di++){
      $current_row = $reservations_per_months[$di];
      // checks for free use
      if (!isset($current_row) || empty($current_row)){continue;}
      if (
        !isset($current_row['month']) || empty($current_row['month']) ||
        !isset($current_row['total_resv']) || empty($current_row['total_resv'])
      ){
        continue;
      }

      array_push($labels, $month_name = date("F", mktime(0, 0, 0, $current_row['month'], 10)));
      array_push($data, $current_row['total_resv']);
    }

    $data_set = $this->giveMeDataSet($response['cal_id'], $labels, $data, $title='Reservations Per Month', $backgroundcolor_length, $border_color_length, 1);
    if (isset($data_set) && !empty($data_set)){
      $response['labels'] = $labels;
      array_push($response['datasets'], $data_set);
      $response['code'] = 200;
      $response['cal_id'] = $response['cal_id'];
      return $response;
    } else {
      $response['code'] = 404;
      $response['cal_id'] = $response['cal_id'];
      $response['message'] = 'No Data Found For this Chart!';
      print_r($response);
      die();
      return 'denomk';
      return $response;
    }
  }
  /* Chart 3 end  */


  /* Chart  4 */
  public function reservations_perperiods_anlysis(){
    $response = array('code'=>200, 'message'=>'Successfully get dataset', 'labels'=>array(), 'datasets'=>array(), 'title'=> 'Reservations Per Periods', 'cal_id'=> 0);
    $current_calendar = $this->get_used_calendar();
    if (!isset($current_calendar) || empty($current_calendar)){
      $response['code'] = 404;
      $response['message'] = 'No Used Calendar!';
      return $response;
    }

    $response['cal_id'] = $current_calendar->get_id();
    $reservations_per_period_s = 'SELECT COUNT(reservation.id) AS total_resv, period.period_index FROM reservation JOIN slot ON
    reservation.slot_id = slot.id JOIN period ON slot.period_id = period.id JOIN day on period.day_id = day.id JOIN month ON
    day.month_id = month.id JOIN year ON month.year_id = year.id JOIN calendar ON
    year.cal_id = calendar.id WHERE calendar.id='.$response['cal_id'].' GROUP BY period.period_index ORDER BY period.period_index';

    $reservations_per_period = $this->calendar_service->free_group_query($reservations_per_period_s);
    if (!isset($reservations_per_period) || empty($reservations_per_period)){
      $response['code'] = 404;
      $response['message'] = 'No users or reservations';
      return $response;
    }

    $backgroundcolor_length = count($reservations_per_period);
    $border_color_length = $backgroundcolor_length;
    $labels=array();
    $data=array();

    for ($di=0; $di<count($reservations_per_period); $di++){
      $current_row = $reservations_per_period[$di];
      // checks for free use
      if (!isset($current_row) || empty($current_row)){continue;}
      if (
        !isset($current_row['period_index']) || empty($current_row['period_index']) ||
        !isset($current_row['total_resv']) || empty($current_row['total_resv'])
      ){
        continue;
      }
      array_push($labels, $current_row['period_index']);
      array_push($data, $current_row['total_resv']);
    }

    $data_set = $this->giveMeDataSet($response['cal_id'], $labels, $data, $title='Reservations Per Periods', $backgroundcolor_length, $border_color_length, 1);
    if (isset($data_set) && !empty($data_set)){
      $response['labels'] = $labels;
      array_push($response['datasets'], $data_set);
      $response['code'] = 200;
      $response['cal_id'] = $response['cal_id'];
      return $response;
    } else {
      $response['code'] = 404;
      $response['cal_id'] = $response['cal_id'];
      $response['message'] = 'No Data Found For this Chart!';
      return $response;
    }
  }
  /* Chart 4 end  */


  /* Chart  4 */
  public function reservations_top_period_slot_anlysis(){
    $response = array('code'=>200, 'message'=>'Successfully get dataset', 'labels'=>array(), 'datasets'=>array(), 'title'=> 'Best Period and Slot', 'cal_id'=> 0);
    $current_calendar = $this->get_used_calendar();
    if (!isset($current_calendar) || empty($current_calendar)){
      $response['code'] = 404;
      $response['message'] = 'No Used Calendar!';
      return $response;
    }

    $response['cal_id'] = $current_calendar->get_id();
    // max period
    $perfred_users_period_s = 'SELECT COUNT(reservation.id) AS total_resv, period.period_index FROM reservation JOIN slot ON reservation.slot_id = slot.id JOIN
    period ON slot.period_id = period.id JOIN day on period.day_id = day.id JOIN month ON day.month_id = month.id JOIN year ON
    month.year_id = year.id JOIN calendar ON year.cal_id = calendar.id WHERE calendar.id='.$response['cal_id'].
    ' GROUP BY period.period_index ORDER BY period.period_index';

    $perfred_users_periods = $this->calendar_service->free_group_query($perfred_users_period_s);
    $max_period = array();
    $last_count = 0;
    for ($pc=0; $pc<count($perfred_users_periods); $pc++){
      if (!isset($perfred_users_periods[$pc]['total_resv']) || empty($perfred_users_periods[$pc]['total_resv'])){
        continue;
      }
      $current_total = intval($perfred_users_periods[$pc]['total_resv']);
      if ($current_total > $last_count){
        $last_count = intval($perfred_users_periods[$pc]['total_resv']);
        $max_period['total_resv' ] = $perfred_users_periods[$pc]['total_resv'];
        $max_period['period_index' ] = $perfred_users_periods[$pc]['period_index'];
      }
    }
      // max slot
    $perfred_users_slot_s = 'SELECT COUNT(reservation.id) AS total_resv, slot.slot_index FROM reservation JOIN slot ON reservation.slot_id = slot.id JOIN
    period ON slot.period_id = period.id JOIN day on period.day_id = day.id JOIN month ON day.month_id = month.id JOIN year ON
    month.year_id = year.id JOIN calendar ON year.cal_id = calendar.id WHERE calendar.id='.$response['cal_id'].
    ' GROUP BY slot.slot_index ORDER BY slot.slot_index';

    $perfred_users_slots = $this->calendar_service->free_group_query($perfred_users_slot_s);
    $max_slot = array();
    $last_count = 0;
    for ($pc=0; $pc<count($perfred_users_slots); $pc++){
      if (!isset($perfred_users_slots[$pc]['total_resv']) || empty($perfred_users_slots[$pc]['total_resv'])){
        continue;
      }
      $current_total = intval($perfred_users_slots[$pc]['total_resv']);
      if ($current_total > $last_count){
        $last_count = intval($perfred_users_slots[$pc]['total_resv']);
        $max_slot['total_resv' ] = $perfred_users_slots[$pc]['total_resv'];
        $max_slot['slot_index' ] = $perfred_users_slots[$pc]['slot_index'];
      }
    }
    if (
       !isset($max_period) || empty($max_period) ||
       !isset($max_slot) || empty($max_slot) ||
       !isset($max_period['total_resv']) || !isset($max_period['period_index']) ||
       empty($max_period['period_index']) ||
       !isset($max_slot['total_resv']) || !isset($max_slot['slot_index']) ||
       empty($max_slot['slot_index'])
      ){
      $response['code'] = 404;
      $response['message'] = 'No Data For this chart';
      return $response;
    }

    $backgroundcolor_length = 2;
    $border_color_length = 2;
    $labels=array('Period-'.$max_period['period_index'], 'Slot-'.$max_slot['slot_index']);
    $data=array($max_period['total_resv'], $max_slot['total_resv']);
    $data_set = $this->giveMeDataSet($response['cal_id'], $labels, $data, $title='Best Period and Slot', $backgroundcolor_length, $border_color_length, 1);
    if (isset($data_set) && !empty($data_set)){
      $response['labels'] = $labels;
      array_push($response['datasets'], $data_set);
      $response['code'] = 200;
      $response['cal_id'] = $response['cal_id'];
      return $response;
    } else {
      $response['code'] = 404;
      $response['cal_id'] = $response['cal_id'];
      $response['message'] = 'No Data Found For this Chart!';
      return $response;
    }
  }
  /* Chart 4 end  */

  /* Chart  5 */
  public function prefered_periods_anlysis(){
    $response = array('code'=>200, 'message'=>'Successfully get dataset', 'labels'=>array(), 'datasets'=>array(), 'title'=> 'Perfered Users Periods', 'cal_id'=> 0);
    if (!isset($this->calendar_service) || empty($this->calendar_service)){
      $response['code'] = 500;
      $response['message'] = 'No Data Crtical Error!';
      return $response;
    }

    $perfered_users_period_s = 'SELECT COUNT(reservation.id) total_resv, period.period_index FROM reservation JOIN user ON
    reservation.user_id = user.id JOIN slot ON reservation.slot_id = slot.id JOIN period ON slot.period_id = period.id
    GROUP BY period.period_index';
    $perfered_users_period = $this->calendar_service->free_group_query($perfered_users_period_s);
    if (!isset($perfered_users_period) || empty($perfered_users_period)){
      $response['code'] = 404;
      $response['message'] = 'No users or reservations';
      return $response;
    }

    $backgroundcolor_length = count($perfered_users_period);
    $border_color_length = $backgroundcolor_length;
    $labels=array();
    $data=array();

    for ($di=0; $di<count($perfered_users_period); $di++){
      $current_row = $perfered_users_period[$di];
      // checks for free use
      if (!isset($current_row) || empty($current_row)){continue;}
      if (
        !isset($current_row['period_index']) || empty($current_row['period_index']) ||
        !isset($current_row['total_resv']) || empty($current_row['total_resv'])
      ){
        continue;
      }
      array_push($labels, 'Period-'.$current_row['period_index']);
      array_push($data, $current_row['total_resv']);
    }

    $data_set = $this->giveMeDataSet($response['cal_id'], $labels, $data, $title='Perfered Users Periods', $backgroundcolor_length, $border_color_length, 1);
    if (isset($data_set) && !empty($data_set)){
      $response['labels'] = $labels;
      array_push($response['datasets'], $data_set);
      $response['code'] = 200;
      $response['cal_id'] = $response['cal_id'];
      return $response;
    } else {
      $response['code'] = 404;
      $response['cal_id'] = $response['cal_id'];
      $response['message'] = 'No Data Found For this Chart!';
      return $response;
    }
  }
  /* Chart 5 end  */


  /* Chart  6 */
  public function prefered_slots_anlysis(){
    $response = array('code'=>200, 'message'=>'Successfully get dataset', 'labels'=>array(), 'datasets'=>array(), 'title'=> 'Perfered Users Slots', 'cal_id'=> 0);
    $current_calendar = $this->get_used_calendar();
    if (!isset($current_calendar) || empty($current_calendar)){
      $response['code'] = 404;
      $response['message'] = 'No Used Calendar!';
      return $response;
    }
    $response['cal_id'] = $current_calendar->get_id();

    if (!isset($this->calendar_service) || empty($this->calendar_service)){
      $response['code'] = 500;
      $response['message'] = 'No Data Crtical Error!';
      return $response;
    }

    $perfered_users_slot_s = 'SELECT COUNT(reservation.id) total_resv, slot.slot_index FROM reservation JOIN user ON
    reservation.user_id = user.id JOIN slot ON reservation.slot_id = slot.id GROUP BY slot.slot_index';
    $perfered_users_slots = $this->calendar_service->free_group_query($perfered_users_slot_s);
    if (!isset($perfered_users_slots) || empty($perfered_users_slots)){
      $response['code'] = 404;
      $response['message'] = 'No users or reservations';
      return $response;
    }

    $backgroundcolor_length = count($perfered_users_slots);
    $border_color_length = $backgroundcolor_length;
    $labels=array();
    $data=array();

    for ($di=0; $di<count($perfered_users_slots); $di++){
      $current_row = $perfered_users_slots[$di];
      // checks for free use
      if (!isset($current_row) || empty($current_row)){continue;}
      if (
        !isset($current_row['slot_index']) || empty($current_row['slot_index']) ||
        !isset($current_row['total_resv']) || empty($current_row['total_resv'])
      ){
        continue;
      }
      array_push($labels, 'Slot-'.$current_row['slot_index']);
      array_push($data, $current_row['total_resv']);
    }

    $data_set = $this->giveMeDataSet($response['cal_id'], $labels, $data, $title='Perfered Users Slots', $backgroundcolor_length, $border_color_length, 1);
    if (isset($data_set) && !empty($data_set)){
      array_push($response['datasets'], $data_set);
      $response['code'] = 200;
      $response['labels'] = $labels;
      $response['cal_id'] = $response['cal_id'];
      return $response;
    } else {
      $response['code'] = 404;
      $response['cal_id'] = $response['cal_id'];
      $response['message'] = 'No Data Found For this Chart!';
      return $response;
    }

  }
  /* Chart 6 end  */

  /* Chart  7 */
  public function best_period_slot_mix_anlysis(){
    return $this->used_calendar;
  }
  /* Chart 7 end  */

  /* Chart  8 */
  /*
    public function calendar_reservation_anlysis(){
      $response = array('code'=>200, 'message'=>'Successfully get datasets', 'labels'=>array(), 'datasets'=>array(), 'title'=> 'Calendars Performance', 'cal_id'=> 0);
      if (!isset($this->calendar_service) || empty($this->calendar_service)){
        $response['code'] = 500;
        $response['message'] = 'No Data Crtical Error!';
        return $response;
      }
      $all_cals_s = 'SELECT id FROM calendar ORDER BY id';
      $all_cals = $this->calendar_service->free_group_query($all_cals_s);
      if (!isset($all_cals) || empty($all_cals)){
        $response['code'] = 404;
        $response['message'] = 'No Calendars Found!';
        return $response;
      }

      // get data set for each cal
      for ($ci=0; $ci<count($all_cals); $ci++){
        if (!isset($all_cals[$ci]['id']) || empty($all_cals[$ci]['id']) || !is_numeric($all_cals[$ci]['id'])){
          continue;
        }
        $calid = $all_cals[$ci]['id'];
        $cc = 'IT';
        if (!isset($country_code) || empty($country_code)){
          $cc = 'IT';
        }

        $total_resev_s = $this->calendar_service->free_single_query('SELECT COUNT(reservation.id) AS reservations FROM reservation JOIN slot ON reservation.slot_id = slot.id JOIN period ON slot.period_id = period.id JOIN day on period.day_id = day.id JOIN month ON
        day.month_id = month.id JOIN year ON month.year_id = year.id JOIN calendar ON year.cal_id = calendar.id WHERE calendar.id='.$calid);
        $total_years_s = $this->calendar_service->free_single_query('SELECT COUNT(year.id) AS years FROM year JOIN calendar ON year.cal_id = calendar.id WHERE calendar.id='.$calid);
        $total_logs_s = $this->calendar_service->free_single_query('SELECT COUNT(logs.id) AS logs FROM logs JOIN calendar ON logs.cal_id = calendar.id WHERE logs.cal_id='.$calid);
        $total_ever_blocked = 1;
        $total_ever_banned = 1;
        $total_forgien_users = $this->calendar_service->free_single_query('SELECT COUNT(logs.id) AS forgiens FROM logs JOIN calendar ON logs.cal_id = calendar.id WHERE logs.loc != '."'".$cc."'".' AND logs.cal_id='.$calid);
        $total_native_users = $this->calendar_service->free_single_query('SELECT COUNT(logs.id) AS native FROM logs JOIN calendar ON logs.cal_id = calendar.id WHERE logs.loc ='."'".$cc."'".' AND logs.cal_id='.$calid);
        $total_windows_users = $this->calendar_service->free_single_query('SELECT COUNT(logs.id) AS windows FROM logs JOIN calendar ON logs.cal_id = calendar.id WHERE logs.os_type = '."'Windows OS'".' AND logs.cal_id='.$calid);
        $total_linux_users = $this->calendar_service->free_single_query('SELECT COUNT(logs.id) AS linux FROM logs JOIN calendar ON logs.cal_id = calendar.id WHERE logs.os_type = '."'Linux OS'".' AND logs.cal_id='.$calid);
        $total_mac_users = $this->calendar_service->free_single_query('SELECT COUNT(logs.id) AS mac FROM logs JOIN calendar ON logs.cal_id = calendar.id WHERE logs.os_type = '."'MacOS'".' AND logs.cal_id='.$calid);
        $total_hacked_users = $this->calendar_service->free_single_query('SELECT COUNT(logs.id) AS hacked FROM logs JOIN calendar ON logs.cal_id = calendar.id WHERE logs.class_token != logs.form_token AND logs.cal_id='.$calid);
        $total_dist_remember_me = 0;


        if (
            !isset($total_resev_s) || !isset($total_windows_users) ||
            !isset($total_years_s) || !isset($total_linux_users) ||
            !isset($total_logs_s) || !isset($total_mac_users) ||
            !isset($total_ever_blocked) || !isset($total_hacked_users) ||
            !isset($total_ever_banned) || !isset($total_dist_remember_me) ||
            !isset($total_forgien_users) || !isset($total_native_users)
          ){
            $response['code'] = 500;
            $response['message'] = 'invalid data chart notloaded';
            return $response;
          }

          $labels1 = array(
            'reservations',
            'years',
            'logs',
            'blocked',
            'banneds',
            'hacked',
            'forgiens',
            'native',
            'remembered_me',
            'windows',
            'linux',
            'mac',
          );
          $data = array(
            intval($total_resev_s['reservations']),
            intval($total_years_s['years']),
            intval($total_logs_s['logs']),
            0,
            0,
            0,
            intval($total_forgien_users['forgiens']),
            intval($total_native_users['native']),
            $total_dist_remember_me,
            intval($total_windows_users['windows']),
            intval($total_linux_users['linux']),
            intval($total_mac_users['mac'])
          );
          $data_set = $this->giveMeDataSet($calid, $labels1, $data, $title='Calendar Performance', 1, 1, 1);
          if (isset($data_set) && !empty($data_set)){
            $response['code'] = 200;
            $response['cal_id'] = $calid;
            $response['labels'] = $labels1;
            $response['message'] = 'Successfully Get data';
            array_push($response['datasets'], $data_set);
            return $response;
          } else {
            continue;
          }
      }
      return $response;

    }
    */
    /* Chart 8 end  */

  public function giveMeDataSet($cal_id, $labels=array(), $data=array(), $title='', $backgroundColor=0, $borderColor=0, $borderWidth=1){
    if (
       !isset($labels) || empty($labels) ||
       !isset($data) || empty($data) ||
       !isset($backgroundColor) || empty($backgroundColor) ||
       !isset($borderColor) || empty($borderColor) ||
       !is_numeric($backgroundColor) || !is_numeric($borderColor) ||
       !is_numeric($cal_id) || !is_numeric($cal_id)
     ) {
       return array();
     }
     if (!isset($borderWidth) || empty($borderWidth) || !is_numeric($borderWidth)){
       $borderWidth = 1;
     }
     if (!isset($title) || empty($title)){
       $title = 'Python King Chart';
     }
    $data_set = array(
      'label'=> $title,
      'data'=> $data,
      'backgroundColor'=>$backgroundColor,
      'borderColor'=>$borderColor,
      'borderWidth'=>$borderWidth,
    );
    return $data_set;
  }

}

/* current anlysis tests and format
echo '<pre>';
print_r($this->reservations_perusers_anlysis());
echo '</pre>';


echo '<pre>';
print_r($this->reservations_peryear_anlysis());
echo '</pre>';


echo '<pre>';
print_r($this->reservations_permonth_anlysis());
echo '</pre>';

echo '<pre>';
print_r($this->reservations_perperiods_anlysis());
echo '</pre>';

echo '<pre>';
print_r($this->reservations_top_period_slot_anlysis());
echo '</pre>';

echo '<pre>';
print_r($this->prefered_periods_anlysis());
echo '</pre>';

echo '<pre>';
print_r($this->prefered_slots_anlysis());
echo '</pre>';

echo '<pre>';
print_r($this->calendar_reservation_anlysis());
echo '</pre>';*/
