<?php
ob_start();
require_once('config.php');
require_once('controllers/IndexController.php');
if (!isset($_SESSION['logged']) && empty($_SESSION['logged'])){
  if ($_SESSION['logged'] == False){
    $_SESSION['message_login'] = 'Please Login To acces this page';
    $_SESSION['success_login'] = False;
    header("Location: ./login.php");
    die();
    return False;
  }
}
$request_type = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';

/* Handle Used Calendar part and get all data needed from controller like the MVC but modifed by include controller instead of get the request */
$current_calendar = null;
$used_calendar_emessage = '';
$index_controller = null;
$current_months = null;
$current_weeks = null;
$cal_years = null;
$current_year = null;
$cal_id = null;
$has_years = null;
$min_year = null;
$max_year = null;

$error = false;

// this important it handle all exception from indexController
try {
  // Index Controller and view setup
  // if you get here, all is fine and you can use $object
  $current_month = isset($_GET['month']) && !empty($_GET['month']) ? test_input($_GET['month']) : null;
  global $pdo;
  $index_controller = new IndexController($pdo, $current_month, $request_type);
  $current_calendar = $index_controller->get_used_calendar();
  $current_months = $index_controller->get_current_months();
  $cal_years = $index_controller->get_years();
  $current_year = $index_controller->get_current_year();
  $current_weeks = $index_controller->get_current_weeks();
  define('Calid', $current_calendar->get_id());
  define('TITLE', $current_calendar->get_title());
  define('DESCRIPTION', $current_calendar->get_description());
  define('THUMBNAIL', $current_calendar->get_background_image());
  $has_years = $index_controller->get_has_years();
  $min_year = $index_controller->get_current_min_year();
  $max_year = $index_controller->get_current_max_year();
}
catch( Exception $e ) {
  $used_calendar_emessage = $e->getMessage();
  $error = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  // This How The SuperMVC handle all view with all needed given post requests
  $redirect_url = $_SERVER['HTTP_REFERER'];
  $index_controller->postHandler($index_controller, $_POST, $_SESSION, $redirect_url, $error);
  die();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" href="<?php echo defined('THUMBNAIL') ? 'uploads/images/' . THUMBNAIL : 'uploads/images/default_logo.png'; ?>">

    <title><?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>


    <style>
      body, html {
      margin: 0;
      height: auto;
      font-family: georgia;
      font-size: 16px;
      width: 100%;
      }
      div.aside_container{
      box-shadow: 0 4px 8px 0 rgba(200, 200, 200), 0 6px 20px 0 rgba(0, 150, 200, .06);
      background: #e6e6fa75;
      display: block;
      }
      .nav_cont{text-align:justify;}
      div.aside_container .nav_item, div.calendar {
      box-shadow: 0 4px 8px 0 rgba(200, 200, 200, 0.2), 0 6px 20px 0 rgba(200, 200, 200, 1.2);
      cursor: pointer;
      }
      div.aside_container .nav_item:hover {
      box-shadow: 0 4px 8px 0 rgba(200, 200, 200, 0.2), 0 6px 20px 0 rgba(200, 200, 200, 1.2);
      box-shadow: 0 4px 8px 0 rgba(30, 200, 50, 0.2), 0 6px 20px 0 rgba(200, 200, 200, .6);
      cursor: pointer;
      background: azure;
      }
      .menu__item {
      text-decoration: none;
      color: black;
      font-weight: bold;
      font-family: georgia;
      }
      .active_section{
      box-shadow: 0 4px 8px 0 rgb(150 150 150 / 20%), 0 6px 10px 0 rgb(12 130 150) !important;
      }
      .active_link{
      background: royalblue;
      color: white !important;
      box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }
      .scroll_to_btns:hover{
      background: white;
      color: black !important;
      box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }
      .default_shadow {
      box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }
      .bg_darkkhaki{
      background:darkkhaki;
      }
      .bg_aliceblue {
      background: aliceblue;
      }
      .bg_dodgerblue {
      background: dodgerblue;
      }
      .bg_dimgray{
      background: dimgray;
      }
      .bg_indianred{
      background: indianred;
      }
      .bg_cornsilk{
      background: cornsilk;
      color: dimgray !important;
      }
      .bg_azure{
      background: azure;
      }
      .bg_cornflowerblue{
      background: cornflowerblue;
      }
      .bg_palevioletred{
        background: palevioletred;
      }
      .fontbold{
        font-weight: bold;
      }
      .shadow1{
      text-shadow: 1px 1px 2px black, 8px 0 25px gray, 3px 0 5px darkblue;
      }
      .text_shadow01 {
      text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px lightgray;
      }
      .text_shadow02 {
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 5px darkblue;
      }

      .max_width_30{
        max-width: 30% !important;
      }
      .month_arrow {
      cursor: pointer;
      }
      .month_arrow:hover {
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .month_toggle_btn {
      width: 30px;
      max-width: 30px;
      box-shadow: 0 2px 3px 0 rgb(200 200 200 / 20%), 0 6px 8px 0 rgba(0, 0, 50, 0.5);
      cursor: pointer;
      }
      .month_toggle_btn span{
      text-shadow: 1px 1px 2px lightgray, 0 0 25px blue, 0 0 5px gold;
      color: darkslategrey;
      font-weight: bold;
      }
      .month_toggle_btn:hover {
      box-shadow: 0 2px 3px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }
      .cal_card_cell {
      max-width:14.2857142857%;
      overflow:auto;
      hight: fit-content;
      max-height: fit-content;
      padding: 8px;
      background: royalblue;
      color: cornsilk;
      }
      .day_card {
      min-height: fit-content;
      cursor: default;
      }

      .period_title_default{

         width: fit-content;
         margin-left: auto !important;
         margin-right: auto !important;
         padding: 2px;
       }
      .period_background_default {
        padding: 5px;
        color: black;
        border: 1px solid white;
        border-radius: 8px;
        width: 95%;
        max-width: 95%;
        margin-bottom: 5px;


      }

      .used_slot {
        opacity: 0.8;
        cursor: default !important;
      }
      .slot_background_default {
        cursor: pointer;
        background: lightgray;
        color: black;
        border: 1p solid gold;
        border-radius: 8px;
        width: 95%;
        max-width: 95%;

        display: flex !important;
        justify-content: center !important;
        align-items: center importat;
        flex-flow: row nowrap !important;
        margin-left: auto !important;
        margin-right: auto !important;

      }
       /* active section and link and scroll to */
       .active_section{
        box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(10 10 10);
      }
      .active_link{
        background: royalblue;
        color: white !important;
        box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }


      .scroll_to_btns:hover{
        background: white;
        color: black !important;
        box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }

      .null_day {
        background: dimgrey;
      }

      /* media query programing */
      @media only screen and (max-width: 690px) {
       /* ipad */
      .full_day {display: none !important;}
      .short_day{display: block !important;}


       .period_title_default {
          max-width:80% !important;
          overflow:hidden;
          font-size: .525rem;
          padding:0;
      }

      .period_background_default {
        padding: 0px !important;
        max-width: 100%;
        overflow: hidden;
        margin-bottom: 0px !important;
                  display: flex !important;
          justify-content: center !important;
          align-items: center importat;
          flex-flow: column nowrap !important;
       padding: 0px;
      }
      .slot_background_default {
        max-width: 80%;
        overflow: hidden;
      }


      }

      @media only screen and (max-width: 580px) {

      .period_title_default {
          max-width:100%;
          overflow:hidden;
          font-size: .925rem;
          padding:8px;
          margin-top: 4px;
          margin-bottom 2px;
      }

      .cal_cards_row{
      display: block !important;
      border-bottom: 5px solid royalblue;
      border-top: 5px solid royalblue;
      border-radius: 10px;
      margin-bottom: 20px;

      box-shadow: 0px 4px 0 4px rgba(50, 50, 50, .4), 0 0 0 1px rgba(0, 200, 150, .8);
      }
      .cal_cards_row:hover{
      box-shadow: 0px 4px 0 4px rgba(50, 50, 50, .4), 0 0 0 2px rgba(130, 255, 130, .8);
      background: rgba(240, 250, 240, .8);
      }
      .cal_days_titles {
      display: none !important;
      }
      .day_card{ max-width: 100% !important;}
      /* \a new line */
      .week_1::before {
      content: 'Week 1';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .week_2::before {
      content: 'Week 2';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .week_3::before {
      content: 'Week 3';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .week_4::before {
      content: 'Week 4';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .week_5::before {
      content: 'Week 5';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      }
      @media only screen and (max-width: 300px) {
      div#month_controler_container {
      width: 100%;
      }
      .options_parent {
      margin: 0;
      padding: 0;
      }
      .month_arrow {
      font-size: 1.5rem;
      }
      .month_name {
      font-size: 1rem;
      display: inline-block !important;
      margin-left: auto !important;
      margin-right: auto !important;
      }
      .month_small_btns{
      padding: 0;
      margin: 0;
      }
      .month_small_btns form {
      width: 30px;
      font-size: 1rem !important;
      padding: 1 !important;
      }
      .month_row  {
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      flex-flow: column;
      }



      }
      @media only screen and (max-width: 190px) {
      .month_row {
      border: none !important;
      word-break: break-word;
      }
      .month_name {
      font-size: .825rem;
      }
      .month_arrow {
      font-size: 1.5rem;
      }
      }
    </style>
  </head>
  <body>



    <div class="container-fluid p-2 text-white text-center cal_title bg_dimgray">
      <h3 class="display-6 mt-2 mb-3 text-white p-2 bg_indianred
      default_shadow text_shadow03 border border-secondary">
      <img src="<?php echo defined('THUMBNAIL') ? 'uploads/images/' . THUMBNAIL : 'uploads/images/default_logo.png'; ?>" alt="Calendar Logo" height="50" width="50">
      <?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?>
    </h3>
      <p class="description_p bg_azure  text-black border border-secondary p-2 default_shadow">
        <?php echo defined('DESCRIPTION') ? DESCRIPTION : 'Booking Calendar'; ?>
      </p>

      <div class="container d-flex justify-content-end border border-succcess p-2">
        <a href="./logout.php" class="btn btn-danger">Log Out</a>
        <!-- fake margin for flex -->
        <span style="width:10px;"></span>
        <button class="btn btn-primary">Setup</button>
        <span style="width:10px;"></span>
        <button class="btn btn-success">Profile</button>
      </div>

    </div>


    <!-- in case no used were calendar found -->
    <?php if(is_null($current_calendar)){ ?>
      <div class="alert alert-danger">
        <p class="text-center"><strong>Warning!</strong> <?php echo $used_calendar_emessage; ?>
          <!-- check if admin and display link to setup -->
          <div class="d-flex justify-content-center align-items-center">
            <a href="setup.php" class="btn btn-outline-primary">Go To Setup</a>
          </div>
        </p>
      </div>
    <?php die();} ?>

    <!-- in case no used were calendar found -->
    <?php if(isset($_SESSION['message']) && isset($_SESSION['success']) && !empty($_SESSION['message'])){ ?>
      <div class="alert alert-<?php echo $_SESSION['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <p class="text-center">
          <?php echo $_SESSION['message']; ?>
        </p>
      </div>
    <?php unset($_SESSION['message']);unset($_SESSION['success']);} ?>





    <div class="app_main container-fluid p-2 text-black text-center" style="width: 100% !important;">
      <div class="container-fluid main_cal_display border border-secondary">
        <div class="row">
          <div class="col-sm-12  p-2 d-flex justify-content-center">
            <div class="row container-fluid options_parent  bg_dimgray p-2">
              <!-- month controller start -->
              <div id="month_controler_container" class="col-sm-6">
                <!-- month switcher start -->
                <div  class="container month_row d-flex flex-wrap align-items-start justify-content-between p-2  text-black border border-light">
                  <i class="display-6 flex-fill fa fa-arrow-circle-left text-white month_arrow"></i>
                  <h3 id="selected_month_name" class="flex-fill month_name text_shadow01 text-white">
                    <?php if (!is_null($index_controller->get_current_month())){
                      $dateObj = DateTime::createFromFormat('!m', $index_controller->get_current_month()->get_month());
                      $monthName = $dateObj->format('F');
                      echo $monthName;
                    } ?>
                  </h3>
                  <i class="display-6 flex-fill fa fa-arrow-circle-right text-white month_arrow"></i>
                </div>
                <!-- month switcher end -->
              </div>
              <!-- month controller end -->
              <div class="col-sm-5 d-flex justify-content-center align-items-start rounded p-2">
                <!-- year display start -->
                <form class="flex-fill">
                  <select class="form-control" name="selected_year" id="selected_year">
                    <!-- years selector -->

                    <?php if (!empty($cal_years) && is_array($cal_years)) {
                      for ($y=0; $y<count($cal_years); $y++){
                    ?>
                      <option value="<?php echo $cal_years[$y]->get_year(); ?>"
                        <?php echo $current_year->get_year() == $cal_years[$y]->get_year() ? 'selected' : ''; ?> >
                        <?php echo $cal_years[$y]->get_year(); ?>
                      </option>
                    <?php }
                    } ?>
                  </select>
                </form>
                <!-- year display end -->
              </div>
              <div class="col-sm-12 bg_dimgray p-2">
                <!-- months numbers switch month -->
                <div class="btn-group btn-sm d-flex flex-wrap justify-content-center align-items-center month_small_btns">
                  <?php
                    if ($current_months && is_array($current_months) && !empty($current_months)){
                      for ($m=0; $m<count($current_months); $m++){
                        ?>
                        <!-- change month by number better UX option for old man -->
                        <form method="GET" action="./" class="month_form bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                          <span class="p-1 text-center"><?php echo $current_months[$m]->get_month(); ?></span>
                          <input type="hidden" style="display:none;" name="month" value="<?php echo $current_months[$m]->get_month(); ?>" required>
                        </form>
                        <?php
                      }
                    }
                  ?>
                  <!-- change month buttons end -->
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12 d-flex justify-content-center align-items-center bg_dimgray">
            <!-- Calendar display start -->
            <div class="calendar border border-dark p-2 mt-3 mb-5 container-fluid bg-white">
              <!-- week Titles row start -->
              <div class="d-flex p-2 cal_days_titles">
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Monday</span>
                  <span class="short_day" style="display:none;">Mon</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Tuesday</span>
                  <span class="short_day" style="display:none;">Tue</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Wednesday</span>
                  <span class="short_day" style="display:none;">Wed</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Thursday</span>
                  <span class="short_day" style="display:none;">Thu</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Friday</span>
                  <span class="short_day" style="display:none;">Fri</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Saturday</span>
                  <span class="short_day" style="display:none;">Sat</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Sunday</span>
                  <span class="short_day" style="display:none;">Sun</span>
                </div>
              </div>
              <!-- week Titles row end -->
              <!-- hidden week scroll buttons -->
              <div class="d-flex flex-column align-items-center p-2 " style="position: fixed;left:0;top:0;background: transparent;width:fit-content;max-width:100% !important; font-size:10px;margin:0;padding:0 !important;">
                <?php
                  if ($current_weeks && !empty($current_weeks)){
                    for ($cw=0; $cw<count($current_weeks); $cw++){
                      $week_id = 'week_' . ($cw+1);
                      ?>
                      <div class="scroll_to_btns flex-fill border border-primary btn  btn-secondary text-white  mt-1 mb-1" data-target="<?php echo $week_id; ?>"><?php echo ($cw+1); ?></div>
                      <?php
                    }
                  }
                ?>


                <div id="map_booking_modal_open" class="flex-fill border border-primary btn btn-light mt-1 mb-1 aside_add_res" data-bs-toggle="modal" data-bs-target="#mapBookingModal">
                 <i class="fa fa-plus text-primary"></i>
                </div>


              </div>
              <!-- week Titles row end -->
              <?php
                if (!is_null($current_weeks) && !empty($current_weeks)){
                  for ($w=0; $w<count($current_weeks); $w++){
                    // new week
                    $current_week = $current_weeks[$w];
                    $week_id = 'week_' . ($w+1);
                    $week_count = count($index_controller->get_current_days());
                    ?>
                    <!-- week days row start -->
                    <div class="d-flex p-2 cal_cards_row week_<?php echo ($w+1); ?>" id="<?php echo $week_id; ?>">
                      <?php
                      // new day
                      for ($d=0; $d<count($current_week); $d++){

                        $day_data = $current_weeks[$w][$d];
                        if ($day_data == false || empty($day_data)){
                          // empty day

                          ?>
                          <!-- empty day -->
                          <!-- day start -->
                          <div class="flex-fill border border-light cal_card_cell day_card null_day" title="This day is not available The selected month is : <?php echo $week_count; ?> Days">

                          </div>
                          <?php
                          // day data array([day] => Day Object, [day_data] => Array)
                        } else {
                          $selected_day = $day_data['day'];
                          $day_id = $selected_day->get_id();
                          $day = $selected_day->get_day();
                          $day_date = $selected_day->get_day_date();
                          $day_name = $selected_day->get_day_name();

                          $selected_day_data = $day_data['day_data'];
                        ?>
                        <!-- day start -->
                        <div data-day="<?php echo $day_name; ?>" class="flex-fill border border-light cal_card_cell day_card" id="day_<?php echo $day_id;  ?>">

                           <!-- day meta -->
                             <h6 class="text-center"><?php echo substr($day_name, 0, 3) . ' ' . $day; ?></h6>
                             <h6 class="text-center bg-light text-black badge"><?php echo $day_date; ?></h6>
                           <!-- array_distribution -->
                           <!-- all periods start -->
                           <div class="all_periods">
                             <?php // now get periods from the data array
                               // day_data array(Array([day_period] => Period Object, [day_slot] => Array([0] => Slot Object)))
                               // loop over periods data (
                               for ($p=0; $p<count($selected_day_data); $p++){
                                 $selected_slots_data = $selected_day_data[$p];
                                 $selected_period = isset($selected_slots_data['day_period']) ? $selected_slots_data['day_period'] : array();
                                 // period data
                                 $p_id = $selected_period->get_id();
                                 $p_day_id = $selected_period->get_day_id();
                                 $p_date = $selected_period->get_period_date();
                                 $p_description = $selected_period->get_description();
                                 $period_index = $selected_period->get_period_index();
                                 $p_element_id = $selected_period->get_element_id();
                                 $p_element_class = $selected_period->get_element_class();
                                 /* ##################### display periods   ############################ */
                                 ?>
                                 <!-- period example start -->
                                 <!-- notice here  the id come from database and class u can change also u can add normal css to target some slots in css file many ways -->
                                 <div class="container period_background_default <?php echo $p_element_class; ?>" id="<?php echo $p_element_id; ?>" >
                                    <!-- period title -->
                                    <span class="badge bg-success mt-1 period_title_default" ><?php echo $p_description; ?></span>
                                    <!-- all slots start -->

                                    <?php
                                      $slots_data = isset($selected_slots_data['day_slot']) ? $selected_slots_data['day_slot'] : array();
                                      // now loop over slots
                                      for ($s=0; $s<count($slots_data); $s++) {
                                        // slot row
                                        $slot_obj = $slots_data[$s];
                                        $s_id = $slot_obj->get_id();
                                        $s_start_from = $slot_obj->get_start_from();
                                        $s_end_at = $slot_obj->get_end_at();
                                        $s_period_id = $slot_obj->get_period_id();
                                        $s_empty = $slot_obj->get_empty();
                                        $s_slot_index = $slot_obj->get_slot_index();
                                        $s_element_id = $slot_obj->get_element_id();
                                        $s_element_class = $slot_obj->get_element_class();
                                        // used slot
                                        if ($s_empty == 0){
                                          // display used slot
                                          ?>
                                          <!-- slot start booked already -->
                                          <div class="slot_background_default m-1 used_slot <?php echo $s_element_class; ?>"
                                            id="<?php echo $s_element_id; ?>">
                                           <div class="container">
                                             <i class="fa fa-calendar-check-o" style="font-size:1.1em;"></i>
                                           </div>
                                          </div>
                                          <!-- slot end -->
                                          <?php
                                        } else {
                                          ?>
                                          <!-- slot start with booking -->
                                          <div class="slot_background_default m-1 empty_slot <?php echo $s_element_class; ?>" id="<?php echo $s_element_id; ?>">
                                           <div class="container book_open_btn"
                                             data-slot-id="<?php echo $s_id; ?>"
                                             data-slot-start_from="<?php echo $s_start_from; ?>"
                                             data-slot-end_at="<?php echo $s_end_at; ?>"
                                             data-slot-empty="<?php echo $s_empty; ?>"
                                             data-slot-index="<?php echo $s_slot_index; ?>"
                                             data-period-date="<?php echo $p_date; ?>"
                                             data-period-description="<?php echo $p_description; ?>"
                                             data-bs-toggle="modal" data-bs-target="#bookingModal">
                                             <i class="fa text-primary fa fa-calendar-o" style="font-size:1.1em;"></i>
                                           </div>
                                          </div>
                                          <!-- slot end -->
                                          <?php

                                        }
                                        // end slot
                                      }
                                    ?>
                                    <!-- all slots end -->
                                 </div>
                                 <!-- period example end -->
                                 <?php
                               }
                             ?>

                           </div>
                           <!-- all periods end -->
                        </div>
                        <!-- day end -->
                        <?php
                        }
                      }
                      ?>
                    </div>
                    <!-- week days end -->

                    <?php
                  }
                }
              ?>


            <!-- Calendar display end -->
          </div>
        </div>
      </div>
    </div>
  </div>


<!-- Booking modal start -->
<div class="modal fade" id="bookingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header ">
        <h5 class="modal-title ">Booking <span id="booking_date_a"></span> <i class="fa fa-calendar"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <!-- Modal body -->
      <div class="modal-body">
          <div class="container text-center d-flex justify-content-between align-items-center">
            <p>Period: ( <span id="period_description_a" class="bg-success badge text-white ml-1 mr-1"></span> )</p>
            <p>Slot: ( <span id="slot_index_a" class="bg-primary badge text-white ml-1 mr-1"></span> )</p>
          </div>
            <div class="form-group">
              <div class="mb-3 mt-3 text-center">
                <input  maxlength="30"  pattern="[A-Za-z\s]{1,30}" title="Please Enter a valid name: eg Jone" type="text" class="form-control" placeholder="Name.." name="reservation_name" id="reservation_name" />
                <textarea min="0" maxlength="255" placeholder="Reservation Notes.." class="form-control" rows="3" id="reservation_comment" name="reservation_comment"></textarea>
              </div>
                 <input id="supcal_token" name="secuirty_token" type="hidden" value="<?php echo isset($index_controller) ? $index_controller->get_request_secert() : ''; ?>" />
                 <input type="hidden" value="" name="reservation_slot_id" id="reservation_slot_id" style="display:none;">
                  <div class="container text-center d-flex justify-content-between m-2 p-2">
                  <p class="ml-2">Start At: <span id="start_from_slot_a" class="bg-secondary text-white badge p-2">12:00PM</span></p>
                  <p class="ml-2">end at: <span class="bg-danger text-white badge p-2" id="end_at_slot_a">02:00PM</span></p>
                  <p>
                    Period Started at: <span id="period_date_time_a" class="badge bg-warning text-secondary"></span>
                  </p>
                </div>
            </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        <button id="add_reservation" type="submit" class="btn btn-primary">Confirm Booking</button>
      </div>
      </form>

    </div>
  </div>
</div>
<!-- Booking modal end -->



<!-- Map Booking modal start -->
<div class="modal fade" id="mapBookingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header ">
        <h5 class="modal-title "> Add new Booking <i class="fa fa-calendar-o"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <div id="map_error_cont"></div>
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">

            <!-- level 1 select day date and get id with ajax -->
            <div class="form-group"  id="map_day_level1">
              <?php
              // if calendar id exist it must exist
              if (defined('Calid')){
                  // check if min year and max year and the calendar has years you can has calendar with 0 years easy
                  if ($has_years && !is_null($min_year) && !is_null($max_year)){
                  ?>
                    <label>Pick a Date</label>
                    <input class="form-control" name="map_reservation_date"
                    id="map_reservation_date" data-cal-id="<?php echo Calid;?>" type="date"  min="<?php echo $min_year . '-01-01'; ?>" max="<?php echo $max_year . '-12-12'; ?>">
                  <?php
                  } else {
                  ?>

                    <div class="alert alert-warning fade show alert-dismissible">
                      <span>This Calendar Has No Years</span>
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                  <?php
                 }
              } else {
                ?>
                <div class="alert alert-warning fade show alert-dismissible">
                  <span>unexcpted Error Calendar Data Not Loaded</span>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  <!-- all periods data -->
                </div>
                <?php
              }
              ?>
            </div>
            <!-- end level 1 -->


<!-- display periods and slots level 2 -->
<div id="map_day_level2" class="container-fluid d-flex flex-row flex-wrap">

   <div class="container-fluid p-2" id="map_reservation_periods_container">
     <!-- all periods data -->
   </div>

</div>
<!-- end display periods and slots level 2 -->




            <!-- level 3 display add reservation inputs -->

            <div class="form-group"  id="map_day_level3" style="display:none;">

              <div class="container p-2 mt-2 mb-1">
               <h5 id="reservation_ptitle_map" class="text-center"></h5>
              </div>
              <div class="mb-3 mt-3 text-center">
                <input  maxlength="30"  pattern="[A-Za-z\s]{1,30}" title="Please Enter a valid name: eg Jone" type="text" class="form-control" placeholder="Name.." name="reservation_name" />
                <textarea min="0" maxlength="255" placeholder="Reservation Notes.." class="form-control" rows="3" name="reservation_comment"></textarea>
              </div>


                   <input name="secuirty_token" type="hidden" value="<?php echo isset($index_controller) ? $index_controller->get_request_secert() : ''; ?>" />
                   <input type="hidden" value="" name="reservation_slot_id" id="reservation_slot_id_map" style="display:none;">


                   <div class="container text-center d-flex justify-content-between m-2 p-2">
                     <p class="ml-2">Start At: <span id="level3_start_from" class="bg-secondary text-white badge p-2"></span></p>
                     <p class="ml-2">end at: <span class="bg-danger text-white badge p-2" id="level3_end_at"></span></p>
                   </div>
                   <button type="submit" class="btn btn-primary">Confirm Booking </button>
            </div>




          </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- Map Booking modal end -->


<!-- Cancel reservation modal start -->
<div class="modal" id="myModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title">Cancel the reservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="alert alert-danger text-black text-center">Are you sure you want to cancel the reservation</div>

        <div class="d-grid">
          <button type="submit" class="btn btn-block btn-dark text-white" data-bs-dismiss="modal">
           Cancel Reservation</button>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Back</button>
      </div>

    </div>
  </div>
</div>
<!-- Cancel reservation modal end -->

<!-- sound effects -->
<audio id="open_modal_sound">
  <source src="https://sndup.net/rp5j/d" type="audio/wav">
</audio>

<audio id="unable_open_modal">
  <source src="https://sndup.net/sbs9/d" type="audio/wav">
</audio>

<!-- HTML5 sounds -->
<script>


const playSound = (selector)=>{
  //open_modal_sound unable_open_modal
  const selectedSound = document.querySelector(`${selector}`);
  selectedSound.volume = 0.1;
  // important for on time sound like it play from begning and ignore previous
  selectedSound.currentTime = 0;
  selectedSound.play();

}


      const allScrollBtns = document.querySelectorAll(".scroll_to_btns");

      allScrollBtns.forEach( (scrollBtn)=>{
        scrollBtn.addEventListener( "click", (event)=>{
          const toId = scrollBtn.getAttribute("data-target");
          const scrollToWeek = document.getElementById(toId);
          if (scrollToWeek){
            scrollToWeek.scrollIntoView({behavior: "smooth", block: "start", inline: "nearest"});
            return true;
          } else {
            return false;
          }
        })

      });

      // active link and section



const sections = [];

const allSections = document.querySelectorAll(".cal_cards_row");

let allLinks = document.querySelectorAll(".scroll_to_btns");

allSections.forEach( (currentSection)=>{
  sections.push(currentSection.clientHeight);
});

const min_elm_hieght = Math.min(...sections);
const nagtive_height = -1 * Number(min_elm_hieght);

const backToDefault = ()=>{
  const ActivesSecs = document.querySelectorAll(".active_section");
  ActivesSecs.forEach( (activeSec)=>{
    activeSec.classList.remove("active_section");
  });

  const ActivesLinks = document.querySelectorAll(".active_link");
  ActivesLinks.forEach( (activeLink)=>{
    activeLink.classList.remove("active_link");
  });

};
window.addEventListener( 'scroll', ()=>{
  let activeSection = null;
  let activeLink = null;

  if (!allLinks){
    allLinks = document.querySelectorAll(".scroll_to_btns");
  }

  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
    // if small screen it will be hard to reach last section so manual handle it
    activeSection = allSections[allSections.length-1];
  } else {
    for (let i=0; i<allSections.length; i++){
       let top = allSections[i].getBoundingClientRect().top;
       let active = top > (nagtive_height + 50) && top < min_elm_hieght;

       if (active==false && i==0){
       }
       if (active){
         activeSection = allSections[i];
         break;
       }
    }
  }
  backToDefault();
  if (activeSection){
    activeSection.classList.add("active_section");
    const getLink = document.querySelector(`div.scroll_to_btns[data-target='${activeSection.id}']`);
    if (getLink){
      activeLink = getLink;
      getLink.classList.add("active_link");
    }
  }

});

// switch month form
const monthForms = document.querySelectorAll(".month_form");
monthForms.forEach( (monthForm)=>{
  monthForm.addEventListener("click", (event)=>{
    if (event.target.nodeName.toLowerCase() == 'form'){
      event.target.submit();
    } else {
      if (event.currentTarget.nodeName.toLowerCase() == 'form'){
        event.currentTarget.submit();
      } else {
        let current_parent = event.target.parentElement;
        for (let i=0; i<4; i++){
          if (current_parent.nodeName.toLowerCase() == 'form'){
            current_parent.submit();
            break;
          } else {
            current_parent = event.target.parentElement;
          }
        }
      }

    }
  });
});

/* open booking modal */
const startFromSlotA = document.getElementById('start_from_slot_a');
const endAtSlotA = document.getElementById('end_at_slot_a');
const bookingDateA = document.getElementById('booking_date_a');
const periodDescriptionA = document.getElementById('period_description_a');
const periodDateTimeA = document.getElementById('period_date_time_a');
const reservationSlotId = document.getElementById('reservation_slot_id');
const slotIndexA = document.getElementById('slot_index_a');

const allBookingOpenBtns = document.querySelectorAll(".book_open_btn");
allBookingOpenBtns.forEach( (bookOpen)=>{
  bookOpen.addEventListener("click", (event)=>{
    const targetElement = event.currentTarget;
    if (!targetElement.classList.contains('book_open_btn')){
      return false;
    }
    const SlotEmpty = targetElement.getAttribute("data-slot-empty");
    if (SlotEmpty != '0'){
      startFromSlotA.innerText = targetElement.getAttribute("data-slot-start_from");
      endAtSlotA.innerText = targetElement.getAttribute("data-slot-end_at");
      bookingDateA.innerText = targetElement.getAttribute("data-period-date");
      periodDescriptionA.innerText = targetElement.getAttribute("data-period-description");
      periodDateTimeA.innerText = targetElement.getAttribute("data-period-date");
      reservationSlotId.value = targetElement.getAttribute("data-slot-id");
      slotIndexA.innerText = targetElement.getAttribute("data-slot-index");
    }
  });
});


/* sound effects not owned by current user */
const allUsedSlots = document.querySelectorAll(".used_slot");
allUsedSlots.forEach( (slot)=>{
  slot.addEventListener("click", ()=>{
    playSound("#unable_open_modal");
    return true;
  });
});

const allEmptySlots = document.querySelectorAll(".empty_slot");
allEmptySlots.forEach( (slot)=>{
  slot.addEventListener("click", ()=>{
    playSound("#open_modal_sound");
    return true;
});
});

const addResAisde = document.querySelector(".aside_add_res");
addResAisde.addEventListener("click", ()=>{playSound("#open_modal_sound")});

/* ############## AJAX ############## */
const postData = async function (url="", data={}){
  const response = await fetch(url, {
        method: "POST",
        credentials: "same-origin",
        headers:{
         "Content-Type": "application/json"
       },
       body: JSON.stringify(data)
     }
   );
   try{
      const res = await response.json();
      //console.error(res);
      return res;
    }catch(err){
      console.error(err);
    }
}
/*
const reservationName = document.getElementById('reservation_name');
const reservationComment = document.getElementById('reservation_comment');
const supcalToken = document.getElementById('supcal_token');


async function bookingFunction(event){
  const res = await postData('', {
    reservation_slot_id: reservationSlotId.value,
    reservation_name: reservationName.value,
    reservation_comment: reservationComment.value,
    secuirty_token: supcalToken.value
  });
}
const addReservationBtn = document.querySelector("#add_reservation");
addReservationBtn.addEventListener("click", bookingFunction );
*/


/* AJAX Map New Reservation advanced UX */
window.addEventListener('DOMContentLoaded', (event) => {

const mapDayLevel2 = document.querySelector("#map_day_level2");
const mapDayLevel3 = document.querySelector("#map_day_level3");
const level3StartFrom = document.querySelector("#level3_start_from");
const level3EndAt = document.querySelector("#level3_end_at");
const reservationSlotIdMap = document.querySelector("#reservation_slot_id_map");
const reservationPTitleMap = document.querySelector("#reservation_ptitle_map");
const mapNewPeriodsCont = document.querySelector("#map_reservation_periods_container");
const mapBookingModalOpen = document.querySelector("#map_booking_modal_open");
let periodIndex = 0;

function backEveryThingMap(){
  mapDayLevel3.style.display = "none";
  mapDayLevel2.style.display = "none";
  mapNewPeriodsCont.innerHTML = '';
  periodIndex = 0;
}
mapBookingModalOpen.addEventListener("click", backEveryThingMap);

function goTomapLevel2(){
  mapDayLevel3.style.display = "none";
  mapDayLevel2.style.display = "block";
  mapNewPeriodsCont.innerHTML = '';
}



function goTomapLevel3(event){
  mapNewPeriodsCont.innerHTML = '';
  const slotId = event.target.value;
  const slotStartFrom = event.target.getAttribute('data-start');
  const slotEndAt = event.target.getAttribute('data-end');
  const periodTitle = event.target.getAttribute('data-period-title');
  displayAddReservationForm(slotId, slotStartFrom,  slotEndAt, periodTitle);
}


function displayAddReservationForm(slot_id, period_title, start_at, end_from){
  mapDayLevel3.style.display = "block";
  reservationSlotIdMap.value = slot_id;
  level3StartFrom.innerText = period_title;
  level3EndAt.innerText = start_at;
  reservationPTitleMap.innerText = end_from;
}


const displayErrorAjaxMap = (error_msg)=>{
  const mapErrorCont = document.querySelector("#map_error_cont");
  mapErrorCont.innerHTML = `
  <div class="alert alert-danger alert-dismissible fade show">
    <p>${error_msg}</p>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>`;
}


function addSlot(slot_id, start_from, end_it, period_title, empty){
  let slot_input = '';
  if (empty == '1'){
    slot_input =  `
    <div class="container">
      <input type="radio" value="${slot_id}"  class="map_select_slot_id"
                         data-start="${start_from}" data-end="${end_it}" data-period-title="${period_title}">
    </div>`;
  } else {
    slot_input = `
     <div class="container">
      <div class="badge bg-info">Not Avail</div>
     </div>
    `;
  };
  const startPMorAM = Number(start_from.slice(0, 2)) > 12 ? 'PM' : 'AM';
  const endPMorAM = Number(end_it.slice(0, 2)) > 12 ? 'PM' : 'AM';

  const slotHTML =
  `     <!-- slot start -->
        <div class="border border-primary">
         <div class="d-flex text-center flex-fill p-2 mb-1">
            <div class="d-flex justify-content-center align-items-between text-center flex-fill border border-primary p-2">
               <div class="badge bg_cornflowerblue flex-fill max_width_30 fontbold">${start_from.slice(0, 5)} ${startPMorAM}</div>
               <div class="badge bg_palevioletred flex-fill max_width_30 fontbold">${end_it.slice(0, 5)} ${endPMorAM}</div>
               <div class="badge bg-light flex-fill max_width_30">
                 ${slot_input}
               </div>
            </div>
         </div>
        </div>
        <!-- slot end -->
  `;
 return slotHTML;
}


function addPeriod(period_id, period_title){
  const newPeriod = document.createElement("div");
  newPeriod.classList.add("d-flex", "flex-wrap", "flex-column", "border", "border-secondary", "mt-2");
  const periodId = `new_period_${period_id}`;
  newPeriod.setAttribute("id", periodId);
  mapNewPeriodsCont.appendChild(newPeriod);
  newPeriod.innerHTML = "<h5 class='text-center p-2 text-white bg_darkkhaki fontbold'>"+period_title+"</h5>";
  return periodId;
}
async function getDayPeriodsAndSlots(event){
  // send ajax request to get periods and slots data
  const selectedDay = event.target.value;
  const currentCalId = event.target.getAttribute("data-cal-id");
  if (!selectedDay || !currentCalId){return false;}

  const periodsAndSlotsData = await postData('',{map_reservation_date:selectedDay, map_cal_id:currentCalId});
  // incase unknown problem like calendar open unavail years that not happend without break db and code but when it handled friendly
  if (periodsAndSlotsData.code != 200){
    displayErrorAjaxMap(periodsAndSlotsData.message);
    backEveryThingMap();
    return false;
  }
  if (periodsAndSlotsData.data.length < 1){
    displayErrorAjaxMap("No Periods Found For selected Day");
    backEveryThingMap();
    return false;
  }
  const periodsData = periodsAndSlotsData.data;
  goTomapLevel2();
  for (let i=0; i<periodsData.length; i++){
    const currentPeriod = periodsData[i].period;
    const currentSlots = periodsData[i].slots;

    const periodId = addPeriod(currentPeriod.id, currentPeriod.period_title);
    const getPeriod = document.getElementById(periodId);
    // slots data
    for (let s=0; s<currentSlots.length; s++){
      getPeriod.innerHTML += addSlot(currentSlots[s].id, currentSlots[s].start_from, currentSlots[s].end_at, currentPeriod.period_title, currentSlots[s].empty);
    }
  }

  const slotmapIdInputs = document.querySelectorAll(".map_select_slot_id");
  slotmapIdInputs.forEach( (inputElm)=>{
    inputElm.addEventListener( "change", goTomapLevel3 );
  });
}
const mapRservationDate = document.querySelector("#map_reservation_date");
mapRservationDate.addEventListener( "change", getDayPeriodsAndSlots );

});

/* AJAX MAP new Reservation end */




    </script>
  </body>
</html>
