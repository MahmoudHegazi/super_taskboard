<?php
ob_start();
require_once('config.php');
require_once('controllers/IndexController.php');
if (!isset($_SESSION['logged']) || empty($_SESSION['logged']) || !isset($_SESSION['logged_id']) || empty($_SESSION['logged_id']) || !isset($_SESSION['name'])){
  $_SESSION['message_login'] = 'Please Login To acces this page';
  $_SESSION['success_login'] = False;
  header("Location: ./login.php");
  die();
  return False;
}

$logged_userid = test_input($_SESSION['logged_id']);
$logged_uname = test_input($_SESSION['name']);

$user_role = 'user';
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
$styles_string = '';

$error = false;

$current_month = null;
if (isset($_GET['month']) && !empty($_GET['month'])){
  $current_month = intval(test_input($_GET['month']));
  $_SESSION['month'] =  $current_month;
}

if (is_null($current_month) && isset($_SESSION['month'])){
  $current_month = $_SESSION['month'];
}


$current_year = null;
if (isset($_GET['year']) && !empty($_GET['year'])){
  $current_year = intval(test_input($_GET['year']));
  $_SESSION['year'] =  $current_year;
}

if (is_null($current_year) && isset($_SESSION['year'])){
  $current_year = $_SESSION['year'];
}

// this important it handle all exception from indexController
try {
  // Index Controller and view setup
  // if you get here, all is fine and you can use $object

  // system has default start year and month always we need keep everything can work year or month so when first load it load normal will nulls user can change months when change months he not change loaded year so ok after that when he change year he will set the cookie for year and before he set cookie of month so he will be in the target year and month

  $index_controller = new IndexController($pdo, $current_month, $request_type, $current_year, $logged_userid);

  $get_role = $index_controller->return_current_logged_role($logged_userid);
  if (isset($get_role) && !empty($get_role)){
    $user_role = $get_role == 'admin' ? 'admin' : 'user';
  }

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
  $styles_string = $index_controller->get_current_styles();

}
catch( Exception $e ) {
  $used_calendar_emessage = $e->getMessage();
  $error = true;
}

