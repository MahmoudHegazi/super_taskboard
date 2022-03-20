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
  echo '
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <title>No Calendar Found</title>
    </head>
    <body>
    <div class="alert alert-danger">
      <p class="text-center"><strong>Warning!</strong> '.
      $used_calendar_emessage
      .
      '
        <!-- check if admin and display link to setup -->
        <div class="d-flex justify-content-center align-items-center">
          <a href="setup.php" class="btn btn-outline-primary">Go To Setup</a>
          <a href="index.php" class="btn btn-outline-primary">Go To Home</a>
          <a href="reports.php" class="btn btn-outline-primary">Go To Reports</a>
          <a href="signup.php" class="btn btn-outline-primary">Go To Signup</a>
          <a href="login.php" class="btn btn-outline-primary">Go To Login</a>
        </div>
      </p>
   </div>
   </body>
   </html>
   ';
   die();
}

// load containers bs and check if loaded or not
function setupBSElementsData($inC, $element_id, $type='container', $default_classes='', $main=false){
  if ($main){
    return $default_classes;
  }
  if ($type == 'container'){
    $classes = empty($default_classes) ? 'd-flex justify-content-center align-items-end not_saved' : $default_classes . ' not_saved';
    $title_container = $inC->getElement($element_id, $type);
    // this help js to connected with php esaily for save the new added elements so if u need add element and active make it like other elements
    $title_container_bs = !empty($title_container) ? $title_container->get_bootstrap_classes() : $classes;

    // main customiztion for limit d-flex
    $forbiden_mains = array('d-flex','d-inline-flex');
    for ($i=0; $i<count($forbiden_mains); $i++){
      str_replace($forbiden_mains[$i], '', $title_container_bs);
    }
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
    <meta charset="utf-8">
    <link rel="icon" href="<?php echo defined('THUMBNAIL') ? 'uploads/images/' . THUMBNAIL : 'uploads/images/default_logo.png'; ?>">

    <title><?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <!-- local data -->
    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
  </head>
  <body>


<div class="main_view bg-white">


    <div data-editor-type="container" data-editor-class="the_real_containercss" id="the_real_container_<?php echo Calid; ?>" class="<?php echo setupBSElementsData($index_controller, 'the_real_container_'.Calid, 'container', '', true); ?> the_real_containercss container-fluid p-2 text-white text-center cal_title bg_dimgray" <?php echo $index_controller->getBsId($index_controller, 'the_real_container_'.Calid, 'container'); ?>>


      <div data-editor-type="container" data-editor-class="title_container_cs"
      class="<?php echo setupBSElementsData($index_controller, 'title_container_new', 'container'); ?> display-6 mt-2 mb-3 text-white p-2 default_shadow text_shadow03 border border-secondary title_container_cs" id="title_container_new" <?php echo $index_controller->getBsId($index_controller, 'title_container_new', 'container'); ?>>

      <img data-editor-type="element" id="logo_image" data-editor-class="logo_image_css"  class="logo_image_css <?php echo setupBSElementsData($index_controller, 'logo_image', 'element'); ?>"
      src="<?php echo defined('THUMBNAIL') ? 'uploads/images/' . THUMBNAIL : 'uploads/images/default_logo.png'; ?>" alt="Calendar Logo" height="50" width="50" <?php echo $index_controller->getBsId($index_controller, 'logo_image', 'element'); ?>>
      <span data-editor-type="element" id="title_text" data-editor-class="title_text_css" class="title_text_css <?php echo setupBSElementsData($index_controller, 'title_text', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'title_text', 'element'); ?>>
        <?php echo defined('TITLE') ? TITLE : 'Super Calendar'; ?></span>
      </div>






        <div data-editor-type="container" data-editor-class="desc_class"
        class="<?php echo setupBSElementsData($index_controller, 'desc_id', 'container'); ?> desc_class description_p bg_azure  text-black border border-secondary p-2 default_shadow" id="desc_id" <?php echo $index_controller->getBsId($index_controller, 'desc_id', 'container'); ?>>

        <?php echo defined('DESCRIPTION') ? DESCRIPTION : 'Booking Calendar'; ?>
      </div>

      <div data-editor-type="container" id="top_nav" data-editor-class="top_nav_css" class="top_nav_css container border border-succcess p-2 <?php echo setupBSElementsData($index_controller, 'top_nav', 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, 'top_nav', 'container'); ?>>

        <?php
          if (isset($_SESSION['logged_id']) && !empty($_SESSION['logged_id'])){
            ?>
            <a href="./logout.php" class="<?php echo setupBSElementsData($index_controller, 'logout_page_link', 'element'); ?> logout_page_link_css btn bg-danger text-white" id="logout_page_link" data-editor-type="element" data-editor-class="logout_page_link_css" <?php echo $index_controller->getBsId($index_controller, 'logout_page_link', 'element'); ?>>Log Out</a>
            <span style="width:10px;"></span>
            <?php
          }
         ?>
        <!-- fake margin for flex -->
        <span style="width:10px;"></span>
        <?php if ($user_role == 'admin'){
          ?>
            <a href="./setup.php" class="<?php echo setupBSElementsData($index_controller, 'setup_page_link', 'element'); ?> setup_page_link_css btn bg-primary text-white" id="setup_page_link" data-editor-type="element" data-editor-class="setup_page_link_css" <?php echo $index_controller->getBsId($index_controller, 'setup_page_link', 'element'); ?>>Setup</a>
            <span style="width:10px;"></span>
            <a href="./reports.php" class="<?php echo setupBSElementsData($index_controller, 'report_page_link', 'element'); ?> report_page_link_css btn bg-primary text-white" id="report_page_link" data-editor-type="element" data-editor-class="report_page_link_css" <?php echo $index_controller->getBsId($index_controller, 'report_page_link', 'element'); ?>>Reports</a>
          <?php
        } ?>
      </div>

    </div>


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
            class="row container-fluid options_parent bg_dimgray p-2 month_switcher_grid_css <?php echo setupBSElementsData($index_controller, 'month_switcher_grid', 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, 'month_switcher_grid', 'container'); ?>>
              <!-- month controller start -->
              <div data-editor-type="container" id="monthswitcher_main" data-editor-class="monthswitcher_main_css"
               class="monthswitcher_main_css col-sm-12 <?php echo setupBSElementsData($index_controller, 'monthswitcher_main', 'container'); ?>" style="width: 90%;height: fit-content;" <?php echo $index_controller->getBsId($index_controller, 'monthswitcher_main', 'container'); ?>>
              <div class="row flex-fill d-flex justify-content-center ">
              <div id="month_controler_container" class="col-sm-5">
                <!-- month switcher start -->
                <div data-editor-type="container" id="monthswitcher_parent" data-editor-class="monthswitcher_css"
                  class="monthswitcher_css container month_row flex-wrap p-2 text-black border border-light <?php echo setupBSElementsData($index_controller, 'monthswitcher_parent', 'container', 'd-flex align-items-start justify-content-between'); ?>" <?php echo $index_controller->getBsId($index_controller, 'monthswitcher_parent', 'container'); ?>>
                  <i data-editor-type="element" id="monthswitcher_left" data-editor-class="monthswitcher_left_css"
                   class="monthswitcher_left_css display-6 flex-fill fa fa-arrow-circle-left text-white month_arrow <?php echo setupBSElementsData($index_controller, 'monthswitcher_left', 'element'); ?>"
                  data-month="<?php
                  if (is_numeric($index_controller->get_current_month()->get_month())){
                    echo $index_controller->get_current_month()->get_month() >= 2 ? $index_controller->get_current_month()->get_month() - 1 : 1;
                  }
                  ?>" <?php echo $index_controller->getBsId($index_controller, 'monthswitcher_left', 'element'); ?>></i>
                  <h3 data-editor-type="element" data-editor-class="monthswitcher_select_css"
                      id="selected_month_name" class="flex-fill month_name text_shadow01 text-white monthswitcher_select_css <?php echo setupBSElementsData($index_controller, 'selected_month_name', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'selected_month_name', 'element'); ?>>
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
                  ?>" <?php echo $index_controller->getBsId($index_controller, 'monthswitcher_right', 'element'); ?>></i>
                </div>
                <!-- month switcher end -->
              </div>
              <!-- month controller end -->

              <div data-editor-type="container" id="year_select_cont"
                 data-editor-class="year_select_cont_css" class="year_select_cont_css col-sm-5 rounded <?php echo setupBSElementsData($index_controller, 'year_select_cont', 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, 'year_select_cont', 'container'); ?>>
                <!-- year display start -->
                <form data-editor-type="container" id="year_select_form"
                   data-editor-class="year_select_form_css" class="year_select_form_css flex-fill <?php echo setupBSElementsData($index_controller, 'year_select_form', 'container'); ?>" action="./index.php" method="GET" id="year_form" <?php echo $index_controller->getBsId($index_controller, 'year_select_form', 'container'); ?>>
                  <select data-editor-type="element" data-editor-class="year_select_css"
                     class="year_select_css form-control <?php echo setupBSElementsData($index_controller, 'year', 'element'); ?>" name="year" id="year" <?php echo $index_controller->getBsId($index_controller, 'year', 'element'); ?>>
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
                class="month_numbers_main_css col-sm-12 bg_dimgray p-2 <?php echo setupBSElementsData($index_controller, 'month_numbers_main', 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, 'month_numbers_main', 'container'); ?>>
                <!-- months numbers switch month -->
                <div data-editor-type="container" id="month_numbers" data-editor-class="month_numbers_css"
                  class="btn-group btn-sm flex-wrap month_small_btns month_numbers_css <?php echo setupBSElementsData($index_controller, 'month_numbers', 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, 'month_numbers', 'container'); ?>>
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
                        class="btn_arrow_month month_form bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn <?php echo setupBSElementsData($index_controller, $btnidhtml, 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, $btnidhtml, 'element'); ?>>
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
            class="p-1 col-sm-12 bg_dimgray calendar_first_container_css <?php echo setupBSElementsData($index_controller, 'calendar_first_container', 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, 'calendar_first_container', 'container'); ?>>

            <!-- Calendar display start -->
            <div id="main_calendar_container"
              class="main_calendar_container_css calendar border border-dark p-2 mt-3 mb-5 container-fluid bg-white">
              <div data-editor-type="container" id="cal_date_title_cont" data-editor-class="cal_date_title_contcss"
              class="cal_date_title_contcss <?php echo setupBSElementsData($index_controller, 'cal_date_title_cont', 'container', '', true); ?>"  <?php echo $index_controller->getBsId($index_controller, 'cal_date_title_cont', 'container'); ?>>

              <h5 data-editor-type="element" id="cal_date_title_elm" data-editor-class="cal_date_title_contcss" class="cal_date_title_contcss text_black <?php echo setupBSElementsData($index_controller, 'cal_date_title_elm', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'cal_date_title_elm', 'element'); ?>><?php
              $smonth = intval($index_controller->get_current_month()->get_month()) > 9 ? $index_controller->get_current_month()->get_month() : '0' . $index_controller->get_current_month()->get_month();

              echo $index_controller->get_current_year()->get_year() . '-' . $smonth . '-01'; ?></h5></div>
              <!-- week Titles row start -->
              <div data-editor-type="container" id="day_namecont" data-editor-class="day_namecont_css"
                class="day_namecont_css p-2 cal_days_titles <?php echo setupBSElementsData($index_controller, 'day_namecont', 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_namecont', 'container'); ?>>

                <div data-editor-type="element" id="day_name_parent1" data-editor-class="day_name_parentcss" data-editor-group="2"
                class="day_name_parentcss day_outer_name_contcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent1', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_name_parent1', 'element'); ?>>
                  <span data-editor-type="element" id="day_mon" data-editor-class="day_mon_css" class="full_day day_mon_css <?php echo setupBSElementsData($index_controller, 'day_mon', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_mon', 'element'); ?>>Monday</span>
                  <span data-editor-type="element" id="day_mon_mob" data-editor-class="day_mon_mob_css" class="short_day day_mon_mob_css <?php echo setupBSElementsData($index_controller, 'day_mon_mob', 'element'); ?>" style="display:none;" <?php echo $index_controller->getBsId($index_controller, 'day_mon_mob', 'element'); ?>>Mon</span>
                </div>

                <div data-editor-type="element" id="day_name_parent2" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent2', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_name_parent2', 'element'); ?>>
                  <span data-editor-type="element" id="day_tue" data-editor-class="day_tue_css" class="full_day day_tue_css <?php echo setupBSElementsData($index_controller, 'day_tue', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_tue', 'element'); ?>>Tuesday</span>
                  <span data-editor-type="element" id="day_tue_mob" data-editor-class="day_tue_mob_css" class="short_day day_tue_mob_css <?php echo setupBSElementsData($index_controller, 'day_tue_mob', 'element'); ?>" style="display:none;" <?php echo $index_controller->getBsId($index_controller, 'day_tue_mob', 'element'); ?>>Tue</span>
                </div>
                <div data-editor-type="element" id="day_name_parent3" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent3', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_name_parent3', 'element'); ?>>
                  <span data-editor-type="element" id="day_wed" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_wed', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_wed', 'element'); ?>>Wednesday</span>
                  <span data-editor-type="element" id="day_wed_mob" data-editor-class="day_wed_mob_css" class="short_day day_wed_mob_css <?php echo setupBSElementsData($index_controller, 'day_wed_mob', 'element'); ?>" style="display:none;" <?php echo $index_controller->getBsId($index_controller, 'day_wed_mob', 'element'); ?>>Wed</span>
                </div>
                <div data-editor-type="element" id="day_name_parent4" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent4', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_name_parent4', 'element'); ?>>
                  <span data-editor-type="element" id="day_thu" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_thu', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_thu', 'element'); ?>>Thursday</span>
                  <span data-editor-type="element" id="day_thu_mob" data-editor-class="day_thu_mob_css" class="short_day day_thu_mob_css <?php echo setupBSElementsData($index_controller, 'day_thu_mob', 'element'); ?>" style="display:none;" <?php echo $index_controller->getBsId($index_controller, 'day_thu_mob', 'element'); ?>>Thu</span>
                </div>
                <div data-editor-type="element" id="day_name_parent5" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent5', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_name_parent5', 'element'); ?>>
                  <span  data-editor-type="element"id="day_fri" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_fri', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_fri', 'element'); ?>>Friday</span>
                  <span  data-editor-type="element" id="day_fri_mob" data-editor-class="day_fri_mob_css" class="short_day day_fri_mob_css <?php echo setupBSElementsData($index_controller, 'day_fri_mob', 'element'); ?>" style="display:none;" <?php echo $index_controller->getBsId($index_controller, 'day_fri_mob', 'element'); ?>>Fri</span>
                </div>
                <div data-editor-type="element" id="day_name_parent6" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent6', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_name_parent6', 'element'); ?>>
                  <span  data-editor-type="element" id="day_sat" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_sat', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_sat', 'element'); ?> <?php echo $index_controller->getBsId($index_controller, 'day_name_parent6', 'element'); ?>>Saturday</span>
                  <span  data-editor-type="element" id="day_sat_mob" data-editor-class="day_sat_mob_css" class="short_day day_sat_mob_css <?php echo setupBSElementsData($index_controller, 'day_sat_mob', 'element'); ?>" style="display:none;" <?php echo $index_controller->getBsId($index_controller, 'day_sat_mob', 'element'); ?>>Sat</span>
                </div>
                <div data-editor-type="element" id="day_name_parent7" data-editor-class="day_name_parentcss" data-editor-group="2" class="day_name_parentcss flex-fill border border-light cal_card_cell <?php echo setupBSElementsData($index_controller, 'day_name_parent7', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_name_parent7', 'element'); ?>>
                  <span  data-editor-type="element" id="day_sun" data-editor-class="day_thu_css" class="full_day day_thu_css <?php echo setupBSElementsData($index_controller, 'day_sun', 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, 'day_sun', 'element'); ?>>Sunday</span>
                  <span  data-editor-type="element" id="day_sun_mob" data-editor-class="day_sun_mob_css" class="short_day day_sun_mob_css <?php echo setupBSElementsData($index_controller, 'day_sun_mob', 'element'); ?>" style="display:none;" <?php echo $index_controller->getBsId($index_controller, 'day_sun_mob', 'element'); ?>>Sun</span>
                </div>
              </div>
              <!-- week Titles row end -->
              <!-- hidden week scroll buttons -->
              <div data-editor-type="container" id="weekscroll_container" data-editor-class="weekscroll_containercss"  class="allweeks weekscroll_containercss flex-column p-2 <?php echo setupBSElementsData($index_controller, 'weekscroll_container', 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, 'weekscroll_container', 'container'); ?>>
                <?php
                  if ($current_weeks && !empty($current_weeks)){
                    for ($cw=0; $cw<count($current_weeks); $cw++){
                      $week_id = 'week_' . ($cw+1);
                      ?>
                      <div data-editor-type="element"
                      id="<?php echo 'scrollbtn_'.$week_id; ?>" data-editor-group="3" data-editor-class="scroll_left_btncss"
                      class="scroll_to_btns scroll_left_btncss flex-fill border border-primary btn  btn-secondary text-white  mt-1 mb-1 <?php echo setupBSElementsData($index_controller, ('scrollbtn_'.$week_id), 'element'); ?>"
                      data-target="<?php echo $week_id; ?>" <?php echo $index_controller->getBsId($index_controller, ('scrollbtn_'.$week_id), 'element'); ?>><?php echo ($cw+1); ?></div>
                      <?php
                    }
                  }
                ?>

                <div data-editor-type="element" id="map_booking_modal_open" data-editor-class="map_resevation_css" class="map_resevation_css flex-fill border border-primary btn btn-light mt-1 mb-1 aside_add_res <?php echo setupBSElementsData($index_controller, 'map_booking_modal_open' , 'element'); ?>" data-bs-toggle="modal" data-bs-target="#mapBookingModal" <?php echo $index_controller->getBsId($index_controller, 'map_booking_modal_open', 'element'); ?>>
                 <i class="fa fa-plus text-primary"></i>
                </div>

                <div data-editor-type="element" data-editor-class="open_style_editorcss" id="open_style_editor"
                  class="open_style_editorcss toggle_asside magical_btn flex-fill border border-primary btn  btn-danger text-white  mt-1 mb-1 <?php echo setupBSElementsData($index_controller, 'open_style_editor' , 'element'); ?>"
                  title="Close The Style Editor" data-bs-original-title="Close The Style Editor" <?php echo $index_controller->getBsId($index_controller, 'open_style_editor', 'element'); ?>><i class="fa fa-magic text-white"></i>
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

                        $day_calid = 0;
                        $usedcal_here = $index_controller->get_used_calendar();

                        if (isset($day_data['day']) && isset($usedcal_here)){

                          $day_calid = $index_controller->get_day_cal_id($day_data['day']->get_id());
                          $day_calid = $usedcal_here->get_id() == $day_calid ? $day_calid : 0;
                        }
                        if ($day_data == false || empty($day_data) || !$day_calid){
                          // empty day
                          $delm_id = 'x_day_' . $d . '_' . $index_controller->get_current_month()->get_month();
                          ?>
                          <!-- empty day -->
                          <!-- day start -->
                          <div data-editor-type="element" data-editor-class="day_x_css"
                          data-editor-group="4"  id="<?php echo $delm_id; ?>" class="day_x_css flex-fill border border-light cal_card_cell day_card null_day <?php echo setupBSElementsData($index_controller, $delm_id, 'element'); ?>"
                          title="This day is not available The selected month is : <?php echo $week_count; ?> Days" <?php echo $index_controller->getBsId($index_controller, $delm_id, 'element'); ?>>

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

                          $periods_dayid = 'all_periods_container_' . $day_id;
                          $day_cont_id = 'day_container_'.($d+1);
                        ?>
                        <!-- day start -->
                        <div data-day="<?php echo $day_name; ?>" data-editor-type="element" data-editor-group="50" data-editor-class="day_main_cont_css" class="day_main_cont_css flex-fill border border-light cal_card_cell day_card day_style_css <?php echo setupBSElementsData($index_controller, $day_cont_id , 'element'); ?>" id="<?php echo $day_cont_id;  ?>" <?php echo $index_controller->getBsId($index_controller, $day_cont_id, 'element'); ?>>
                           <!-- day meta -->
                             <h6 data-editor-type="element" data-editor-group="5" id="<?php echo $title_id_html;  ?>" data-editor-class="day_title_textcss" class="day_title_textcss text-center font_80em <?php echo setupBSElementsData($index_controller, $title_id_html , 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, $title_id_html, 'element'); ?>><?php echo substr($day_name, 0, 3) . ' ' . $day; ?></h6>
                             <h6 data-editor-type="element" data-editor-group="6" id="<?php echo $date_id_html;  ?>"  data-editor-class="day_date_css"  class="day_date_css text-center bg-light text-black badge <?php echo setupBSElementsData($index_controller, $date_id_html , 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, $date_id_html, 'element'); ?>><?php echo $day_date; ?></h6>
                           <!-- array_distribution -->
                           <!-- all periods start -->
                           <div data-editor-type="container" id="<?php echo $periods_dayid; ?>" data-editor-class="all_periods_containercss"  class="all_periods_containercss all_periods flex-column flex-nowrap <?php echo setupBSElementsData($index_controller, $periods_dayid, 'container'); ?>" <?php echo $index_controller->getBsId($index_controller, $periods_dayid, 'container'); ?>>
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
                                 data-editor-group="8" class="<?php echo setupBSElementsData($index_controller, $p_element_id, 'container'); ?> period_container_css flex-column flex-nowrap container period_background_default <?php echo $p_element_class; ?>" id="<?php echo $p_element_id; ?>" <?php echo $index_controller->getBsId($index_controller, $p_element_id, 'container'); ?>>
                                    <!-- period title -->
                                    <span data-editor-type="element" id="<?php echo $period_id_html; ?>" data-editor-group="7" data-editor-class="period_description_css" class="period_description_css badge bg-secondary p-1 mt-1 period_title_default <?php echo setupBSElementsData($index_controller, $date_id_html , 'element'); ?>" <?php echo $index_controller->getBsId($index_controller, $date_id_html, 'element'); ?>><?php echo $p_description; ?></span>
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
                                        $slot_cont_id = 'cont_' . $s_element_id;

                                        // used slot
                                        if ($s_empty == 0){
                                          // display used slot
                                          ?>
                                          <!-- slot start booked already -->
                                          <div
                                            data-editor-class="slot_cont_css"
                                            data-editor-type="element"
                                            class="<?php echo setupBSElementsData($index_controller, $slot_cont_id, 'element'); ?> w-100 slot_background_default slot_cont_css p-1 m-1 used_slot <?php echo $s_element_class; ?>"
                                            id="<?php echo $slot_cont_id; ?>" data-editor-group="8" <?php echo $index_controller->getBsId($index_controller, $slot_cont_id, 'element'); ?>>
                                           <div data-editor-type="container" data-editor-group="9" id="<?php echo $s_element_id; ?>" data-editor-class="slot_child_contcss"
                                            class="w-100 flex-fill slot_child_contcss <?php echo setupBSElementsData($index_controller, $s_element_id, 'container', 'justify-content-between align-items-center'); ?>" <?php echo $index_controller->getBsId($index_controller, $s_element_id, 'container'); ?>>
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
                                          <div data-editor-type="container" data-editor-group="10" data-editor-class="slot_cont_css" class="<?php echo setupBSElementsData($index_controller, 'container_slot_' . $s_element_id, 'container'); ?> slot_background_default slot_cont_css p-1 m-1 empty_slot <?php echo $s_element_class; ?>" id="container_slot_<?php echo $s_element_id; ?>" <?php echo $index_controller->getBsId($index_controller, 'container_slot_' . $s_element_id, 'container'); ?>>
                                           <div data-editor-type="element" data-editor-group="12" id="<?php echo $s_element_id; ?>" data-editor-class="slot_child_css" class="padding-1 flex-fill slot_child_css <?php echo setupBSElementsData($index_controller, $s_element_id, 'element', 'd-flex justify-content-between align-items-center'); ?>"
                                             <?php echo $index_controller->getBsId($index_controller, $s_element_id, 'element'); ?>>

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

                                             <!-- end noob part -->


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
                <input id="add_booking_input1" maxlength="30"  pattern="[A-Za-z\s]{1,30}" title="Please Enter a valid name: eg Jone" type="text" class="form-control" placeholder="Name.." name="reservation_name" id="reservation_name" />
                <textarea id="add_booking_input2" min="0" maxlength="255" placeholder="Reservation Notes.." class="form-control" rows="3" id="reservation_comment" name="reservation_comment"></textarea>
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



<!-- view empty slot modal start -->
<div class="modal fade" aria-modal="true" role="dialog" id="resetbootstrapModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Bootstrap Factory Reset</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <div id="bootstrap_reset_alert">
        </div>
        <p>If you click this button, all bootstrap class style patterns related to <strong style="font-size:14px !important;" class="badge bg-primary">[<?php echo $current_calendar->get_title(); ?>]</strong> will only be reset and there will be no way back.</p>
        <button class="btn btn-danger" type="button" id="back_bs_to_default">Reset</button>
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
         if (isset($index_controller) && !empty($index_controller)){
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

      </div>
      <!-- Modal footer -->
      <div class="modal-footer">

        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Back</button>
      </div>

    </div>
  </div>
</div>
<!-- Cancel reservation modal end -->

<!-- load boostrap modal -->
<?php require_once('bootstrap_options.html'); ?>

<!-- sound effects -->
<audio id="open_modal_sound">
  <source src="https://sndup.net/rp5j/d" type="audio/wav">
</audio>

<audio id="unable_open_modal">
  <source src="https://sndup.net/sbs9/d" type="audio/wav">
</audio>

<!-- HTML5 sounds -->
<script src="assets/js/index.js" type="text/javascript"></script>
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
