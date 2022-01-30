<?php
require_once('../config.php');
require_once('../services/CalendarService.php');
require_once('../services/YearService.php');
require_once('../services/MonthService.php');
require_once('../services/DayService.php');
require_once('../services/PeriodService.php');
require_once('../services/SlotService.php');
require_once('../models/Calendar.php');



function create_calendar($cal_id,$calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period){
  global $pdo;
  $calendar_service = new CalendarService($pdo);
  $year_service = new YearService($pdo);
  $month_service = new MonthService($pdo);
  $day_service = new DayService($pdo);
  $period_service = new PeriodService($pdo);
  $slot_service = new SlotService($pdo);

  $calendar = new Calendar();
  $calendar->init($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period);
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
        for ($period=1; $period<=$periods_per_day; $period++){
          // get 3 dates array (Periods)
          $description = 'period on day' . $full_date;
          $period_id = $period_service->add($day_id, NULL, $description);

          for ($slot=1; $slot<=$slots_per_period; $slot++){
            $slot_id = $slot_service->add('09:30', '12:30', $period_id, True);
          }

        }

      }

    }

    $current_year += 1;


  }

}


function add_new_calendar($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period){
  global $pdo;
  $calendar_service = new CalendarService($pdo);

  $calendar_id = $calendar_service->add($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period);
  create_calendar($calendar_id, $calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period);
  echo 'cal created' . $calendar_id;
  return $calendar_id;
}

$run = False;
if ($run == False){
  //add_new_calendar('Super Calendar', '2022', 1, 3, 3);
  $run = True;
}
?>
