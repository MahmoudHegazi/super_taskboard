# What is that
(The meaning of Dynamic)

This is a CMS for creating fully customized task boards and calendars to suit your needs, it also gives you full control over new calendars created, edit styles of any element, add custom CSS styles, control the application based on the calendar used, edit title, favicon, background images, (This tool to create and manage different task board with secure login and registration with Remember me and advanced secure against most known hacking types such as XSS, SQL injection, password guessing, remote attack, notification in case the user is hacked and get information about the hacker's computer, on Example: IP address, country, type of operating system), with best UX and good performance and using New custom technique created for this app PHP custom  MVC if you study and practice how to use the app you will create stuning calendars. as this pure PHP it work faster and make it easy to any php developer to edit it and update it

### New BS5 Full Calendar Layeout style Editor

(Full Control with all BS5 main options to edit any element or container in the page easy to use and using AJAX for fast edit and always get good result also
for important point which is none developer users who not know bootstrap they can now easly edit options and view on time with ajax the result also using strong sheme
of database with ENUM to make sure every part under control plus using advanced technique to zip very large table with all it's operations read, add, edit, delete into one
single string column on the element which reduce db calls and alot of functions and keep everything in core dynamic and simple (Note this styles is unqiue for each calendar you can have unlimted calendar with total diffrent sizes and total diffrent looking that fit any web app in the world + the app has very big setup page that let you control other thing like web app title, icon, images even signup and login images beside some styles for group componets for example change style of all slots or change style of all periods
but this using styles with another strong service to handle the styles dynamic this app can added into any app also it has strong login system and secured you can replace 
your own with it, and as it MVC app can easly added into your web app and work smooth


![image](https://user-images.githubusercontent.com/55125302/157669246-a763defc-9937-4746-8124-5d2c3736e1f8.png)


![image](https://user-images.githubusercontent.com/55125302/156524210-1a57a3df-e514-4a43-ad81-57c3dbadaa59.png)
![image](https://user-images.githubusercontent.com/55125302/156803898-6f7d2fd1-c306-450a-aee2-4fce9d83de89.png)
![image](https://user-images.githubusercontent.com/55125302/156524645-9444798e-319f-43c7-a024-97c7584cf488.png)
![image](https://user-images.githubusercontent.com/55125302/156524700-7c6ef4eb-14f8-494f-99da-e0e39ebeac15.png)
![image](https://user-images.githubusercontent.com/55125302/156524747-6c68cd2a-6ff9-4983-b7dd-208d7dc9ff72.png)
![image](https://user-images.githubusercontent.com/55125302/156524783-af4d9413-af7b-4b25-a8e8-16c298956d16.png)
![image](https://user-images.githubusercontent.com/55125302/156524859-5e3b5324-4d8d-4e35-ae31-51599f2c26ba.png)
![image](https://user-images.githubusercontent.com/55125302/156525066-0f7d8a31-e257-4410-b7f2-dc76d20b8eda.png)
![image](https://user-images.githubusercontent.com/55125302/156525107-acc11015-8f64-4c96-81d7-44f3f992b53f.png)
![image](https://user-images.githubusercontent.com/55125302/156525130-1700f032-325c-45be-8a95-28794bb9c4cd.png)

#### bootstrap5 and css3 and ES6 and JQuery aside
![image](https://user-images.githubusercontent.com/55125302/156806324-b5930a1e-1c63-46b6-86ad-855087f1e096.png)



in this project I tried to make everything work as single unit to reach the last dynamic level can made by leting everything can work or edited alone smothly without issue, also
every part in calendar is a single object and has it's service, class/modal and SQL mapper, also I tried as much as possible to make eveything can edited alone even styles, also secret tip, this app will add element_id for each slot or period you can look for ids and add custom style for some single elements to group them beside the main a lots of ways let you group elements with style using class or custom css with id and !important etc





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
