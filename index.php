<?php
require_once('config.php');
require_once('controllers/IndexController.php');
ob_start();

/* Handle Used Calendar part and get all data needed from controller like the MVC but modifed by include controller instead of get the request */
$current_calendar = null;
$used_calendar_emessage = '';
$index_controller = null;
$current_months = null;
$current_weeks = null;
$cal_years = null;
$current_year = null;
$cal_id = null;


// this important it handle all exception from indexController
try {
  // if you get here, all is fine and you can use $object
  $current_month = isset($_GET['month']) && !empty($_GET['month']) ? test_input($_GET['month']) : null;
  global $pdo;
  $index_controller = new IndexController($pdo, $current_month);
  $current_calendar = $index_controller->get_used_calendar();
  $current_months = $index_controller->get_current_months();
  $cal_years = $index_controller->get_years();
  $current_year = $index_controller->get_current_year();
  $current_weeks = $index_controller->get_current_weeks();
  define('Calid', $current_calendar->get_id());
  define('TITLE', $current_calendar->get_title());
  define('DESCRIPTION', $current_calendar->get_description());
  define('THUMBNAIL', $current_calendar->get_background_image());

  //$cal_id = $current_calendar->get_id();
}
catch( Exception $e ) {
  // if you get here, something went terribly wrong.
  // also, $object is undefined because the object was not createDocumentFragment
  $used_calendar_emessage = $e->getMessage();
}

// all current years $index_controller->get_years()
//$index_controller->set_current_year($index_controller->return_all_years($current_calendar->get_id()));
//print_r($index_controller->return_current_months(275));
//print_r($index_controller->get_current_year());
//print_r($index_controller->return_curent_month($index_controller->get_current_year()->get_id(), $index_controller->get_today_month()));


// note any thing will used will be after exaption handle as it die with error message
//die();
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
      .shadow1{
      text-shadow: 1px 1px 2px black, 8px 0 25px gray, 3px 0 5px darkblue;
      }
      .text_shadow01 {
      text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px lightgray;
      }
      .text_shadow02 {
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 5px darkblue;
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
      min-height: 250px;
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


                <div class="flex-fill border border-primary btn btn-light mt-1 mb-1 aside_add_res" data-bs-toggle="modal" data-bs-target="#mapBookingModal">
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

                        $selected_day = $current_weeks[$w][$d];
                        if ($selected_day == false){
                          // empty day

                          ?>
                          <!-- empty day -->
                          <!-- day start -->
                          <div class="flex-fill border border-light cal_card_cell day_card null_day" title="This day is not available The selected month is : <?php echo $week_count; ?> Days">

                          </div>
                          <?php
                        } else {
                          $day_id = $selected_day->get_id();
                          $day = $selected_day->get_day();
                          $day_date = $selected_day->get_day_date();
                          $day_name = $selected_day->get_day_name();
                        ?>
                        <!-- day start -->
                        <div class="flex-fill border border-light cal_card_cell day_card" id="day_<?php echo $day_id;  ?>">

                           <!-- day meta -->
                             <h6 class="text-center"><?php echo $day_name . ' ' . $day; ?></h6>
                             <h6 class="text-center bg-light text-black badge"><?php echo $day_date; ?></h6>
                             <!-- array_distribution -->
                           <!-- all periods start -->
                           <div class="all_periods">


                             <!-- period example start -->
                             <div class="period_background_default">
                                <!-- period title -->
                                <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                                <!-- all slots start -->
                                  <!-- slot start -->
                                  <div class="slot_background_default m-1 empty_slot">
                                   <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                     <i class="fa text-primary fa fa-calendar-o" style="font-size:1.1em;"></i>
                                   </div>
                                  </div>
                                  <!-- slot end -->

                                  <!-- slot start -->
                                  <div class="slot_background_default m-1 used_slot">
                                   <div class="container">
                                     <i class="fa fa-calendar-check-o" style="font-size:1.1em;"></i>
                                   </div>
                                  </div>
                                  <!-- slot end -->

                                  <!-- slot start -->
                                  <div class="slot_background_default m-1">
                                   <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                     <i class="fa text-primary">&#xf271;</i>
                                   </div>
                                  </div>
                                  <!-- slot end -->

                                <!-- all slots end -->
                             </div>
                             <!-- period example end -->

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


<!-- Booking modal start -->
<div class="modal fade" id="bookingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header ">
        <h5 class="modal-title ">Booking (02/16/2022) <i class="fa fa-calendar"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <h6>Period Notes:</h6>
          <p>you must come with laptop.</p>
          <form action="/action_page.php">
            <div class="mb-3 mt-3">
              <label for="comment">Comments:</label>
              <textarea class="form-control" rows="3" id="comment" name="text"></textarea>
            </div>
            <p class="alert alert-info text-center">Please click 'Confirm' to confirm your booking start at  <br /> <span id="start_from_slot p-1" class="bg-primary text-white badge">12:00PM</span> and end at <span class="bg-success text-white badge" id="badge end_at_slot">02:00PM</span></p>
            <button type="submit" class="btn btn-primary">Confirm Booking </button>
          </form>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- Booking modal end -->



<!-- Booking modal start -->
<div class="modal fade" id="mapBookingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header ">
        <h5 class="modal-title "> Create new Booking <i class="fa fa-calendar-o"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <form action="controllers/index_controller.php">
            <div class="form-group">
              <label>Pick a Date</label>
              <input class="form-control" name="map_reservation_date" id="map_reservation_date" type="date"  min="2022-01-01" max="2024-01-01">
            </div>
            <div class="mb-3 mt-3">
              <label for="comment">Booking Notes:</label>
              <textarea class="form-control" rows="3" id="comment" name="text" placeholder="Booking Notes.."></textarea>
            </div>
            <p class="alert alert-info text-center">Please click 'Confirm' to confirm your booking start at  <br /> <span id="start_from_slot p-1" class="bg-primary text-white badge">12:00PM</span> and end at <span class="bg-success text-white badge" id="badge end_at_slot">02:00PM</span></p>
            <button type="submit" class="btn btn-primary">Confirm Booking </button>
          </form>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- Booking modal end -->


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




    </script>
  </body>
</html>