// load containers bs and check if loaded or not
function setupBSElementsData($inC, $element_id, $type='container', $default_classes=''){
  if ($type == 'container'){
    $classes = empty($default_classes) ? 'd-flex justify-content-center align-items-end not_saved' : $default_classes . ' not_saved';
    $title_container = $inC->getElement($element_id, $type);
    // this help js to connected with php esaily for save the new added elements so if u need add element and active make it like other elements
    $title_container_bs = !empty($title_container) ? $title_container->get_bootstrap_classes() : $classes;
    return $title_container_bs;
  } else {
    $element = $inC->getElement($element_id, $type);
    if (!isset($element) || empty($element)){
      return $default_classes . ' not_saved_element';
    } else {
      return $element->get_bootstrap_classes();
    }
  }
  // note this default bs classes for containers (not_saved to tell send message to js when load elements controlled by user to setup, all needed now js know with legal way 'class' not php to js what php need to do with elements)

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
    <meta charset="utf-8">

    <!-- local data -->
    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <style>
      body, html {
      margin: 0;
      height: auto;
      font-family: georgia;
      font-size: 16px;
      width: 100%;
      }
      .active_toggle{
        box-shadow: 0 4px 8px 0 rgba(200, 200, 200), 0 6px 20px 0 rgba(0, 150, 200, .06) !important;
      }
      .main_view {
        width: 100% !important;
        max-width: 100% !important;
        height:max-content;
      }
      /* scroll style */
      ::-webkit-scrollbar {
        width: 20px;
        height: 10px !important;
      }
      /* Track */
      ::-webkit-scrollbar-track {
        box-shadow: inset 0 0 5px grey;
        border-radius: 10px;
      }

      /* Handle */
      ::-webkit-scrollbar-thumb {
        background: url('assets/images/scrollbg.jpg');
        border-radius: 10px;
      }

      /* Handle on hover */
      ::-webkit-scrollbar-thumb:hover {
        opacity: 0.8;
        box-shadow: inset 2px 0 5px lightblue;
      }
      .options_parent {min-height: 175px;}
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
      color: dimgray;
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
      .cursor_pointer{
        cursor:pointer;
      }
      .text_black{color: black;}
      .font_80em {font-size: .80em;}

      .max_width_30{
        max-width: 30% !important;
      }
      .the_width80{ width: 100% !important;}
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

      .owned_byLoged,.owned_byLoged_remove,.owned_byLoged_reserv {
        cursor: pointer;
      }
      .owned_byLoged_remove:hover{
        background: darkred !important;
        color: white !important;
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

      /* style controller aside */
      div.active_aside {
         position: fixed;
         display: none;
         bottom: 0;
         right: 4px;
         overflow: auto;
         height: 100%;
         width: 22%;
         background: cadetblue;
         border: 3px solid lightgray;
         box-shadow: 0 2px 3px 0 khaki, 0 2px 8px 0 lightgray;
       }

       .style_editor_title{
      margin: 5px;
      text-shadow: 1px 1px 2px lightgray, 0 0 25px blue, 0 0 5px gold;
            box-shadow: 0 2px 3px 0 khaki, 0 2px 8px 0 lightgray;
      }

      .magic_icon{
      color: khaki;
      text-shadow: 1px 1px 2px lightgreen, 0 0 25px blue, 0 0 5px gold;
    }
    .toggle_asside{
      cursor: pointer;
    }

    .toggle_asside:hover{
      text-shadow: 1px 1px 2px red, 0 0 25px blue, 0 0 5px gold;
    }

    .style_viewercss{
      min-height:100%;
      height: auto;
      min-width: 80%;
      max-width: 100%;
      width: 100%;
      box-shadow: 0 2px 3px 2px lightgray, 0 2px 8px 0 gray;
    }



      /* media query programing */
      @media only screen and (max-width: 900px) {
       /* ipad */
      .full_day {display: none !important;}
      .short_day{display: block !important;}

      div.slot_background_default div.justify-content-between{ justify-content: center !important; }

       .period_title_default {
          max-width:80% !important;
          overflow:hidden;
          font-size: .525rem;
          padding:0;
      }
      .active_aside {width: 40% !important;}

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

      .active_aside {width: 40% !important;}

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
      form.active_small_button {
          background: #41e15c85 !important;
          box-shadow: 0px 4px 0 4px rgba(50, 50, 50, .4), 2px 2px 2px 1px rgba(255, 255, 255, .8);
      }

    </style>
  </head>
  <body>


<div class="main_view bg-white">

    <div class="container-fluid p-2 text-white text-center cal_title bg_dimgray">


      <div data-editor-type="container" data-editor-class="title_container_cs"
      class="<?php echo setupBSElementsData($index_controller, 'title_container_new', 'container'); ?> display-6 mt-2 mb-3 text-white p-2 default_shadow text_shadow03 border border-secondary title_container_cs" id="title_container_new" >

      <img data-editor-type="element" id="logo_image" data-editor-class="logo_image_css"  class="logo_image_css <?php echo setupBSElementsData($index_controller, 'logo_image', 'element'); ?>"
      src="<?php echo defined('THUMBNAIL') ? 'uploads/images/' . THUMBNAIL : 'uploads/images/default_logo.png'; ?>" alt="Calendar Logo" height="50" width="50">
      <span data-editor-type="element" id="title_text" data-editor-class="title_text_css" class="title_text_css <?php echo setupBSElementsData($index_controller, 'title_text', 'element'); ?>">
        <?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></span>
      </div>






        <div data-editor-type="container" data-editor-class="desc_class"
        class="<?php echo setupBSElementsData($index_controller, 'desc_id', 'container'); ?> desc_class description_p bg_azure  text-black border border-secondary p-2 default_shadow" id="desc_id" >

        <?php echo defined('DESCRIPTION') ? DESCRIPTION : 'Booking Calendar'; ?>
      </div>

      <div data-editor-type="container" id="top_nav" data-editor-class="top_nav_css" class="top_nav_css container border border-succcess p-2 <?php echo setupBSElementsData($index_controller, 'top_nav', 'container'); ?>">

        <?php
          if (isset($_SESSION['logged_id']) && !empty($_SESSION['logged_id'])){
            ?>
            <a href="./logout.php" class="<?php echo setupBSElementsData($index_controller, 'logout_page_link', 'element'); ?> logout_page_link_css btn btn-danger" id="logout_page_link" data-editor-type="element" data-editor-class="logout_page_link_css">Log Out</a>
            <span style="width:10px;"></span>
            <a href="./profile?uid=<?php echo $_SESSION['logged_id'];  ?>" class="<?php echo setupBSElementsData($index_controller, 'profile_page_link', 'element'); ?> profile_page_link_css btn btn-success" id="profile_page_link" data-editor-type="element" data-editor-class="profile_page_link_css">Profile</a>
            <?php
          }
         ?>
        <!-- fake margin for flex -->
        <span style="width:10px;"></span>
        <?php if ($user_role == 'admin'){
          ?>
            <a href="./setup.php" class="<?php echo setupBSElementsData($index_controller, 'setup_page_link', 'element'); ?> setup_page_link_css btn btn-primary" id="setup_page_link" data-editor-type="element" data-editor-class="setup_page_link_css">Setup</a>
          <?php
        } ?>
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
            <div data-editor-type="container" id="month_switcher_grid" data-editor-class="month_switcher_grid_css"
            class="row container-fluid options_parent bg_dimgray p-2 month_switcher_grid_css <?php echo setupBSElementsData($index_controller, 'month_switcher_grid', 'container'); ?>">
              <!-- month controller start -->
              <div data-editor-type="container" id="monthswitcher_main" data-editor-class="monthswitcher_main_css"
               class="monthswitcher_main_css col-sm-12 <?php echo setupBSElementsData($index_controller, 'monthswitcher_main', 'container'); ?>" style="width: 90%;height: fit-content;">
              <div class="row flex-fill d-flex justify-content-center ">
              <div id="month_controler_container" class="col-sm-5">
                <!-- month switcher start -->
                <div data-editor-type="container" id="monthswitcher_parent" data-editor-class="monthswitcher_css"
                  class="monthswitcher_css container month_row flex-wrap p-2 text-black border border-light <?php echo setupBSElementsData($index_controller, 'monthswitcher_parent', 'container', 'd-flex align-items-start justify-content-between'); ?>">
                  <i data-editor-type="element" id="monthswitcher_left" data-editor-class="monthswitcher_left_css"
                   class="monthswitcher_left_css display-6 flex-fill fa fa-arrow-circle-left text-white month_arrow <?php echo setupBSElementsData($index_controller, 'monthswitcher_left', 'element'); ?>"
                  data-month="<?php
                  if (is_numeric($index_controller->get_current_month()->get_month())){
                    echo $index_controller->get_current_month()->get_month() >= 2 ? $index_controller->get_current_month()->get_month() - 1 : 1;
                  }
                  ?>"
                  ></i>
                  <h3 data-editor-type="element" data-editor-class="monthswitcher_select_css"
                      id="selected_month_name" class="flex-fill month_name text_shadow01 text-white monthswitcher_select_css <?php echo setupBSElementsData($index_controller, 'selected_month_name', 'element'); ?>">
                    <?php if (!is_null($index_controller->get_current_month())){
                      $dateObj = DateTime::createFromFormat('!m', $index_controller->get_current_month()->get_month());
                      $monthName = $dateObj->format('F');
                      echo $monthName;
                    } ?>
                  </h3>
                  <i data-editor-type="element" data-editor-class="monthswitcher_right_css"
                     id="monthswitcher_right" class="monthswitcher_right_css display-6 flex-fill fa fa-arrow-circle-right text-white month_arrow <?php echo setupBSElementsData($index_controller, 'monthswitcher_right', 'element'); ?>"
                  data-month="<?php
                  if (is_numeric($index_controller->get_current_month()->get_month())){
                    echo $index_controller->get_current_month()->get_month() <= 12 ? $index_controller->get_current_month()->get_month() + 1 : 12;
                  }
                  ?>"></i>
                </div>
                <!-- month switcher end -->
              </div>
              <!-- month controller end -->

              <div data-editor-type="container" id="year_select_cont"
                 data-editor-class="year_select_cont_css" class="year_select_cont_css col-sm-5 rounded <?php echo setupBSElementsData($index_controller, 'year_select_cont', 'container'); ?>">
                <!-- year display start -->
                <form data-editor-type="container" id="year_select_form"
                   data-editor-class="year_select_form_css" class="year_select_form_css flex-fill <?php echo setupBSElementsData($index_controller, 'year_select_form', 'container'); ?>" action="./index.php" method="GET" id="year_form">
                  <select data-editor-type="element" data-editor-class="year_select_css"
                     class="year_select_css form-control" name="year" id="year" <?php echo setupBSElementsData($index_controller, 'year', 'element'); ?>>
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
              <!-- remove bg_dmgriay !Important -->
              <div data-editor-type="container" id="month_numbers_main" data-editor-class="month_numbers_main_css"
                class="month_numbers_main_css col-sm-12 bg_dimgray p-2 <?php echo setupBSElementsData($index_controller, 'month_numbers_main', 'container'); ?>">
                <!-- months numbers switch month -->
                <div data-editor-type="container" id="month_numbers" data-editor-class="month_numbers_css"
                  class="btn-group btn-sm flex-wrap month_small_btns month_numbers_css <?php echo setupBSElementsData($index_controller, 'month_numbers', 'container'); ?>">
                  <?php

                    if ($current_months && is_array($current_months) && !empty($current_months)){
                      for ($m=0; $m<count($current_months); $m++){
                        $is_active_class = ($current_months[$m]->get_month() == $current_month) ? 'active_small_button' : '';
                        $btnidhtml = 'btn_month_' . ($m +1);

                        ?>
                        <!-- change month by number better UX option for old man -->
                        <form  id="<?php echo $btnidhtml;?>"
                        data-editor-type="element"
                        data-month="<?php echo $current_months[$m]->get_month(); ?>"
                        data-editor-class="btn_arrow_month"
                        data-editor-group="1"
                        class="btn_arrow_month month_form bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn <?php echo setupBSElementsData($index_controller, $btnidhtml, 'element'); ?>">
                        <span class="p-1 text-center"> <?php echo $current_months[$m]->get_month(); ?></span>
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
        </div>


          </div>
          <div data-editor-type="container" data-editor-class="calendar_first_container_css"  id="calendar_first_container"
            class="p-1 col-sm-12 bg_dimgray calendar_first_container_css <?php echo setupBSElementsData($index_controller, 'calendar_first_container', 'container'); ?>">

            <!-- Calendar display start -->
            <div data-editor-type="main" data-editor-class="main_calendar_container"  id="main_calendar_container_css"
              class="main_calendar_container_css calendar border border-dark p-2 mt-3 mb-5 container-fluid bg-white">
              <div data-editor-type="container" id="cal_date_title_cont" data-editor-class="cal_date_title_contcss"
              class="cal_date_title_contcss <?php echo setupBSElementsData($index_controller, 'cal_date_title_cont', 'container'); ?>">


              <h5 data-editor-type="element" id="cal_date_title_elm" data-editor-class="cal_date_title_contcss" class="cal_date_title_contcss text_black <?php echo setupBSElementsData($index_controller, 'cal_date_title_elm', 'element'); ?>"><?php
              $smonth = intval($index_controller->get_current_month()->get_month()) > 9 ? $index_controller->get_current_month()->get_month() : '0' . $index_controller->get_current_month()->get_month();

              echo $index_controller->get_current_year()->get_year() . '-' . $smonth . '-01'; ?></h5></div>
              <!-- week Titles row start -->
              <div data-editor-type="container" id="day_namecont" data-editor-class="day_namecont_css"
                class="day_namecont_css p-2 cal_days_titles <?php echo setupBSElementsData($index_controller, 'day_namecont', 'container'); ?>">

                <div data-editor-type="element" id="day_name_parent1" data-editor-class="day_name_parentcss" data-editor-group="2"
                class="day_outer_name_contcss flex-fill border border-light cal_card_cell">
                  <span data-editor-type="element" id="day_mon" data-editor-class="day_mon_css" class="full_day day_mon_css <?php echo setupBSElementsData($index_controller, 'day_mon', 'element'); ?>">Monday</span>
                  <span data-editor-type="element" id="day_mon_mob" data-editor-class="day_mon_mob_css" class="short_day day_mon_mob_css <?php echo setupBSElementsData($index_controller, 'day_mon_mob', 'element'); ?>" style="display:none;">Mon</span>
                </div>
                <div data-editor-type="element" id="day_name_parent2" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent2', 'element'); ?>">
                  <span data-editor-type="element" id="day_tue" data-editor-class="day_tue_css" class="full_day day_tue_css <?php echo setupBSElementsData($index_controller, 'day_tue', 'element'); ?>">Tuesday</span>
                  <span data-editor-type="element" id="day_tue_mob" data-editor-class="day_tue_mob_css" class="short_day day_tue_mob_css <?php echo setupBSElementsData($index_controller, 'day_tue_mob', 'element'); ?>" style="display:none;">Tue</span>
                </div>
                <div data-editor-type="element" id="day_name_parent3" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent3', 'element'); ?>">
                  <span data-editor-type="element" id="day_wed" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_wed', 'element'); ?>">Wednesday</span>
                  <span data-editor-type="element" id="day_wed_mob" data-editor-class="day_wed_mob_css" class="short_day day_wed_mob_css <?php echo setupBSElementsData($index_controller, 'day_wed_mob', 'element'); ?>" style="display:none;">Wed</span>
                </div>
                <div data-editor-type="element" id="day_name_parent4" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent4', 'element'); ?>">
                  <span data-editor-type="element" id="day_thu" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_thu', 'element'); ?>">Thursday</span>
                  <span data-editor-type="element" id="day_thu_mob" data-editor-class="day_thu_mob_css" class="short_day day_thu_mob_css <?php echo setupBSElementsData($index_controller, 'day_thu_mob', 'element'); ?>" style="display:none;">Thu</span>
                </div>
                <div data-editor-type="element" id="day_name_parent5" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent5', 'element'); ?>">
                  <span  data-editor-type="element"id="day_fri" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_fri', 'element'); ?>">Friday</span>
                  <span  data-editor-type="element" id="day_fri_mob" data-editor-class="day_fri_mob_css" class="short_day day_fri_mob_css <?php echo setupBSElementsData($index_controller, 'day_fri_mob', 'element'); ?>" style="display:none;">Fri</span>
                </div>
                <div data-editor-type="element" id="day_name_parent6" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_fri_mob', 'element'); ?>">
                  <span  data-editor-type="element" id="day_sat" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_sat', 'element'); ?>">Saturday</span>
                  <span  data-editor-type="element" id="day_sat_mob" data-editor-class="day_sat_mob_css" class="short_day day_sat_mob_css <?php echo setupBSElementsData($index_controller, 'day_sat_mob', 'element'); ?>" style="display:none;">Sat</span>
                </div>
                <div data-editor-type="element" id="day_name_parent7" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent7', 'element'); ?>">
                  <span  data-editor-type="element" id="day_sun" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_sun', 'element'); ?>">Sunday</span>
                  <span  data-editor-type="element" id="day_sun_mob" data-editor-class="day_sun_mob_css" class="short_day day_sun_mob_css <?php echo setupBSElementsData($index_controller, 'day_sun_mob', 'element'); ?>" style="display:none;">Sun</span>
                </div>
              </div>
              <!-- week Titles row end -->
              <!-- hidden week scroll buttons -->
              <div data-editor-type="container" id="weekscroll_container" data-editor-class="weekscroll_containercss"  class="weekscroll_containercss flex-column p-2 <?php echo setupBSElementsData($index_controller, 'weekscroll_container', 'container'); ?>" style="position: fixed;left:0;top:0;background: transparent;width:fit-content;max-width:100% !important; font-size:10px;margin:0;padding:0 !important;">
                <?php
                  if ($current_weeks && !empty($current_weeks)){
                    for ($cw=0; $cw<count($current_weeks); $cw++){
                      $week_id = 'week_' . ($cw+1);
                      ?>
                      <div data-editor-type="element"
                      id="<?php echo 'scrollbtn_'.$week_id; ?>" data-editor-group="3" data-editor-class="scroll_left_btncss"
                      class="scroll_to_btns scroll_left_btncss flex-fill border border-primary btn  btn-secondary text-white  mt-1 mb-1 <?php echo setupBSElementsData($index_controller, ('scrollbtn_'.$week_id), 'element'); ?>"
                      data-target="<?php echo $week_id; ?>"><?php echo ($cw+1); ?></div>
                      <?php
                    }
                  }
                ?>

                <div data-editor-type="element" id="map_booking_modal_open" data-editor-class="map_resevation_css" class="map_resevation_css flex-fill border border-primary btn btn-light mt-1 mb-1 aside_add_res <?php echo setupBSElementsData($index_controller, 'map_booking_modal_open' , 'element'); ?>" data-bs-toggle="modal" data-bs-target="#mapBookingModal">
                 <i class="fa fa-plus text-primary"></i>
                </div>

                <div data-editor-type="element" data-editor-class="open_style_editorcss" id="open_style_editor"
                  class="open_style_editorcss toggle_asside magical_btn flex-fill border border-primary btn  btn-danger text-white  mt-1 mb-1 <?php echo setupBSElementsData($index_controller, 'open_style_editor' , 'element'); ?>"
                  title="Close The Style Editor" data-bs-original-title="Close The Style Editor"><i class="fa fa-magic text-white"></i>
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
                          $delm_id = 'x_day_' . $d . '_' . $index_controller->get_current_month()->get_month();
                          ?>
                          <!-- empty day -->
                          <!-- day start -->
                          <div data-editor-type="element" data-editor-class="day_x_css"
                          data-editor-group="4"  id="<?php echo $delm_id; ?>" class="day_x_css flex-fill border border-light cal_card_cell day_card null_day <?php echo setupBSElementsData($index_controller, $delm_id, 'element'); ?>"
                          title="This day is not available The selected month is : <?php echo $week_count; ?> Days">

                          </div>
                          <?php
                          // day data array([day] => Day Object, [day_data] => Array)
                        } else {
                          $selected_day = $day_data['day'];
                          $day_id = $selected_day->get_id();
                          $day = $selected_day->get_day();
                          $day_date = $selected_day->get_day_date();
                          $day_name = $selected_day->get_day_name();
                          $daycalid = $index_controller->get_used_calendar()->get_id();
                          $day_id_html = 'day_' . $day_id;
                          $title_id_html = 'day_title_'.$day_id . '_' . $daycalid;
                          $date_id_html = 'day_date_'.$day_id . '_' . $daycalid;

                          $selected_day_data = $day_data['day_data'];
                        ?>
                        <!-- day start -->
                        <div data-day="<?php echo $day_name; ?>" class="flex-fill border border-light cal_card_cell day_card day_style_css" id="<?php echo $day_id_html;  ?>">
                           <!-- day meta -->
                             <h6 data-editor-type="element" data-editor-group="5" id="<?php echo $title_id_html;  ?>" data-editor-class="day_title_textcss" class="day_title_textcss text-center font_80em <?php echo setupBSElementsData($index_controller, $title_id_html , 'element'); ?>"><?php echo substr($day_name, 0, 3) . ' ' . $day; ?></h6>
                             <h6 data-editor-type="element" data-editor-group="6" id="<?php echo $date_id_html;  ?>"  data-editor-class="day_date_css"  class="day_date_css text-center bg-light text-black badge <?php echo setupBSElementsData($index_controller, $date_id_html , 'element'); ?>"><?php echo $day_date; ?></h6>
                           <!-- array_distribution -->
                           <!-- all periods start -->
                           <div data-editor-type="container" id="all_periods_container" data-editor-class="all_periods_containercss"  class="all_periods_containercss all_periods flex-column flex-nowrap <?php echo setupBSElementsData($index_controller, 'all_periods_container', 'container'); ?>">
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
                                 $daycalid = $index_controller->get_used_calendar()->get_id();
                                 $period_id_html = 'period_desc_'. $p_id;


                                 /* ##################### display periods   ############################ */
                                 ?>
                                 <!-- period example start -->
                                 <!-- notice here  the id come from database and class u can change also u can add normal css to target some slots in css file many ways -->
                                 <div data-editor-type="container" data-editor-class="period_container_css"
                                 data-editor-group="8" class="<?php echo setupBSElementsData($index_controller, $p_element_id, 'container'); ?> period_container_css flex-column flex-nowrap container period_background_default <?php echo $p_element_class; ?>" id="<?php echo $p_element_id; ?>" >
                                    <!-- period title -->
                                    <span data-editor-type="element" id="<?php echo $period_id_html; ?>" data-editor-group="7" data-editor-class="period_description_css" class="period_description_css badge bg-secondary p-1 mt-1 period_title_default <?php echo setupBSElementsData($index_controller, $date_id_html , 'element'); ?>" ><?php echo $p_description; ?></span>
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

                                        $current_reservation = $index_controller->get_reservation_data_byslot($s_id);
                                        $owned_reservation = isset($current_reservation) && !empty($current_reservation) && (intval($current_reservation->get_user_id()) === intval($logged_userid)) ? 1 : 0;



                                        // used slot
                                        if ($s_empty == 0){
                                          // display used slot
                                          ?>


                                          <!-- slot start booked already -->
                                          <div

                                            data-editor-class="slot_cont_css"
                                            data-editor-type="element"
                                            class="<?php echo setupBSElementsData($index_controller, $s_element_id, 'element'); ?> slot_background_default slot_cont_css p-1 m-1 used_slot <?php echo $s_element_class; ?>"
                                            id="<?php echo $s_element_id; ?>" data-editor-group="8">
                                           <div data-editor-type="container" data-editor-group="9" id="child_<?php echo $s_element_id; ?>" data-editor-class="slot_child_contcss"
                                            class="container slot_child_contcss <?php echo setupBSElementsData($index_controller, 'child_' . $s_element_id, 'container', 'justify-content-between align-items-center'); ?>">

                                             <!-- if this slot owned by logged display controls else nope -->
                                             <?php


                                             if ($owned_reservation || (isset($current_reservation) && !empty($current_reservation) && $user_role === 'admin')){
                                               $user_public_data = $index_controller->return_public_user_data($current_reservation->get_user_id());

                                               $u_name = isset($user_public_data) && !empty($user_public_data) ? $user_public_data->get_name() : '';
                                               $u_email = isset($user_public_data) && !empty($user_public_data) ? $user_public_data->get_email() : '';
                                               $u_role = isset($user_public_data) && !empty($user_public_data) ? $user_public_data->get_role() : '';
                                               $u_username = isset($user_public_data) && !empty($user_public_data) ? $user_public_data->get_username() : '';

                                               echo '<i  data-id="'. $current_reservation->get_id().'" data-start="'.$s_start_from.'" data-end="'.$s_end_at.'"  data-name="'.$current_reservation->get_name().'" data-notes="'.$current_reservation->get_notes().'" data-bs-toggle="modal" data-bs-target="#editReservationModal" class="cursor_pointer text-success fa fa-calendar-check-o  edit_owned_slot" style="font-size:17px;"></i>';
                                               echo'<i data-slot-id="'.$s_id.'" data-id="'. $current_reservation->get_id().'" class="fa text-white fa fa-close border border-light bg-danger rounded owned_byLoged_remove" style="font-size:1.1em;"  data-bs-toggle="modal" data-bs-target="#cancelReservationModal" ></i>';
                                               echo '<i data-show="show_'.$current_reservation->get_id().'"  data-id="'. $current_reservation->get_id().'" data-start="'.$s_start_from.'" data-end="'.$s_end_at.'"  data-name="'.$current_reservation->get_name().'" data-notes="'.$current_reservation->get_notes().'"
                                               data-user-name="'.$u_name.'" data-user-email="'.$u_email.'" data-user-role="'.$u_role.'" data-username="'.$u_username.'" class="text-success fa fa-envelope-o owned_byLoged view_reservation" style="font-size:18px;"></i>';
                                               echo '<button style="display:none !important;" data-bs-toggle="modal" data-bs-target="#viewReservationModal" id="show_'.$current_reservation->get_id().'"></button>';
                                             } else {
                                               // not owned reservation
                                               if ($user_role === 'admin' && !empty($current_reservation)){
                                                 echo'<i title="Admin Remove Reservation" data-slot-id="'.$current_reservation->get_id().'" data-id="x" class="fa text-white fa fa-close border border-light bg-danger rounded owned_byLoged_remove" style="font-size:1.1em;"  data-bs-toggle="modal" data-bs-target="#cancelReservationModal" ></i>';
                                               }
                                               echo '<i title="This Slot Not available" class="fa fa-calendar-check-o not_ownedbyloged_resev" style="font-size:17px;"></i>';
                                               echo '<i title="This Slot Not available" class="fa fa-envelope not_ownedbyloged"></i>';
                                             }
                                             ?>

                                           </div>
                                          </div>
                                          <!-- slot end -->
                                          <?php
                                        } else {
                                          ?>
                                          <!-- slot start with booking diffrent group to diffrence -->
                                          <div data-editor-type="element" data-editor-group="10" data-editor-class="slot_cont_css" class="<?php echo setupBSElementsData($index_controller, $s_element_id, 'element'); ?> slot_background_default slot_cont_css p-1 m-1 empty_slot <?php echo $s_element_class; ?>" id="<?php echo $s_element_id; ?>">
                                           <div data-editor-type="container" data-editor-group="12" id="child_<?php echo $s_element_id; ?>" data-editor-class="slot_child_contcss" class="slot_child_contcss <?php echo setupBSElementsData($index_controller, 'child_' . $s_element_id, 'container', 'd-flex justify-content-between align-items-center'); ?>">
                                             <i class="fa text-primary fa fa-calendar-o book_open_btn" style="font-size:1.1em;" data-slot-id="<?php echo $s_id; ?>"
                                             data-slot-start_from="<?php echo $s_start_from; ?>"
                                             data-slot-end_at="<?php echo $s_end_at; ?>"
                                             data-slot-empty="<?php echo $s_empty; ?>"
                                             data-slot-index="<?php echo $s_slot_index; ?>"
                                             data-period-date="<?php echo $p_date; ?>"
                                             data-period-description="<?php echo $p_description; ?>"
                                             data-uid="<?php echo $logged_userid; ?>"
                                             data-bs-toggle="modal" data-bs-target="#bookingModal" ></i>


                                             <i data-slot-id="<?php echo $s_id; ?>"
                                                data-show-view="<?php echo 'show_view_' . $s_id; ?>"
                                                data-id="<?php echo $s_id; ?>"
                                                data-start="<?php echo $s_start_from; ?>"
                                                data-end="<?php echo $s_end_at; ?>"
                                                data-period-date="<?php echo $p_date; ?>"
                                                data-slot-index="<?php echo $s_slot_index; ?>"
                                                data-period-description="<?php echo $p_description; ?>"
                                               class="fa text-primary fa fa-envelope-o view_empty_slot"
                                                style="font-size:1.1em;"></i>
                                               <button style="display:none !important;" data-bs-toggle="modal"
                                               data-bs-target="#viewSlotModal" id="<?php echo 'show_view_' . $s_id;?>" style="display:none;"></button>
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

</div>

<!-- Booking modal start -->
<div class="modal fade" id="bookingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header ">
        <h5 class="modal-title">Booking <span id="booking_date_a"></span> <i class="fa fa-calendar"></i></h5>
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

              <!-- addmin select user id -->
              <!-- addmin select user id -->
              <?php
               if (isset($index_controller) && !empty($index_controller) && $user_role === 'admin'){
                 ?>
                 <label for="admin_select_userid_add">Select the owner of the reservation</label>
                 <select title="You See this Becuase You Are an admin select the user"
                   class="form-control" id="admin_select_userid_add" name="admin_select_userid_add" required>
                     <?php
                     $public_users_data = $index_controller->return_users_public_data();
                     for ($pu=0; $pu<count($public_users_data); $pu++){
                       $user_id = $public_users_data[$pu]->get_id();
                       $uname = ($user_id === $logged_userid) ? 'You: ' . $public_users_data[$pu]->get_name() : $public_users_data[$pu]->get_name();
                     ?>
                       <option value="<?php echo $user_id; ?>" checked><?php echo $uname; ?></option>
                     <?php
                     }?>
                 </select>
                 <?php
               }
              ?>


              <input id="loggeduid" name="loggeduid" type="hidden" value="<?php echo isset($logged_userid) ? $logged_userid : ''; ?>" required />

                 <input id="supcal_token" name="secuirty_token" type="hidden" value="<?php echo isset($index_controller) ? $index_controller->get_request_secert() : ''; ?>" required />
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

                  <!-- for admin this best secured way instead of interput ajax request or play in this area just load it with php and display noe and in level 3display block -->
                  <?php
                   if (isset($index_controller) && !empty($index_controller) && $user_role === 'admin'){
                     ?>
                     <div class="form-group mb-2 mt-2" id="admin_reservation_owner" style="display:none;">
                     <label for="admin_select_userid_add">Select the owner of the reservation</label>
                     <select title="You See this Becuase You Are an admin select the user"
                       class="form-control mb-2" id="admin_select_userid_add1" name="admin_select_userid_add" required>
                         <?php
                         $public_users_data = $index_controller->return_users_public_data();
                         for ($pu=0; $pu<count($public_users_data); $pu++){
                           $user_id = $public_users_data[$pu]->get_id();
                           $uname = ($user_id === $logged_userid) ? 'You: ' . $public_users_data[$pu]->get_name() : $public_users_data[$pu]->get_name();
                         ?>
                           <option value="<?php echo $user_id; ?>" checked><?php echo $uname; ?></option>
                         <?php
                         }?>
                     </select>
                     </div>
                     <?php
                   }
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


<!-- view empty slot modal start -->
<div class="modal fade" aria-modal="true" role="dialog" id="viewSlotModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">View Slot</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="mt-2 mb-2">
           <h5 class="text-center" id="period_description_viewslot"></h5>
        </div>
        <div class="mb-4 mt-3">
          <p><span>Period Date: </span><span id="period_dateslot_view"></span></p>
          <p>Slot Index: <span id="slot_indexview"></span></p>
        </div>
        <div class="mb-3 mt-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="badge bg-success p-2">start: <span id="view_slotstart_at"></span></div>
              <div class="badge bg-danger p-2">end: <span id="view_slotend_at"></span></div>
            </div>
        </div>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Back</button>
      </div>
    </div>
  </div>
</div>
<!-- view empty slot modal end -->

<!-- view reservation modal start -->
<div class="modal fade" aria-modal="true" role="dialog" id="viewReservationModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">View Reservation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="d-flex justify-content-between align-items-center">
          <div class="badge bg-success p-2">start: <span id="view_viewstart_at"></span></div>
          <div class="badge bg-danger p-2">end: <span id="view_viewend_at"></span></div>
        </div>
        <div class="mb-3 mt-3">
          <div class="text-left">
            <h6>Name: <span id="view_reservation_name"></span></h6>

            <div class="text-center text-center">
              <h6 class="mb-3">Notes</h6>
              <p id="view_reservation_notes"></p>
            </div>

            <div class="container mt-3">
              <caption class="caption bg-primary">Reservation Owner Data</caption>
              <table class="table table-dark table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td id="view_reservation_uname">Mary</td>
                    <td id="view_reservation_uusername">Moe</td>
                    <td id="view_reservation_urole">mary@example.com</td>
                    <td id="view_reservation_email">mary@example.com</td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Back</button>
      </div>
    </div>
  </div>
</div>
<!-- view reservation modal end -->


<!-- edit reservation modal start -->
<div class="modal fade" aria-modal="true" role="dialog" id="editReservationModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="./index.php" method="POST">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Reservation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="d-flex justify-content-between align-items-center">
          <div class="badge bg-success p-2">start: <span id="edit_viewstart_at"></span></div>
          <div class="badge bg-danger p-2">end: <span id="edit_viewend_at"></span></div>
        </div>
        <div class="mb-3 mt-3 text-center">
          <label for="edit_reservation_name">Name: </label>
          <input  maxlength="30"  pattern="[A-Za-z\s]{1,30}" title="Please Enter a valid name: eg Jone" type="text" class="form-control" placeholder="Name.." name="edit_reservation_name" id="edit_reservation_name" />
          <label for="edit_reservation_comment">Description: </label>
          <textarea min="0" maxlength="255" placeholder="Reservation Notes.." class="form-control" rows="3" name="edit_reservation_comment" id="edit_reservation_comment"></textarea>
        </div>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
          <input type="hidden" style="display:none !important;" id="edit_reservation_id" name="edit_reservation_id" required />
          <button  type="submit" class="btn btn-danger" >Edit Reservation</button>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Back</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- edit reservation modal end -->


<!-- Cancel reservation modal start -->
<div class="modal fade" aria-modal="true" role="dialog" id="cancelReservationModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">cancellazione della prenotazione</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <?php
         if (isset($index_controller) && !empty($index_controller) && $user_role === 'admin'){
           ?>
           <form action="./index.php" method="POST">
             <label for="cancel_reservation_slotid">Sei sicuro di voler cancellare la prenotazione</label>
             <input type="hidden" style="display:none !important;" id="cancel_reservation_slotid" name="cancel_reservation_slotid" required />
             <input type="hidden" style="display:none !important;" id="cancel_reservation_id" name="cancel_reservation_id" required />
             <button  type="submit" class="btn btn-danger" >Cancel Booking</button>
           </form>
           <?php
         }
        ?>
        <form action="./index.php" method="POST">
          <label for="cp_reservation_slotid">Cancel/Pause Reservation</label>
          <input type="hidden" style="display:none !important;" id="cp_reservation_slotid" name="cp_reservation_slotid" required />
          <input type="hidden" style="display:none !important;" id="cp_reservation_id" name="cp_reservation_id" required />

          <select class="form-control" id="cp_reservation_status" name="cp_reservation_status">
            <option value="pause">Pause</option>
            <option value="cancel_forever">Cancel Forver</option>
          </select>
          <button  type="submit" class="btn btn-danger" >Update Status</button>
        </form>

      </div>
      <!-- Modal footer -->
      <div class="modal-footer">

        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Back</button>
      </div>

    </div>
  </div>
</div>
<!-- Cancel reservation modal end -->


<!-- active_aside NEW -->
<div class="active_aside bg-light" id="aside_style_controller">

   <div class="container-fluid editor_part">
   <h4 class="p-3 mt-2 mb-4 text-center border border-secondary rounded bg-dark text-white style_editor_title " data-bs-original-title="Close The Style Editor">
      <i class="fa fa-magic magic_icon toggle_asside"></i> <span>Style Editor</span>
   </h4>
   <div id="editor_error_cont"></div>

   <div class="container">
     <div class="d-flex justify-content-between align-items-center mt-2 mb-2" style="display:none;" id="edit_mode_container">
       <button class="btn btn-success mt-2 btn-small mr-2" id="enable_edit_mode">Start Edit Containers</button>  <span id="enable_edit_modemsg">(OFF)</span>
     </div>
     <div class="d-flex justify-content-between align-items-center mt-2 mb-2" style="display:none;" id="edit_mode_elm">
       <button class="btn btn-success mt-2 btn-small mr-2" id="enable_edit_mode_elm">Start Edit Elements</button>  <span id="enable_edit_modemsg_elm">(OFF)</span>
     </div>
     <div class="d-flex justify-content-center align-items-center flex-column bg-secondary p-2">
       <img id="editor_wait_gif" src="assets/images/load_circle_ux.gif" width="150" height="150" style="display:none;" />
     </div>

     <div id="setup_elm_conts">
       <div class="d-flex justify-content-center align-items-center flex-column bg-secondary p-2">
         <span class="text-white">Undefined Containers: <span id="total_undefined_containers" class="badge p-2"></span></span>
         <button class="btn btn-primary mt-2" style="display:none;" id="setup_loaded_containers">Setup These Containers</button>
       </div>

       <div class="d-flex justify-content-center align-items-center flex-column bg-secondary p-2">
         <span class="text-white">Undefined Elements: <span id="total_undefined_elements_txt" class="badge p-2"></span></span>
         <button class="btn btn-primary mt-2" style="display:none;" id="setup_element_btn">Setup These Elements</button>
       </div>
     </div>

     <div class="d-flex justify-content-center align-items-center p-2 m-2 flex-column">
       <div class="style_viewercss  border border-secondary p-4 bg-secondary text-black flex-fill" id="style_viewer">
       </div>
     </div>
   </div>

   <input id="calid_editor_style" type="hidden" value="<?php echo $current_calendar->get_id(); ?>" style="display:none;">
   <div class="d-flex flex-column">
   <div class="d-flex flex-column no-wrap justify-content-center align-items-center p-2 bg-light">
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill row bg-light">
            <label for="sys_elm_bg" class="col-sm-7">Background: </label>
            <input type="color" class="col-sm-4 p-2 border border-outline-primary" id="sys_elm_bg" name="sys_elm_bg" />
         </div>
      </div>

      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill row">
            <label for="sys_elm_color" class="col-sm-7">Color: </label>
            <input type="color" class="col-sm-4 p-2 border border-outline-primary" id="sys_elm_color" name="sys_elm_color" />
         </div>
      </div>


      <!-- Container Styles Start -->
      <div id="bs_containers_editor">

      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_bg" class="badge bg-light text-black">Background Color: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_bg" name="container_bg">
                <option value=""></option>
                <option value="bg-primary">Blue</option>
                <option value="bg-success">Green</option>
                <option value="bg-info">Lightblue</option>
                <option value="bg-warning">Yellow</option>
                <option value="bg-danger">Red</option>
                <option value="bg-secondary">gray</option>
                <option value="bg-dark">black</option>
                <option value="bg-light">light gray</option>
              </select>
            </div>
         </div>
      </div>


      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_text_color" class="badge bg-light text-black">Text Color: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_text_color" name="container_text_color">
                <option value=""></option>
                <option value="text-muted">muted</option>
                <option value="text-primary">blue</option>
                <option value="text-success">green</option>
                <option value="text-info">lightblue</option>
                <option value="text-warning">yellow</option>
                <option value="text-danger">red</option>
                <option value="text-secondary">Gray</option>
                <option value="text-white">White</option>
                <option value="text-dark">black</option>
                <option value="text-light">Lightgray</option>
                <option value="text-body">parent color</option>
              </select>
            </div>
         </div>
      </div>


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_p" class="badge bg-light text-black">Padding: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_p" name="container_p">
                <option value=""></option>
                <option value="p-1">p-1</option>
                <option value="p-2">p-2</option>
                <option value="p-3">p-3</option>
                <option value="p-4">p-4</option>
                <option value="p-5">p-5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_m" class="badge bg-light text-black">Margin: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_m" name="container_m">
                <option value=""></option>
                <option value="m-1">m-1</option>
                <option value="m-2">m-2</option>
                <option value="m-3">m-3</option>
                <option value="m-4">m-4</option>
                <option value="m-5">m-5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_border" class="badge bg-light text-black">Border: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_border" name="container_border">
                <option value=""></option>
                <option value="border">border</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_border_size" class="badge bg-light text-black">Border Size: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_border_size" name="container_border_size">
                <option value=""></option>
                <option value="border-1">border-1</option>
                <option value="border-2">border-2</option>
                <option value="border-3">border-3</option>
                <option value="border-4">border-4</option>
                <option value="border-5">border-5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

       <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_border_color" class="badge bg-light text-black">Border Color: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_border_color" name="container_border_color">
                <option value=""></option>
                <option value="border-primary">Blue</option>
                <option value="border-secondary">Gray</option>
                <option value="border-success">Green</option>
                <option value="border-danger">Red</option>
                <option value="border-warning">Yellow</option>
                <option value="border-info">Lightblue</option>
                <option value="border-light">Lightgray</option>
                <option value="border-dark">Black</option>
                <option value="border-white">White</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


       <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_round" class="badge bg-light text-black">Border Round: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_round" name="container_round">
                <option value=""></option>
                <option value="rounded">rounded</option>
                <option value="rounded-top">rounded-top</option>
                <option value="rounded-end">rounded-end</option>
                <option value="rounded-bottom">rounded-bottom</option>
                <option value="rounded-start">rounded-start</option>
                <option value="rounded-circle">rounded-circle</option>
                <option value="rounded-pill">rounded-pill</option>
                <option value="rounded-0">rounded-0</option>
                <option value="rounded-1">rounded-1</option>
                <option value="rounded-2">rounded-2</option>
                <option value="rounded-3">rounded-3</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_width" class="badge bg-light text-black">Width: </label>
            </div>

            <div class="" style="width:max-content;">
              <select class="form-control" id="container_width" name="container_width">
                <option value=""></option>
                <option value="w-25">25%</option>
                <option value="w-50">50%</option>
                <option value="w-75">75%</option>
                <option value="w-100">100%</option>
                <option value="w-auto">auto</option>
                <option value="mw-100">Max</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_height" class="badge bg-light text-black">Height: </label>
            </div>

            <div class="" style="width:max-content;">
              <select class="form-control" id="container_height" name="container_height">
                <option value=""></option>
                <option value="h-25">25%</option>
                <option value="h-50">50%</option>
                <option value="h-75">75%</option>
                <option value="h-100">100%</option>
                <option value="h-auto">auto</option>
                <option value="mh-100">Max</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_m_t" class="badge bg-light text-black">Margin Top: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_m_t" name="container_m_t">
                <option value=""></option>
                <option value="mt-1">1</option>
                <option value="mt-2">2</option>
                <option value="mt-3">3</option>
                <option value="mt-4">4</option>
                <option value="mt-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_m_b" class="badge bg-light text-black">Margin Bottom: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_m_b" name="container_m_b">
                <option value=""></option>
                <option value="mb-1">1</option>
                <option value="mb-2">2</option>
                <option value="mb-3">3</option>
                <option value="mb-4">4</option>
                <option value="mb-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_m_r" class="badge bg-light text-black">Margin Right: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_m_r" name="container_m_r">
                <option value=""></option>
                <option value="mr-1">1</option>
                <option value="mr-2">2</option>
                <option value="mr-3">3</option>
                <option value="mr-4">4</option>
                <option value="mr-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_m_l" class="badge bg-light text-black">Margin Left: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_m_l" name="container_m_l">
                <option value=""></option>
                <option value="ml-1">1</option>
                <option value="ml-2">2</option>
                <option value="ml-3">3</option>
                <option value="ml-4">4</option>
                <option value="ml-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_p_t" class="badge bg-light text-black">Padding Top: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_p_t" name="container_p_t">
                <option value=""></option>
                <option value="pt-1">1</option>
                <option value="pt-2">2</option>
                <option value="pt-3">3</option>
                <option value="pt-4">4</option>
                <option value="pt-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_p_b" class="badge bg-light text-black">Padding Bottom: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_p_b" name="container_p_b">
                <option value=""></option>
                <option value="pb-1">1</option>
                <option value="pb-2">2</option>
                <option value="pb-3">3</option>
                <option value="pb-4">4</option>
                <option value="pb-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_p_r" class="badge bg-light text-black">Padding Right: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_p_r" name="container_p_r">
                <option value=""></option>
                <option value="pr-1">1</option>
                <option value="pr-2">2</option>
                <option value="pr-3">3</option>
                <option value="pr-4">4</option>
                <option value="pr-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_p_l" class="badge bg-light text-black">Padding Left: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_p_l" name="container_p_l">
                <option value=""></option>
                <option value="pl-1">1</option>
                <option value="pl-2">2</option>
                <option value="pl-3">3</option>
                <option value="pl-4">4</option>
                <option value="pl-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_visibility" class="badge bg-light text-black">Visibility</label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_visibility" name="container_visibility">
                <option value=""></option>
                <option value="visible">visible</option>
                <option value="invisible">invisible</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_box_shadow" class="badge bg-light text-black">Shadow: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_box_shadow" name="container_box_shadow">
                <option value=""></option>
                <option value="shadow-none">None</option>
                <option value="shadow-sm">Small</option>
                <option value="shadow">Normal</option>
                <option value="shadow-lg">Large</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_justify_content" class="badge bg-light text-black">Justify Content: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_justify_content" name="container_justify_content">
                <option value=""></option>
                <option value="justify-content-start">Start</option>
                <option value="justify-content-end">End</option>
                <option value="justify-content-center">Center</option>
                <option value="justify-content-between">Space Between</option>
                <option value="justify-content-around">Space Around</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

       <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_align_items" class="badge bg-light text-black">Align Items: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_align_items" name="container_align_items">
                <option value=""></option>
                <option value="align-items-start">Start</option>
                <option value="align-items-end">End</option>
                <option value="align-items-center">Center</option>
                <option value="align-items-around">Space Around</option>
                <option value="align-items-stretch">Stretch</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
     <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
        <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
           <div class="flex-fill">
             <label for="container_align_content" class="badge bg-light text-black">Align Content: </label>
           </div>
           <div class="" style="width:max-content;">
             <select class="form-control" id="container_align_content" name="container_align_content">
               <option value=""></option>
               <option value="align-content-start">Start</option>
               <option value="align-content-end">End</option>
               <option value="align-content-center">Center</option>
               <option value="align-content-around">Space Around</option>
               <option value="align-content-stretch">Stretch</option>
             </select>
           </div>
        </div>
     </div>
     <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_ratio" class="badge bg-light text-black" title="diffrent heights for containers">Size Ratio: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_ratio" name="container_ratio">
                <option value=""></option>
                <option value="ratio ratio-1x1">1x1</option>
                <option value="ratio ratio-4x3">4x3</option>
                <option value="ratio ratio-16x9">16x9</option>
                <option value="ratio ratio-21x9">21x9</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_flex_type" class="badge bg-light text-black">Flex Type:</label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_flex_type" name="container_flex_type">
                <option value=""></option>
                <option value="d-flex">Flex</option>
                <option value="d-inline-flex">Inline Flex</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input Flex -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_flex_flow" class="badge bg-light text-black" title="(layout type)">Flex flow</label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_flex_flow" name="container_flex_flow">
                <option value=""></option>
                <option value="flex-row">Row</option>
                <option value="flex-row-reverse">Row Reverse</option>
                <option value="flex-column">Column</option>
                <option value="flex-column">Column Reverse</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

       <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="container_flex_wrap" class="badge bg-light text-black">Flex Wrap: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="container_flex_wrap" name="container_flex_wrap">
                <option value=""></option>
                <option value="flex-wrap">Wrap</option>
                <option value="flex-wrap-reverse">Wrap reverse</option>
                <option value="flex-nowrap">No wrap</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->
    </div>
    <!-- Container Styles End -->

    <!-- element start -->
    <div id="bs_element_editor">
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_bg" class="badge bg-light text-black">Background Color: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_bg" name="element_bg">
                <option value=""></option>
                <option value="bg-primary">Blue</option>
                <option value="bg-success">Green</option>
                <option value="bg-info">Lightblue</option>
                <option value="bg-warning">Yellow</option>
                <option value="bg-danger">Red</option>
                <option value="bg-secondary">gray</option>
                <option value="bg-dark">black</option>
                <option value="bg-light">light gray</option>
              </select>
            </div>
         </div>
      </div>


      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_text_color" class="badge bg-light text-black">Text Color: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_text_color" name="element_text_color">
                <option value=""></option>
                <option value="text-muted">muted</option>
                <option value="text-primary">blue</option>
                <option value="text-success">green</option>
                <option value="text-info">lightblue</option>
                <option value="text-warning">yellow</option>
                <option value="text-danger">red</option>
                <option value="text-secondary">Gray</option>
                <option value="text-white">White</option>
                <option value="text-dark">black</option>
                <option value="text-light">Lightgray</option>
                <option value="text-body">parent color</option>
              </select>
            </div>
         </div>
      </div>


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_p" class="badge bg-light text-black">Padding: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_p" name="element_p">
                <option value=""></option>
                <option value="p-1">p-1</option>
                <option value="p-2">p-2</option>
                <option value="p-3">p-3</option>
                <option value="p-4">p-4</option>
                <option value="p-5">p-5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_m" class="badge bg-light text-black">Margin: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_m" name="element_m">
                <option value=""></option>
                <option value="m-1">m-1</option>
                <option value="m-2">m-2</option>
                <option value="m-3">m-3</option>
                <option value="m-4">m-4</option>
                <option value="m-5">m-5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_border" class="badge bg-light text-black">Border: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_border" name="element_border">
                <option value=""></option>
                <option value="border">border</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_border_size" class="badge bg-light text-black">Border Size: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_border_size" name="element_border_size">
                <option value=""></option>
                <option value="border-1">border-1</option>
                <option value="border-2">border-2</option>
                <option value="border-3">border-3</option>
                <option value="border-4">border-4</option>
                <option value="border-5">border-5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

       <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_border_color" class="badge bg-light text-black">Border Color: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_border_color" name="element_border_color">
                <option value=""></option>
                <option value="border-primary">Blue</option>
                <option value="border-secondary">Gray</option>
                <option value="border-success">Green</option>
                <option value="border-danger">Red</option>
                <option value="border-warning">Yellow</option>
                <option value="border-info">Lightblue</option>
                <option value="border-light">Lightgray</option>
                <option value="border-dark">Black</option>
                <option value="border-white">White</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


       <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_round" class="badge bg-light text-black">Border Round: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_round" name="element_round">
                <option value=""></option>
                <option value="rounded">rounded</option>
                <option value="rounded-top">rounded-top</option>
                <option value="rounded-end">rounded-end</option>
                <option value="rounded-bottom">rounded-bottom</option>
                <option value="rounded-start">rounded-start</option>
                <option value="rounded-circle">rounded-circle</option>
                <option value="rounded-pill">rounded-pill</option>
                <option value="rounded-0">rounded-0</option>
                <option value="rounded-1">rounded-1</option>
                <option value="rounded-2">rounded-2</option>
                <option value="rounded-3">rounded-3</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_width" class="badge bg-light text-black">Width: </label>
            </div>

            <div class="" style="width:max-content;">
              <select class="form-control" id="element_width" name="element_width">
                <option value=""></option>
                <option value="w-25">25%</option>
                <option value="w-50">50%</option>
                <option value="w-75">75%</option>
                <option value="w-100">100%</option>
                <option value="w-auto">auto</option>
                <option value="mw-100">Max</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_height" class="badge bg-light text-black">Height: </label>
            </div>

            <div class="" style="width:max-content;">
              <select class="form-control" id="element_height" name="element_height">
                <option value=""></option>
                <option value="h-25">25%</option>
                <option value="h-50">50%</option>
                <option value="h-75">75%</option>
                <option value="h-100">100%</option>
                <option value="h-auto">auto</option>
                <option value="mh-100">Max</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_m_t" class="badge bg-light text-black">Margin Top: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_m_t" name="element_m_t">
                <option value=""></option>
                <option value="mt-1">1</option>
                <option value="mt-2">2</option>
                <option value="mt-3">3</option>
                <option value="mt-4">4</option>
                <option value="mt-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_m_b" class="badge bg-light text-black">Margin Bottom: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_m_b" name="element_m_b">
                <option value=""></option>
                <option value="mb-1">1</option>
                <option value="mb-2">2</option>
                <option value="mb-3">3</option>
                <option value="mb-4">4</option>
                <option value="mb-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_m_r" class="badge bg-light text-black">Margin Right: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_m_r" name="element_m_r">
                <option value=""></option>
                <option value="mr-1">1</option>
                <option value="mr-2">2</option>
                <option value="mr-3">3</option>
                <option value="mr-4">4</option>
                <option value="mr-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_m_l" class="badge bg-light text-black">Margin Left: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_m_l" name="element_m_l">
                <option value=""></option>
                <option value="ml-1">1</option>
                <option value="ml-2">2</option>
                <option value="ml-3">3</option>
                <option value="ml-4">4</option>
                <option value="ml-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_p_t" class="badge bg-light text-black">Padding Top: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_p_t" name="element_p_t">
                <option value=""></option>
                <option value="pt-1">1</option>
                <option value="pt-2">2</option>
                <option value="pt-3">3</option>
                <option value="pt-4">4</option>
                <option value="pt-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_p_b" class="badge bg-light text-black">Padding Bottom: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_p_b" name="element_p_b">
                <option value=""></option>
                <option value="pb-1">1</option>
                <option value="pb-2">2</option>
                <option value="pb-3">3</option>
                <option value="pb-4">4</option>
                <option value="pb-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_p_r" class="badge bg-light text-black">Padding Right: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_p_r" name="element_p_r">
                <option value=""></option>
                <option value="pr-1">1</option>
                <option value="pr-2">2</option>
                <option value="pr-3">3</option>
                <option value="pr-4">4</option>
                <option value="pr-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_p_l" class="badge bg-light text-black">Padding Left: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_p_l" name="element_p_l">
                <option value=""></option>
                <option value="pl-1">1</option>
                <option value="pl-2">2</option>
                <option value="pl-3">3</option>
                <option value="pl-4">4</option>
                <option value="pl-4">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_visibility" class="badge bg-light text-black">Visibility</label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_visibility" name="element_visibility">
                <option value=""></option>
                <option value="visible">visible</option>
                <option value="invisible">invisible</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_box_shadow" class="badge bg-light text-black">Shadow: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_box_shadow" name="element_box_shadow">
                <option value=""></option>
                <option value="shadow-none">None</option>
                <option value="shadow-sm">Small</option>
                <option value="shadow">Normal</option>
                <option value="shadow-lg">Large</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->





      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_text_align" class="badge bg-light text-black">Text Align: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_text_align" name="element_text_align">
                <option value=""></option>
                <option value="text-start">Start</option>
                <option value="text-center">Center</option>
                <option value="text-end">End</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_text_break" class="badge bg-light text-black">Text Break: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_text_break" name="element_text_break">
                <option value=""></option>
                <option value="text-break">text break</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_text_case" class="badge bg-light text-black">Text Case: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_text_case" name="element_text_case">
                <option value=""></option>
                <option value="text-lowercase">LowerCase</option>
                <option value="text-uppercase">upperCase</option>
                <option value="text-capitalize">Capitalize</option>
                <option value="initialism">Initialism</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_text_wrap" class="badge bg-light text-black">Text Wrap: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_text_wrap" name="element_text_wrap">
                <option value=""></option>
                <option value="text-wrap">Wrap</option>
                <option value="text-nowrap">No Wrap</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_font_wight" class="badge bg-light text-black">Font Wight: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_font_wight" name="element_font_wight">
                <option value=""></option>
                <option value="font-weight-bold">Bold</option>
                <option value="font-weight-bolder">Bolder</option>
                <option value="font-weight-normal">Normal</option>
                <option value="font-weight-light">Light</option>
                <option value="font-weight-lighter">Lighter</option>
                <option value="font-italic">Italic</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_display" class="badge bg-light text-black">Display: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_display" name="element_display">
                <option value=""></option>
                <option value="display-1">1</option>
                <option value="display-2">2</option>
                <option value="display-3">3</option>
                <option value="display-4">4</option>
                <option value="display-5">5</option>
                <option value="display-6">6</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_heading" class="badge bg-light text-black">Heading: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_heading" name="element_heading">
                <option value=""></option>
                <option value="h1">1</option>
                <option value="h2">2</option>
                <option value="h3">3</option>
                <option value="h4">4</option>
                <option value="h5">5</option>
                <option value="h6">6</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_flex_order" class="badge bg-light text-black">Flex Order: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_flex_order" name="element_flex_order">
                <option value=""></option>
                <option value="order-1">1</option>
                <option value="order-2">2</option>
                <option value="order-3">3</option>
                <option value="order-4">4</option>
                <option value="order-5">5</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_flex_fill" class="badge bg-light text-black">Flex fill: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_flex_fill" name="element_flex_fill">
                <option value=""></option>
                <option value="flex-fill">Fill</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_flex_grow" class="badge bg-light text-black">Flex Grow: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_flex_grow" name="element_flex_grow">
                <option value=""></option>
                <option value="flex-grow-1">Grow 1</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_ms_auto" class="badge bg-light text-black">MS auto: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_ms_auto" name="element_ms_auto">
                <option value=""></option>
                <option value="ms-auto">Grow 1</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->


      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_mx_auto" class="badge bg-light text-black">Center Content</label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_mx_auto" name="element_mx_auto">
                <option value=""></option>
                <option value="mx-auto">Center mx-auto</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_badge" class="badge bg-light text-black">Badge: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_badge" name="element_badge">
                <option value=""></option>
                <option value="badge">Badge</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_vertical_align" class="badge bg-light text-black">Vertical Align: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_vertical_align" name="element_vertical_align">
                <option value=""></option>
                <option value="align-baseline">baseline</option>
                <option value="align-top">top</option>
                <option value="align-bottom">bottom</option>
                <option value="align-text-top">text top</option>
                <option value="align-text-bottom">text bottom</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_box_shadow" class="badge bg-light text-black">Float Position : </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_box_shadow" name="element_box_shadow">
                <option value=""></option>
                <option value="float-sm-end">sm-end</option>
                <option value="float-md-end">md-end</option>
                <option value="float-md-end">md-end</option>
                <option value="float-xl-end">xl-end</option>
                <option value="float-xxl-end">xxl-end</option>
                <option value="float-none">none</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

      <!-- Bootstrap input -->
      <div class="border border-light bg-light d-flex justify-content-center align-items-center the_width80 mb-2 p-2">
         <div class="flex-fill d-flex justify-content-center align-items-center flex-row flex-nowrap">
            <div class="flex-fill">
              <label for="element_colsm" class="badge bg-light text-black">Col SM: </label>
            </div>
            <div class="" style="width:max-content;">
              <select class="form-control" id="element_colsm" name="element_colsm">
                <option value=""></option>
                <option value="col-sm-1">1</option>
                <option value="col-sm-2">2</option>
                <option value="col-sm-3">3</option>
                <option value="col-sm-4">4</option>
                <option value="col-sm-5">5</option>
                <option value="col-sm-6">6</option>
                <option value="col-sm-7">7</option>
                <option value="col-sm-8">8</option>
                <option value="col-sm-9">9</option>
                <option value="col-sm-10">10</option>
                <option value="col-sm-11">11</option>
                <option value="col-sm-12">12</option>
              </select>
            </div>
         </div>
      </div>
      <!-- bootstrap -->

    </div>
    <!-- element end -->



 </div>

  </div>
  </div>
</div>
<!-- NEW -->


<!-- sound effects -->
<audio id="open_modal_sound">
  <source src="https://sndup.net/rp5j/d" type="audio/wav">
</audio>

<audio id="unable_open_modal">
  <source src="https://sndup.net/sbs9/d" type="audio/wav">
</audio>

<!-- HTML5 sounds -->
<script>

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




window.addEventListener('DOMContentLoaded', (event) => {
const playSound = (selector)=>{
  //open_modal_sound unable_open_modal
  const selectedSound = document.querySelector(`${selector}`);
  selectedSound.volume = 0.1;
  // important for on time sound like it play from begning and ignore previous
  // Show loading animation.
  selectedSound.currentTime = 0;
  var playPromise = selectedSound.play();

  if (playPromise !== undefined) {
      playPromise.then(_ => {
      // Automatic playback started!
      // Show playing UI.
      // We can now safely pause video...
      video.pause();
    })
    .catch(error => {
      // Auto-play was prevented
      // Show paused UI.
    });
  }
};





const toggleEditorWait = (editorWaitParm)=>{
  const editEnableBtn = document.querySelector('#edit_mode_container button');
  const editEnableSpan = document.querySelector('#edit_mode_container span');
  const editEnableBtnElm = document.querySelector('#edit_mode_elm button');
  const editEnableSpanElm = document.querySelector('#edit_mode_elm span');

  const setupElmConts = document.getElementById('setup_elm_conts');
  const editorWaitGif = document.getElementById('editor_wait_gif');
  if (editorWaitParm == true){
    editEnableBtn.style.display = "none";
    editEnableSpan.style.display = "none";
    editEnableBtnElm.style.display = "none";
    editEnableSpanElm.style.display = "none";
    setupElmConts.style.display = "none";
    editorWaitGif.style.display = "block";
  } else {
    editEnableBtn.style.display = "block";
    editEnableSpan.style.display = "block";
    editEnableBtnElm.style.display = "block";
    editEnableSpanElm.style.display = "block";
    setupElmConts.style.display = "block";
    editorWaitGif.style.display = "none";
  }
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
const loggedUId = document.getElementById('loggeduid');

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
      loggedUId.value = targetElement.getAttribute("data-uid");
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
  adminResOwner.style.display = "none";
  periodIndex = 0;
}
mapBookingModalOpen.addEventListener("click", backEveryThingMap);

function goTomapLevel2(){
  mapDayLevel3.style.display = "none";
  mapDayLevel2.style.display = "block";
  mapNewPeriodsCont.innerHTML = '';
}


const adminResOwner = document.querySelector("#admin_reservation_owner");
function goTomapLevel3(event){
  mapNewPeriodsCont.innerHTML = '';
  const slotId = event.target.value;
  const slotStartFrom = event.target.getAttribute('data-start');
  const slotEndAt = event.target.getAttribute('data-end');
  const periodTitle = event.target.getAttribute('data-period-title');
  adminResOwner.style.display = "block";
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

const displayAjaxEditorMsg = (msg, type='succss')=>{
  const mapErrorCont = document.querySelector("#editor_error_cont");
  mapErrorCont.innerHTML = `
  <div class="alert alert-${type} alert-dismissible fade show">
    <p>${msg}</p>
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
  backEveryThingMap();
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


// effects for owned
const reservationIdInp = document.querySelector("#cancel_reservation_id");
const reservationSlotIdInp = document.querySelector("#cancel_reservation_slotid");

const changeStatusReservId = document.querySelector("#cp_reservation_id");
const changeStatusReservSlotId = document.querySelector("#cp_reservation_slotid");
const changeStatusReservStatusSelect = document.querySelector("#cp_reservation_status");
const changeStatusOptios = Array.from(changeStatusReservStatusSelect.options);



function openCancelReservation(event){
  event.preventDefault();

  reservationSlotIdInp.value = event.target.getAttribute("data-slot-id");
  reservationIdInp.value = event.target.getAttribute("data-id");

  changeStatusReservSlotId.value = event.target.getAttribute("data-slot-id");
  changeStatusReservId.value = event.target.getAttribute("data-id");
}
function showOwnedEffect(event){
  if (event.target.classList.contains("fa fa-envelope-o")){
    event.target.classList.add("fa-envelope-open-o");
    event.target.classList.remove("fa fa-envelope-o");
    return true;
  }
}
tooltip = null;
let tooltips = [];
function clearthem(){
  tooltips.forEach( (tolTip)=>{
    tolTip.hide();
  });
}
function showOwnedEffectOpen(event, selector='i.owned_byLoged', title='View Your Reservation Data', placement='bottom'){
  event.preventDefault();
  if (event.target.classList.contains("fa-envelope-o")){
    event.target.classList.remove("fa-envelope-o");
    event.target.classList.add("fa-envelope-open-o");
  }
  event.target.setAttribute("data-bs-toggle", "tooltip");
  event.target.setAttribute("data-bs-placement", placement);
  event.target.setAttribute("title", title);

  var tooltipTriggerList = [].slice.call(document.querySelectorAll(`${selector}[data-bs-toggle="tooltip"]`));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    tooltip = new bootstrap.Tooltip(tooltipTriggerEl);
    tooltip.show();
    tooltips.push(tooltip);
    return tooltip;
  });
}

function showOwnedEffectClose(event, selector='i.owned_byLoged'){
  if (event.target.classList.contains("fa-envelope-open-o")){
    event.target.classList.add("fa-envelope-o");
    event.target.classList.remove("fa-envelope-open-o");
  }
  clearthem();
  const allTooltips = document.querySelectorAll(`${selector}[data-bs-toggle="tooltip"]`);
  allTooltips.forEach( (toolTip)=>{
    if (toolTip.hasAttribute("data-bs-toggle")){
      toolTip.removeAttribute("data-bs-toggle")
    }
    if (toolTip.hasAttribute("data-bs-placement")){
      toolTip.removeAttribute("data-bs-placement")
    }
    if (toolTip.hasAttribute("title")){
      toolTip.removeAttribute("title")
    }
  });
}

// effect for open owned reservation
const allOwnedRes = document.querySelectorAll(".owned_byLoged");

allOwnedRes.forEach( (resBtn)=>{
  resBtn.addEventListener("mouseenter", (event)=>{showOwnedEffectOpen(event, 'i.owned_byLoged', 'View Your Reservation Data', 'right')});
  resBtn.addEventListener("mouseout", (event)=>{showOwnedEffectClose(event, 'i.owned_byLoged')});
});

const emptyViewSlots = document.querySelectorAll(".view_empty_slot");
emptyViewSlots.forEach( (emptySlot)=>{
  emptySlot.addEventListener("mouseenter", (event)=>{showOwnedEffectOpen(event, 'i.view_empty_slot', 'View Empter Slot Data', 'right')});
  emptySlot.addEventListener("mouseout", (event)=>{showOwnedEffectClose(event, 'i.view_empty_slot')});
});

const togglesAsside = document.querySelectorAll(".toggle_asside");
togglesAsside.forEach( (toggleAsside)=>{
  toggleAsside.addEventListener("mouseenter", (event)=>{showOwnedEffectOpen(event, 'i.toggle_asside', 'Close The Style Editor', 'bottom')});
  toggleAsside.addEventListener("mouseout", (event)=>{showOwnedEffectClose(event, 'i.toggle_asside', 'Close The Style Editor')});
});


const removeOwnedReservations = document.querySelectorAll(".owned_byLoged_remove");
removeOwnedReservations.forEach( (removeResrv)=>{
  removeResrv.addEventListener("click", openCancelReservation);
});







const allEditReservations = document.querySelectorAll(".edit_owned_slot");

allEditReservations.forEach( (editResrv)=>{
  editResrv.addEventListener("click",editHandler);
});


const editReservIDInput = document.querySelector("#edit_reservation_id");
const editReservNameInput = document.querySelector("#edit_reservation_name");
const editReservCommentInput = document.querySelector("#edit_reservation_comment");
const editViewStartAt = document.querySelector("#edit_viewstart_at");
const editViewEndAt = document.querySelector("#edit_viewend_at");
function editHandler(event){
  event.preventDefault();
  editReservIDInput.value = event.target.getAttribute("data-id");
  editReservNameInput.value = event.target.getAttribute("data-name");
  editReservCommentInput.value = event.target.getAttribute("data-notes");
  editViewStartAt.innerText = event.target.getAttribute("data-start");
  editViewEndAt.innerText = event.target.getAttribute("data-end");
}

const allViewReservations = document.querySelectorAll(".view_reservation");

allViewReservations.forEach( (viewReserv)=>{
  viewReserv.addEventListener("click",viewHandler);
});

const viewReservName = document.querySelector("#view_reservation_name");
const viewViewComment = document.querySelector("#view_reservation_notes");
const viewReservStart = document.querySelector("#view_viewstart_at");
const viewReservEnd = document.querySelector("#view_viewend_at");

const viewReservationUname = document.querySelector("#view_reservation_uname");
const viewReservationUusername = document.querySelector("#view_reservation_uusername");
const viewReservationUrole = document.querySelector("#view_reservation_urole");
const viewReservationEmail = document.querySelector("#view_reservation_email");
function viewHandler(event){
  event.preventDefault();
  const btnId = event.target.getAttribute("data-show");
  const btn = document.getElementById(btnId);
  if (btn){
    viewReservName.innerText = event.target.getAttribute("data-name");
    viewViewComment.innerText = event.target.getAttribute("data-notes");
    viewReservStart.innerText = event.target.getAttribute("data-start");
    viewReservEnd.innerText = event.target.getAttribute("data-end");

    /* user */
    viewReservationUname.innerText = event.target.getAttribute("data-user-name");
    viewReservationUusername.innerText = event.target.getAttribute("data-username");
    viewReservationUrole.innerText = event.target.getAttribute("data-user-role");
    viewReservationEmail.innerText = event.target.getAttribute("data-user-email");
    btn.click();
  }
}

// month arrow toggle
const allMonthArrows = Array.from(document.querySelectorAll(".month_arrow"));
function handleMonthArrowJs(event){
  // handle arrow smoth with same buttons to keep the results easy and less query parameters
  const targetMonth = event.target.getAttribute("data-month");
  if (targetMonth){
    const targetForm = document.querySelector(`.month_form[data-month='${targetMonth}']`);
    if (targetForm){
      targetForm.submit();
    }
  }
}
allMonthArrows.forEach( (monthArrow)=>{
  monthArrow.addEventListener("click", handleMonthArrowJs);
});

// submit year form

const yearSelector = document.getElementById("year");
const yearForm = document.getElementById("year_select_form");
yearSelector.addEventListener("change", (event)=>{
  if (event.target.value){
    yearForm.submit();
    return false;
  }
})






const allViewEmptySlots = document.querySelectorAll(".view_empty_slot");

allViewEmptySlots.forEach( (viewEmptySlot)=>{
  viewEmptySlot.addEventListener("click",viewSlotHandler);
});

const viewSlotStartAt = document.querySelector("#view_slotstart_at");
const viewSlotendAt = document.querySelector("#view_slotend_at");
const slotIndexView = document.querySelector("#slot_indexview");
const periodDateslotView = document.querySelector("#period_dateslot_view");
const periodDescriptionViewslot = document.querySelector("#period_description_viewslot");
function viewSlotHandler(event){
  event.preventDefault();
  const slotBtnId = event.target.getAttribute("data-show-view");
  const slotBtn = document.getElementById(slotBtnId);
  if (slotBtn){
    viewSlotStartAt.innerText = event.target.getAttribute("data-start");
    viewSlotendAt.innerText = event.target.getAttribute("data-end");
    periodDateslotView.innerText = event.target.getAttribute("data-period-date");
    slotIndexView.innerText = event.target.getAttribute("data-slot-index");
    periodDescriptionViewslot.innerText = event.target.getAttribute("data-period-description");
    slotBtn.click();
  }
}








/* AJAX MAP new Reservation end */

/*  UX jquery for aside nav for style controller */
let body_max_width = 1;
/*  UX jquery for aside nav for style controller */
// fix some effect in resisze important Advanced UX small details
$(window).resize(function(){

  if ($(window).width() > 900 && document.body.style.width.trim() == '60%'){
    document.body.style.background = "lightgray";
    document.body.style.width = "78%";
  }

  if ($(window).width() < 900 && document.body.style.width.trim() == '78%'){
    document.body.style.width = "60%";

  }

});

function fit_body_for_aisde(){
  // add css class to body for nice ux
    document.body.style.overflow = "auto";
    if (toggAsside.classList.contains("active_toggle")){
      toggAsside.classList.remove("active_toggle");
      // sound effects
      playSound("#open_modal_sound");
    }

    //toggAsside.style.display = "";
    if (body_max_width == 1){
      body_max_width = 0;
      if ($(window).width() < 900){
        document.body.style.width = "60%";
      } else {
        document.body.style.width = "78%";
      }
      document.body.style.background = "url('assets/images/load_circle_ux.gif')";
      document.body.style.backgroundRepeat = "no-repeat";
      document.body.style.backgroundPosition = "right";
      document.body.style.backgroundAttachment = "fixed";
   }
   else{
      body_max_width = 1;
      document.body.style.width = "100%";
      document.body.style.background = "lightgray";
      // this for make small detail effect show scrollbar after 75mili from all animation end give it ncie ux

   }

}

const toggAsside = document.querySelector("div.toggle_asside");



// advanced styles for control UX and aside and animation
$(document).ready(function(){
  $(".toggle_asside").each(function(i, c){
    const magicBtn = $(this);
    $(magicBtn).click(function(event){
      //toggAsside.style.display = "none";
      if (!toggAsside.classList.contains("active_toggle")){
        toggAsside.classList.add("active_toggle");
      } else {
        return false;
      }

      if (body_max_width == 1){
        document.body.style.width = "78%";
        document.body.style.background = "url('assets/images/load_circle_ux.gif')";
        document.body.style.backgroundRepeat = "no-repeat";
        document.body.style.backgroundPosition = "right";
        document.body.style.backgroundAttachment = "fixed";
        // note this will cast first then the callback so it will will have auto when end 35 smaller alot than effect
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "hidden";
      }
      $("#aside_style_controller").slideToggle(null, 'swing', fit_body_for_aisde);
    });
  })

});

/* UX jquery for aside nav for style controller My WP */

const styleViewer = document.querySelector("#style_viewer");
const enableEditModeMsg = document.querySelector("#enable_edit_modemsg");
const enableEditMode = document.querySelector("#enable_edit_mode");
const editModeContainer = document.querySelector("#edit_mode_container");
let editModeStatus = false;
function startEditMode(event){
  styleViewer.innerHTML = '';
  if (editModeStatus){
    enableEditModeMsg.innerText = "(OFF)";
    event.target.innerText = "Start Edit Containers";
    removeStyleViewerEvent();
    editModeStatus = false;
  } else {
    enableEditModeMsg.innerText = "(ON)";
    event.target.innerText = "Stop Edit Containers";
    addStyleViewerEvent();
    editModeStatus = true;
  }
}

// elements edit mode

const enableEditModeMsgElm = document.querySelector("#enable_edit_modemsg_elm");
const enableEditModeElm = document.querySelector("#enable_edit_mode_elm");
const editModeElm = document.querySelector("#edit_mode_elm");
let editModeElmStatus = false;
function startEditModeElm(event){
  styleViewer.innerHTML = '';
  if (editModeElmStatus){
    enableEditModeMsgElm.innerText = "(OFF)";
    event.target.innerText = "Start Edit Elements";
    removeStyleViewerEventElm();
    editModeElmStatus = false;
  } else {
    enableEditModeMsgElm.innerText = "(ON)";
    event.target.innerText = "Stop Edit Elements";
    addStyleViewerEventElm();
    editModeElmStatus = true;
  }
}

enable_edit_mode_elm.addEventListener("click", startEditModeElm);



/* Style Editor start */
const allElements = document.querySelectorAll("[data-editor-type='element']");
const allContainers = document.querySelectorAll("[data-editor-type='container']");
const setupLoadedContainers = document.querySelector("#setup_loaded_containers");
const setupLoadedElements = document.querySelector("#setup_element_btn");


const calidStyle = document.querySelector("#calid_editor_style");

// send container data ajax
async function sendContainerData(){
  const containersData = [];
  const notSavedConts = document.querySelectorAll(".not_saved[data-editor-type='container']");
  notSavedConts.forEach((cont)=>{
      let dataGroup = '';
      if (cont.hasAttribute("data-editor-group")){
        dataGroup = cont.getAttribute("data-editor-group");
      }
      containerObj = {
        element_id: cont.getAttribute("id"),
        html_class: cont.getAttribute("data-editor-class"),
        data_group: dataGroup,
        c_cal_id: calidStyle.value
      };
      containersData.push(containerObj);
    });
  toggleEditorWait(true);
  const serverResponse = await postData('',{setup_containers: containersData});
  toggleEditorWait(false);
  if (serverResponse.hasOwnProperty('code') && serverResponse.hasOwnProperty('total') && serverResponse.code == 200){
    const messageR = serverResponse.message  + ' Total: ' + serverResponse.total + ' Will restart after 5 seconds to load the new style';
    displayAjaxEditorMsg(messageR, 'success');
    setTimeout(()=>{
      location.reload();
    }, 7000);
    return true;
  } else {
    displayAjaxEditorMsg(serverResponse.message, 'danger');
    return false;
  }
}

setupLoadedContainers.addEventListener("click", sendContainerData);
// elements data ajax
// send container data ajax
let element_counter = 7;

async function sendElementData(){
  const elementObjects = [];
  const notSavedConts = document.querySelectorAll(".not_saved_element[data-editor-type='element']");
  notSavedConts.forEach((cont)=>{
      let dataGroup = '';
      if (cont.hasAttribute("data-editor-group")){
        dataGroup = cont.getAttribute("data-editor-group");
      }
      elemObject = {
        element_id: cont.getAttribute("id"),
        html_class: cont.getAttribute("data-editor-class"),
        data_group: dataGroup,
        c_cal_id: calidStyle.value
      };
      elementObjects.push(elemObject);
    });
  toggleEditorWait(true);
  const serverResponse = await postData('',{setup_elements: elementObjects});
  toggleEditorWait(false);
  if (!serverResponse){
    displayAjaxEditorMsg('Unkown Error', 'danger');
    return false;
  }
  if (serverResponse.hasOwnProperty('code') && serverResponse.hasOwnProperty('total') && serverResponse.code == 200){
    element_counter = 7;
    let servMessage = serverResponse.message  + ' Total: ' + serverResponse.total  + ' Will restart after 5 seconds to load the new style';
    displayAjaxEditorMsg(servMessage, 'success');
    setTimeout(()=>{
      location.reload();
    }, 7000);
    return true;
  } else {
    displayAjaxEditorMsg(serverResponse.message, 'danger');
    return false;
  }
}

setupLoadedElements.addEventListener("click", sendElementData);

enableEditMode.addEventListener("click", startEditMode);

/* view element in viewer */
let elementInView = null;
function loadContainerInView(event){
  const elementInView = event.target;
  styleViewer.innerHTML = elementInView.outerHTML;
}
const allLoadedContainers = document.querySelectorAll("div[data-editor-type='container']:not(.not_saved)");

// this for good preformance as it only make this heavy event when edit on and remove it total not only return it false when edit of
function addStyleViewerEvent(){
  allLoadedContainers.forEach( (loadedCont)=>{
    loadedCont.addEventListener("mouseenter", loadContainerInView);
  });
}

function removeStyleViewerEvent(){
  allLoadedContainers.forEach( (loadedCont)=>{
    loadedCont.removeEventListener("mouseenter", loadContainerInView);
  });
}


const allLoadedElements = document.querySelectorAll("[data-editor-type='element']:not(.not_saved_element)");

function addStyleViewerEventElm(){
  allLoadedElements.forEach( (loadedElm)=>{
    loadedElm.addEventListener("mouseenter", loadContainerInView);
  });
}

function removeStyleViewerEventElm(){
  allLoadedElements.forEach( (loadedElm)=>{
    loadedElm.removeEventListener("mouseenter", loadContainerInView);
  });
}





/* view element in viewer end */

/* style editor end */
// get message from php bs function to know the undefined elements
function loadUndefinedContainerScore(setupBtnselector, targetTxtid, targetSelector, type){
  const setupBtn = document.querySelector(setupBtnselector);
  const undefinedContTxt = document.querySelector(targetTxtid);
  const totalUndefined = document.querySelectorAll(targetSelector).length;
  let appStatusColor = 'bg-primary';
  if (totalUndefined == 0){ appStatusColor = 'bg-success'; }
  if (totalUndefined > 0 && totalUndefined < 100 ){ appStatusColor = 'bg-primary'; }
  if (totalUndefined > 100 && totalUndefined < 700 ){ appStatusColor = 'bg-warning'; }
  if (totalUndefined > 700 ){ appStatusColor = 'bg-danger'; }
  undefinedContTxt.innerText = totalUndefined;
  if (!undefinedContTxt.classList.contains('bg-primary')){
    undefinedContTxt.classList.remove('bg-primary');
  }
  if (!undefinedContTxt.classList.contains('bg-success')){
    undefinedContTxt.classList.remove('bg-success');
  }
  if (!undefinedContTxt.classList.contains('bg-primary')){
    undefinedContTxt.classList.remove('bg-primary');
  }
  if (!undefinedContTxt.classList.contains('bg-warning')){
    undefinedContTxt.classList.remove('bg-warning');
  }
  if (!undefinedContTxt.classList.contains('bg-danger')){
    undefinedContTxt.classList.remove('bg-danger');
  }
  undefinedContTxt.classList.add(appStatusColor);

  if (totalUndefined > 0){
    setupBtn.style.display = "block";
  }

  if (totalUndefined == 1){
    const messageTxt = type == 'container' ? 'Setup This Container' : 'Setup This Element';
    setupBtn.innerText = messageTxt;
  } else {
    const messageTxt = type == 'container' ? 'Setup These Containers' : 'Setup These Elements';
    setupBtn.innerText = messageTxt;
  }

  if (totalUndefined == 0){
    editModeContainer.style.display = "block";
  }
}

loadUndefinedContainerScore("#setup_loaded_containers", "#total_undefined_containers", ".not_saved[data-editor-type='container']");
loadUndefinedContainerScore("#setup_element_btn", "#total_undefined_elements_txt", ".not_saved_element[data-editor-type='element']");

});

    </script>
    <!-- this question in udacity 2 years ago why add the style in the end to override the default style in top if any -->
    <?php
      // like wordpress get app custom styles it has alot of ways edit for example u have id and class and have style for each element u can change each period or slot
      // for fast and get what u use I only load the styles of current calendar and current month and current year so it small but everything here
      if (isset($styles_string) && !empty($styles_string) ){
        ?>
          <style><?php echo $styles_string; ?></style>
        <?php
      }
    ?>
  </body>
</html>
