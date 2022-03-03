# What is that

This is a CMS for creating fully customized task boards and calendars to suit your needs, it also gives you full control over new calendars created, edit styles of any element, add custom CSS styles, control the application based on the calendar used, edit title, favicon, background images, (This tool to create and manage different task board with secure login and registration with Remember me and advanced secure against most known hacking types such as XSS, SQL injection, password guessing, remote attack, notification in case the user is hacked and get information about the hacker's computer, on Example: IP address, country, type of operating system), with best UX and good performance and using New custom technique created for this app PHP custom  MVC

![image](https://user-images.githubusercontent.com/55125302/156524210-1a57a3df-e514-4a43-ad81-57c3dbadaa59.png)


# super_taskboard
MVC PHP task board (This not normal taskbaord) This full system to create full customize taskboard or booking system that fit any use with full control to app style and texts

![image](https://user-images.githubusercontent.com/55125302/156213051-fd597b3f-d41c-4a30-a490-0af232eb3835.png)


![image](https://user-images.githubusercontent.com/55125302/153796173-6a5901ce-3300-44b8-a35b-af511ed37324.png)


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


[Linkg Text](www.google.com)
