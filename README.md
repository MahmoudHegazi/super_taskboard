# super_taskboard
MVC PHP task board


## create calendar

```php
function add_years_to_calendar($years,$start){
  $current_year = $start;
  
  for ($y=0; $y<$years; $y++){
    $current_year += 1;
    // add 12 months

    for ($month=1; $month<=12; $month++){
      // get days in the month
      $month_days = cal_days_in_month(CAL_GREGORIAN,$month,$current_year);
      for ($day=1; $day<=$month_days; $day++){
        $day_name = jddayofweek();
        $jd=gregoriantojd($month,$day,$current_year);
        echo jddayofweek($jd,1) . '<br />';
      }
    }
    echo '<br /><br /><hr />';
  }
}
add_years(2,1990);
```
