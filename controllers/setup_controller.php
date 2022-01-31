<?php
require_once('../config.php');
require_once('../functions.php');
require_once('../services/CalendarService.php');
require_once('../services/YearService.php');
require_once('../services/MonthService.php');
require_once('../services/DayService.php');
require_once('../services/PeriodService.php');
require_once('../services/SlotService.php');
require_once('../models/Calendar.php');


$redirect_url = $_SERVER['HTTP_REFERER'];


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
  $calendar_service = new CalendarService($pdo);


  $calendar_id = $calendar_service->add($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description);
  create_calendar($calendar_id, $calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $periods_data, $slots_data);
  echo 'cal created' . $calendar_id;
  return $calendar_id;
}

$run = False;
if ($run == False){
  //add_new_calendar('Super Calendar', '2022', 1, 3, 3);
  $run = True;
}

/*
$redirect_url = add_query_parameters($redirect_url,array('message'), array('Missing Period values please fill all Period input.'));
header("Location: " . $redirect_url);
*/



if (
  isset($_POST['calendar_title']) && !empty($_POST['calendar_title']) &&
  isset($_POST['start_year']) && !empty($_POST['start_year']) &&
  isset($_POST['add_new_year']) && !empty($_POST['add_new_year']) &&
  isset($_POST['period_per_day']) && !empty($_POST['period_per_day']) &&
  isset($_POST['slots_per_period']) && !empty($_POST['slots_per_period'])
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
    echo "<br /><pre>";
    print_r($periods_data);
    echo "</pre>";
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
  echo '<br /><h2>Slots</h2><pre>';
  print_r($slots_data);
  echo '</pre>';



  if ($ready == True){

    add_new_calendar($calendar_title, $start_year, $add_new_year, $period_per_day, $slots_per_period, $calendar_description, $periods_data, $slots_data);
  }
}
?>
