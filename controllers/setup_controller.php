<?php
ob_start();
require_once (dirname(__FILE__, 2) . '\config.php');
require_once (dirname(__FILE__, 2) . '\functions.php');
require_once (dirname(__FILE__, 2) . '\services\CalendarService.php');
require_once (dirname(__FILE__, 2) . '\services\YearService.php');
require_once (dirname(__FILE__, 2) . '\services\MonthService.php');
require_once (dirname(__FILE__, 2) . '\services\DayService.php');
require_once (dirname(__FILE__, 2) . '\services\PeriodService.php');
require_once (dirname(__FILE__, 2) . '\services\SlotService.php');
require_once (dirname(__FILE__, 2) . '\services\UserService.php');
require_once (dirname(__FILE__, 2) . '\services\StyleService.php');
require_once (dirname(__FILE__, 2) . '\models\Calendar.php');

$_SESSION['error_displayed'] = False;

$redirect_url = $_SERVER['HTTP_REFERER'];
/*
if (!isset($redirect_url)){
  header("Location: ../index.php");
}
*/

function create_calendar($cal_id, $calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $periods_data, $slots_data)
{
    global $pdo;
    $calendar_service = new CalendarService($pdo);
    $year_service = new YearService($pdo);
    $month_service = new MonthService($pdo);
    $day_service = new DayService($pdo);
    $period_service = new PeriodService($pdo);
    $slot_service = new SlotService($pdo);
    $style_service = new StyleService($pdo);

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

    $period_id_index = 1;
    $slot_id_index = 1;
    for ($y = 0;$y < $added_years;$y++)
    {
        //add year
        $yearid = $year_service->add($current_year, $cal_id);

        // add 12 months
        for ($month = 1;$month <= 12;$month++)
        {
            // add month
            $month_id = $month_service->add($month, $yearid);

            // get days in the month
            $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $current_year);
            for ($day = 1;$day <= $month_days;$day++)
            {
                $month_string = $month <= 9 ? '0' . $month : $month;
                $day_string = $day <= 9 ? '0' . $day : $day;
                $full_date = $current_year . '-' . $month_string . '-' . $day_string;



                $jd = gregoriantojd($month, $day, $current_year);
                $dayname = jddayofweek($jd, 1);
                // add day
                $day_id = $day_service->add($day, $dayname, $full_date, $month_id);
                if ($periods_per_day > 0 && $periods_per_day == $periods_per_day)
                {

                    for ($period = 1;$period <= $periods_per_day;$period++)
                    {
                        // get 3 dates array (Periods)
                        $current_period = $periods_data[$period - 1];
                        $description = isset($current_period['description']) && !empty($current_period['description']) ? $current_period['description'] : NULL;
                        $perioddate = isset($current_period['period_date']) && !empty($current_period['period_date']) ? $full_date . ' ' . $current_period['period_date'] : NULL;


                        $period_element_id = 'period_id_' . $period_id_index;
                        $period_index_classname = 'period_class_' . $period;
                        $period_id_index += 1;

                        // get styles data
                        $current_style = $periods_data[$period - 1]['styles'];
                        $font_color = $current_style['color'];
                        $background_color = $current_style['background'];
                        $font_size = $current_style['fontsize'];
                        $font_family = $current_style['fontfamily'];

                        $border = $current_style['border'];
                        $customcss = $current_style['customcss'];

                        $period_id = $period_service->add($day_id, $perioddate, $description, $period, $period_element_id, $period_index_classname);

                        // insert styles
                        if ($font_color && isset($font_color) && !empty($font_color))
                        {
                            $style_service->add($period_index_classname, $period_element_id, $font_color, $period_id, 1, 'Period Font Color ' . $period, 0, $calendar->get_id(), 'color');
                        }

                        if ($background_color && isset($background_color) && !empty($background_color))
                        {
                            $style_service->add($period_index_classname, $period_element_id, $background_color, $period_id, 1, 'Period Background Color ' . $period, 0, $calendar->get_id(), 'backgroundcolor');
                        }

                        if ($font_size && isset($font_size) && !empty($font_size))
                        {
                            $style_service->add($period_index_classname, $period_element_id, $font_size, $period_id, 1, 'Period Font Size ' . $period, 0, $calendar->get_id(), 'fontsize');
                        }

                        if ($font_family && isset($font_family) && !empty($font_family))
                        {
                            $style_service->add($period_index_classname, $period_element_id, $font_family, $period_id, 1, 'Period Font Family ' . $period, 0, $calendar->get_id(), 'fontfamily');
                        }
                        if ($border && isset($border) && !empty($border))
                        {
                            $style_service->add($period_index_classname, $period_element_id, $border, $period_id, 1, 'Period Border ' .$period, 0, $calendar->get_id(), 'border');
                        }

                        if ($customcss && isset($customcss) && !empty($customcss) && count($customcss) > 0)
                        {
                            for ($cs = 0;$cs < count($customcss);$cs++)
                            {
                                $custom_title = 'Period Custom: ' . $period . ', ' . ($cs + 1);
                                $style_service->add($period_index_classname, $period_element_id, $customcss[$cs], $period_id, 1, $custom_title, 1, $calendar->get_id(), 'custom');
                            }
                        }

                        /* end insert styles periods */

                        if ($slots_per_period > 0 && count($slots_data) == $slots_per_period)
                        {

                            for ($slot = 1;$slot <= $slots_per_period;$slot++)
                            {
                                $current_slot = $slots_data[$slot - 1];

                                $slot_id_name = 'slot_id_' . $slot;
                                $slot_class_name = 'slot_class_' . $slot;

                                $description = $current_slot['start_from'];
                                $perioddate = $current_slot['end_at'];

                                $start_from = isset($current_slot['start_from']) && !empty($current_slot['start_from']) ? $current_slot['start_from'] : NULL;
                                $end_at = isset($current_slot['end_at']) && !empty($current_slot['end_at']) ? $current_slot['end_at'] : NULL;

                                // get styles data
                                $slot_element_id = 'slot_id_' . $slot_id_index;
                                $slot_index_classname = 'slot_class_' . $slot;
                                $slot_id_index += 1;

                                $current_style_s = $current_slot['styles'];
                                $font_color_s = $current_style_s['color'];
                                $background_color_s = $current_style_s['background'];
                                $font_size_s = $current_style_s['fontsize'];
                                $font_family_s = $current_style_s['fontfamily'];

                                $border_s = $current_style_s['border'];
                                $customcss_s = $current_style_s['customcss'];
                                $slot_id = $slot_service->add($start_from, $end_at, $period_id, True, $slot, $slot_element_id, $slot_index_classname);


                                // insert styles
                                if ($font_color_s && isset($font_color_s) && !empty($font_color_s))
                                {
                                    $style_service->add($slot_index_classname, $slot_element_id, $font_color_s, $slot_id, 1, 'Slot Font Color ' . $slot, 0, $calendar->get_id(), 'color');
                                }

                                if ($background_color_s && isset($background_color_s) && !empty($background_color_s))
                                {
                                    $style_service->add($slot_index_classname, $slot_element_id, $background_color_s, $slot_id, 1, 'Slot Background Color ' . $slot, 0, $calendar->get_id(), 'backgroundcolor');
                                }

                                if ($font_size_s && isset($font_size_s) && !empty($font_size_s))
                                {
                                    $style_service->add($slot_index_classname, $slot_element_id, $font_size_s, $slot_id, 1, 'Slot Font Size ' . $slot, 0, $calendar->get_id(), 'fontsize');
                                }

                                if ($font_family_s && isset($font_family_s) && !empty($font_family_s))
                                {
                                    $style_service->add($slot_index_classname, $slot_element_id, $font_family_s, $slot_id, 1, 'Slot Font Family ' . $slot, 0, $calendar->get_id(), 'fontfamily');
                                }
                                if ($border_s && isset($border_s) && !empty($border_s))
                                {
                                    $style_service->add($slot_index_classname, $slot_element_id, $border_s, $slot_id, 1, 'Slot Border ' . $slot, 0, $calendar->get_id(), 'border');
                                }
                                if ($customcss_s && isset($customcss_s) && !empty($customcss_s) && count($customcss_s) > 0)
                                {
                                    for ($cs = 0;$cs < count($customcss_s);$cs++)
                                    {
                                        $custom_title = 'Slot Custom: '. $slot . ', ' . ($cs + 1);
                                        $style_service->add($slot_index_classname, $slot_element_id, $customcss_s[$cs], $slot_id, 1, $custom_title, 1, $calendar->get_id(), 'custom');
                                    }
                                }
                                /* insert styles end */


                            }
                        }

                    }
                }

            }

        }

        $current_year += 1;

    }

}

function add_new_calendar($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $periods_data, $slots_data)
{
    global $pdo;
    $can_upload = True;
    $reason = '';
    $calendar_service = new CalendarService($pdo);
    $used = empty($calendar_service->get_all_calendars(1)) ? 1 : 0;

    $calendar_id = $calendar_service->add($calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $used);
    // rules
    if (!$calendar_id)
    {
        $can_upload = False;
        $reason = 'Missing calendar id';
    }

    if (intval($start_year) < 1900 || intval($start_year) > 5000)
    {
        $can_upload = False;
        $reason = 'Start Year can not be less than 1900 or highr than 5000 selected year is: ' . $start_year;
    }

    if (intval($added_years) < 1)
    {
        $can_upload = False;
        $reason = 'No Added Years Deatected Added Year must be positve number';
    }

    if (empty($calendar_name))
    {
        $can_upload = False;
        $reason = 'Please Prove Calendar Title is required';
    }

    if ($can_upload == True)
    {
        create_calendar($calendar_id, $calendar_name, $start_year, $added_years, $periods_per_day, $slots_per_period, $description, $periods_data, $slots_data);
    }

    return array(
        'success' => $can_upload,
        'reason' => $reason,
        'calendar_id' => $calendar_id
    );

}

/*
$redirect_url = add_query_parameters($redirect_url,array('message'), array('Missing Period values please fill all Period input.'));
header("Location: " . $redirect_url);
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

    if (isset($_POST['calendar_title']) && !empty($_POST['calendar_title']) && isset($_POST['start_year']) && !empty($_POST['start_year']) && isset($_POST['add_new_year']) && !empty($_POST['add_new_year']) && isset($_POST['period_per_day']) && isset($_POST['slots_per_period']))
    {
        global $pdo;
        $style_service = new StyleService($pdo);
        //add_new_calendar('Super Calendar', '2022', 1, 3, 3);
        $calendar_title = test_input($_POST['calendar_title']);
        $start_year = test_input($_POST['start_year']);
        $add_new_year = intval(test_input($_POST['add_new_year']));
        $period_per_day = intval(test_input($_POST['period_per_day']));
        $slots_per_period = intval(test_input($_POST['slots_per_period']));
        $calendar_description = isset($_POST['calendar_description']) && !empty($_POST['calendar_description']) ? test_input($_POST['calendar_description']) : 'Calendar to book';
        $periods_data = array();
        $slots_data = array();

        $ready = True;

        // this how I get dynamic the slots and period data JS/PHP syncyed + easy way
        /* ##----------- periods -----------## */

        if ($period_per_day > 0)
        {
            for ($period_index = 0;$period_index < $period_per_day;$period_index++)
            {
                $dateInput = 'period_date_' . ($period_index + 1);
                $descInput = 'period_description_' . ($period_index + 1);

                $dateInput = isset($_POST[$dateInput]) && !empty($_POST[$dateInput]) ? test_input($_POST[$dateInput]) : '';
                $descInput = isset($_POST[$descInput]) && !empty($_POST[$descInput]) ? test_input($_POST[$descInput]) : '';

                /* Get Styles for periods */

                $colort = 'period_color_' . ($period_index + 1);
                $pcolor = isset($_POST[$colort]) && !empty($_POST[$colort]) ? test_input($_POST[$colort]) : False;

                $backgroundt = 'period_background_' . ($period_index + 1);
                $pbackground = isset($_POST[$backgroundt]) && !empty($_POST[$backgroundt]) ? test_input($_POST[$backgroundt]) : False;

                $fontfamilyt = 'period_fontfamily_' . ($period_index + 1);
                $pfontfamily = isset($_POST[$fontfamilyt]) && !empty($_POST[$fontfamilyt]) && $style_service->is_valid_css($_POST[$fontfamilyt]) ? test_input($_POST[$fontfamilyt]) : False;

                $fontsizet = 'period_fontsize_' . ($period_index + 1);
                $pfontsize = isset($_POST[$fontsizet]) && !empty($_POST[$fontsizet]) && $style_service->is_valid_css($_POST[$fontsizet]) ? test_input($_POST[$fontsizet]) : False;

                $bordersizet = 'period_border_size_' . ($period_index + 1);
                $pborder_size = isset($_POST[$bordersizet]) && !empty($_POST[$bordersizet]) ? test_input($_POST[$bordersizet]) : False;

                $bordertypet = 'period_border_type_' . ($period_index + 1);
                $pborder_type = isset($_POST[$bordertypet]) && !empty($_POST[$bordertypet]) ? test_input($_POST[$bordertypet]) : False;

                $bordercolort = 'period_border_color_' . ($period_index + 1);
                $pborder_color = isset($_POST[$bordercolort]) && !empty($_POST[$bordercolort]) ? test_input($_POST[$bordercolort]) : False;

                $customcsst = 'period_customcss_' . ($period_index + 1);
                $period_customcss = isset($_POST[$customcsst]) && !empty($_POST[$customcsst]) ? test_input($_POST[$customcsst]) : False;

                /* Border */
                $period_border = '';
                if ($pborder_size && $pborder_type && $pborder_color)
                {
                    $period_border_result = $pborder_size . " " . $pborder_type . " " . $pborder_color;
                    $period_border = $style_service->is_valid_css($period_border_result) ? $period_border_result : False;
                }

                $period_color = '';
                if ($pcolor)
                {
                    $period_color_result = 'color: ' . $pcolor . ';';
                    $period_color = $style_service->is_valid_css($period_color_result) ? $period_color_result : False;
                }

                $period_background = '';
                if ($pbackground)
                {
                    $period_background_result = 'background-color: ' . $pbackground . ';';
                    $period_background = $style_service->is_valid_css($period_background_result) ? $period_background_result : False;
                }

                $customcss_result = $style_service->get_advanced_style_data(explode('|', $period_customcss));
                $customcss_data = $customcss_result && isset($customcss_result) && !empty($customcss_result) ? $customcss_result : array();

                $styles_array = array(
                    'color' => $period_color,
                    'background' => $period_background,
                    'fontsize' => $pfontsize,
                    'fontfamily' => $pfontfamily,
                    'border' => $period_border,
                    'customcss' => $customcss_data,
                );
                array_push($periods_data, array(
                    'period_date' => $dateInput,
                    'description' => $descInput,
                    'styles' => $styles_array
                ));
                /* end Styles for periods */

            }
        }

        /* ##----------- slots -----------## */

        if ($slots_per_period > 0)
        {

            global $pdo;
            $style_service = new StyleService($pdo);

            for ($slots_index = 0;$slots_index < $slots_per_period;$slots_index++)
            {
                $startFromInput = 'start_at_slot_' . ($slots_index + 1);
                $endAtInput = 'end_at_slot_' . ($slots_index + 1);

                $startFromInput = isset($_POST[$startFromInput]) && !empty($_POST[$startFromInput]) ? test_input($_POST[$startFromInput]) : '';
                $endAtInput = isset($_POST[$endAtInput]) && !empty($_POST[$endAtInput]) ? test_input($_POST[$endAtInput]) : '';

                /* Get Styles for slots */

                $colort = 'slot_color_' . ($slots_index + 1);
                $scolor = isset($_POST[$colort]) && !empty($_POST[$colort]) ? test_input($_POST[$colort]) : False;

                $backgroundt = 'slot_background_' . ($slots_index + 1);
                $sbackground = isset($_POST[$backgroundt]) && !empty($_POST[$backgroundt]) ? test_input($_POST[$backgroundt]) : False;

                $fontfamilyt = 'slot_fontfamily_' . ($slots_index + 1);
                $sfontfamily = isset($_POST[$fontfamilyt]) && !empty($_POST[$fontfamilyt]) ? test_input($_POST[$fontfamilyt]) : False;

                $fontsizet = 'slot_fontsize_' . ($slots_index + 1);
                $sfontsize = isset($_POST[$fontsizet]) && !empty($_POST[$fontsizet]) ? test_input($_POST[$fontsizet]) : False;

                $bordersizet = 'slot_border_size_' . ($slots_index + 1);
                $sborder_size = isset($_POST[$bordersizet]) && !empty($_POST[$bordersizet]) ? test_input($_POST[$bordersizet]) : False;

                $bordertypet = 'slot_border_type_' . ($slots_index + 1);
                $sborder_type = isset($_POST[$bordertypet]) && !empty($_POST[$bordertypet]) ? test_input($_POST[$bordertypet]) : False;

                $bordercolort = 'slot_border_color_' . ($slots_index + 1);
                $sborder_color = isset($_POST[$bordercolort]) && !empty($_POST[$bordercolort]) ? test_input($_POST[$bordercolort]) : False;

                $customcsst = 'slot_customcss_' . ($slots_index + 1);
                $slot_customcss = isset($_POST[$customcsst]) && !empty($_POST[$customcsst]) ? test_input($_POST[$customcsst]) : False;

                /* Border */
                $slot_border = '';
                if ($sborder_size && $sborder_type && $sborder_color)
                {
                    $slot_border_result = $sborder_size . " " . $sborder_type . " " . $sborder_color;
                    $slot_border = $style_service->is_valid_css($slot_border_result) ? $slot_border_result : False;

                }


                $slot_color = '';
                if ($scolor)
                {
                    $slot_color_result = 'color: ' . $scolor . ';';
                    $slot_color = $style_service->is_valid_css($slot_color_result) ? $slot_color_result : False;
                }

                $slot_background = '';
                if ($sbackground)
                {
                    $slot_background_result = 'background-color: ' . $sbackground . ';';
                    $slot_background = $style_service->is_valid_css($slot_background_result) ? $slot_background_result : False;
                }

                $customcss_result = $style_service->get_advanced_style_data(explode('|', $slot_customcss));
                $customcss_data = $customcss_result && isset($customcss_result) && !empty($customcss_result) ? $customcss_result : array();


                $styles_array = array(
                    'color' => $slot_color,
                    'background' => $slot_background,
                    'fontsize' => $sfontsize,
                    'fontfamily' => $sfontfamily,
                    'border' => $slot_border,
                    'customcss' => $customcss_data,
                );
                array_push($slots_data, array(
                    "start_from" => $startFromInput,
                    "end_at" => $endAtInput,
                    'styles' => $styles_array
                ));
                /* end Styles for slots */
            }
        }

        if ($ready == True)
        {
            $new_cal = add_new_calendar($calendar_title, $start_year, $add_new_year, $period_per_day, $slots_per_period, $calendar_description, $periods_data, $slots_data);
            if ($new_cal['success'] == True)
            {
                $calendar_id = $new_cal['calendar_id'];
                if (isset($_FILES) && !empty($_FILES) && isset($_FILES['background_image']) && !empty($_FILES['background_image']))
                {
                    $target_dir = "../uploads/images/";

                    $uploadOk = upload_image($_FILES, $target_dir, 'background_image', array(
                        "jpg",
                        "png",
                        "jpeg",
                        "gif"
                    ) , 500000, 'image', $calendar_id);
                    if ($uploadOk['success'] == True)
                    {
                        // update default image if image uploaded
                        global $pdo;
                        $calender_service = new CalendarService($pdo);
                        $updated_image = $calender_service->update_one_column('background_image', $uploadOk['image'], $calendar_id);
                        $message = $updated_image == 0 ? 'Your image not Uploaded But calendar Created With default image' : 'Calendar With id: ' . $calendar_id . ' Created successfully';
                        $success = $uploadOk['success'] == True ? 'true' : 'false';

                        $redirect_url = addOrReplaceQueryParm($redirect_url, 'success', $success);
                        $redirect_url = addOrReplaceQueryParm($redirect_url, 'message', $message);
                        header("Location: " . $redirect_url);
                        return False;
                        die();
                    }
                    else
                    {
                        $redirect_url = replace_query_paremeters($redirect_url, array(
                            'success',
                            'message'
                        ) , array(
                            'false',
                            $uploadOk['reason']
                        ));
                        header("Location: " . $redirect_url);
                        return False;
                        die();
                    }
                }
            }
            else
            {
                $redirect_url = replace_query_paremeters($redirect_url, array(
                    'success',
                    'message'
                ) , array(
                    'false',
                    $new_cal['reason']
                ));
                header("Location: " . $redirect_url);
                return False;
                die();
            }
        }
        else
        {
            $redirect_url = replace_query_paremeters($redirect_url, array(
                'success',
                'message'
            ) , array(
                'false',
                'Unkown Error Calendar Can not setup please edit Configurations and try again'
            ));
            header("Location: " . $redirect_url);
            return False;
            die();
        }

    }

}

function setup_redirect($url, $success, $message)
{
    $url = addOrReplaceQueryParm($url, 'success', $success);
    $url = addOrReplaceQueryParm($url, 'message', $message);
    header("Location: " . $url);
    return False;
    die();
}

/* Remove Calendar */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['remove_calendar_id']) && !empty($_POST['remove_calendar_id']))
    {
        global $pdo;
        $cal_id = intval(test_input($_POST['remove_calendar_id']));
        $calendar_service = new CalendarService($pdo);
        $cal = $calendar_service->get_calendar_by_id($cal_id);
        if ($cal)
        {
            if ($calendar_service->remove($cal_id))
            {

                $is_used_calendar = $cal->get_used();
                $first_remain_cal = $calendar_service->get_all_calendars($limit = 1, $offset = 0);
                if (count($first_remain_cal) > 0)
                {
                    $calendar_service->update_one_column('used', 1, $first_remain_cal[0]->get_id());
                }
                setup_redirect($redirect_url, 'true', 'Calendar: ' . $cal->get_title() . ' Removed successful.');
            }
            else
            {
                setup_redirect($redirect_url, 'false', 'Could not remove Calendar With ID: ' . $cal_id . '.');
            }
        }
        else
        {
            setup_redirect($redirect_url, 'false', 'Calendar Not Found Please Refresh The Page');

        }
    }
}
/* Remove Calendar end */

/* Remove User */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['remove_user_id']) && !empty($_POST['remove_user_id']))
    {
        global $pdo;
        $user_id = intval(test_input($_POST['remove_user_id']));
        $user_service = new UserService($pdo);

        if ($user_service->get_user_by_id($user_id))
        {
            if ($user_service->remove($user_id))
            {
                setup_redirect($redirect_url, 'true', "Successfully removed user with ID:" . $user_id);
            }
            else
            {
                setup_redirect($redirect_url, 'false', 'Could not remove the user');
            }
        }
        else
        {
            setup_redirect($redirect_url, 'false', 'User Not Found Please Refresh The Page');
        }
    }
}
/* Remove User end */

/* Add User */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['fullname']) && !empty($_POST['fullname']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password']))
    {
        global $pdo;

        $fullname = test_input($_POST['fullname']);
        $email = test_input($_POST['email']);
        $username = test_input($_POST['username']);
        $password = test_input($_POST['password']);
        $password_hash = password_hash($password, PASSWORD_DEFAULT, array(
            'cost' => 9
        ));

        //password_verify('anna', $expensiveHash); //Also returns true


        $user_service = new UserService($pdo);
        $new_user = $user_service->add($fullname, $username, $password_hash, $email);
        if ($new_user)
        {
            setup_redirect($redirect_url, 'true', "Successfully Add user with ID:" . $new_user . " and Name:" . $fullname);
        }
        else
        {
            setup_redirect($redirect_url, 'false', 'User ' . $fullname . ' can not added');
        }
    }
}
/* add User end */

/* update user */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['fullname_edit']) && !empty($_POST['fullname_edit']) && isset($_POST['email_edit']) && !empty($_POST['email_edit']) && isset($_POST['username_edit']) && !empty($_POST['username_edit']) && isset($_POST['userid_edit']) && !empty($_POST['userid_edit']))
    {

        global $pdo;
        $user_service = new UserService($pdo);
        $req_uid = test_input($_POST['userid_edit']);
        $selected_user = $user_service->get_user_by_id($req_uid);
        if ($selected_user)
        {

            $req_email = test_input($_POST['email_edit']);
            $req_username = test_input($_POST['username_edit']);
            $req_name = test_input($_POST['fullname_edit']);
            $update_string = '';

            if ($selected_user->get_name() != $req_name)
            {
                $user_service->update_one_column('name', $req_name, $selected_user->get_id());
                $update_string .= 'name,';
            }
            if ($selected_user->get_username() != $req_username)
            {
                $user_service->update_one_column('username', $req_username, $selected_user->get_id());
                $update_string .= 'username,';
            }
            if ($selected_user->get_email() != $req_email)
            {
                $user_service->update_one_column('email', $req_email, $selected_user->get_id());
                $update_string .= 'email,';
            }

            if (isset($_POST['password_edit']) && !empty($_POST['password_edit']))
            {
                $update_string .= 'password,';
                // password not enabled by default so when he come it need change pass
                $req_password = test_input($_POST['password_edit']);
                $password_hash = password_hash($req_password, PASSWORD_DEFAULT, array(
                    'cost' => 9
                ));
                $user_service->update_one_column('hashed_password', $password_hash, $selected_user->get_id());
            }
            $update_string = '[' . substr($update_string, 0, -1) . ']';
            setup_redirect($redirect_url, 'true', 'User ID:' . $selected_user->get_id() . ' Updated Successfully Data Updated: ' . $update_string);
        }
        else
        {
            setup_redirect($redirect_url, 'false', 'User Not Found');
        }
    }
}
/* update user end */

/* set calendar as used */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['calendar_used_id']) && !empty($_POST['calendar_used_id']))
    {
        global $pdo;
        $calendar_service = new CalendarService($pdo);
        $success = True;

        $cal_used_id = test_input($_POST['calendar_used_id']);
        // remove all used calendars
        $remove_all_used = $calendar_service->update_columns_where('used', 1, 0);
        if (!$remove_all_used)
        {
            $success = False;
        }
        if ($success == True)
        {
            $update_cal = $calendar_service->update_one_column('used', 1, $cal_used_id);
            if (!$update_cal)
            {
                $success = False;
            }
        }
        if ($success)
        {
            setup_redirect($redirect_url, 'true', 'Calendar With ID:' . $cal_used_id . ' Marked As Used');
        }
        else
        {
            setup_redirect($redirect_url, 'false', 'Calendar Could not updated try refresh the page');
        }

    }
}

/* set calendar as used  */

/* set calendar as used */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['calendar_userid_edit']) && !empty($_POST['calendar_userid_edit']))
    {
        $cal_edit_id = test_input($_POST['calendar_userid_edit']);
        $success = True;
        $reason = '';
        $update_string = '';

        global $pdo;
        $calendar_service = new CalendarService($pdo);

        $get_calendar = $calendar_service->get_calendar_by_id($cal_edit_id);

        if (!$cal_edit_id)
        {
            setup_redirect($redirect_url, 'false', 'Calendar With ID:' . $cal_edit_id . ' Is Not Found');
        }

        if (isset($_POST['calendar_title_edit']) && !empty($_POST['calendar_title_edit']))
        {

            $title = test_input($_POST['calendar_title_edit']);
            if ($get_calendar->get_title() != $title)
            {
                $update_cal = $calendar_service->update_one_column('title', $title, $cal_edit_id);
                if (!$update_cal)
                {
                    $success = False;
                    $reason = 'Could not update the title';
                }
                else
                {
                    $update_string .= 'title,';
                }
            }

        }
        if (isset($_POST['calendar_description_edit']) && !empty($_POST['calendar_description_edit']))
        {

            $description = test_input($_POST['calendar_description_edit']);
            if ($get_calendar->get_description() != $description)
            {
                $update_cal = $calendar_service->update_one_column('description', $description, $cal_edit_id);
                if (!$update_cal)
                {
                    $success = False;
                    $reason = 'Could not update the description';
                }
                else
                {
                    $update_string .= 'description,';
                }
            }
        }
        if (isset($_FILES) && !empty($_FILES) && isset($_FILES['background_image_edit']) && !empty($_FILES['background_image_edit']))
        {
            $image_edit = $_FILES['background_image_edit']['size'] > 0 ? True : False;
            if ($image_edit)
            {
                $target_dir = "../uploads/images/";
                $replaceImage = upload_image($_FILES, $target_dir, 'background_image_edit', array(
                    "jpg",
                    "png",
                    "jpeg",
                    "gif"
                ) , 500000, 'image', $cal_edit_id);

                if (!$replaceImage)
                {
                    $success = False;
                    $reason = 'Could not update the background';
                }
                else
                {
                    $update_cal = $calendar_service->update_one_column('background_image', $replaceImage['image'], $cal_edit_id);
                    if (!$update_cal)
                    {
                        $success = False;
                        $reason = 'Could not save new background URL';
                    }
                    else
                    {
                        $update_string .= 'background_image,';
                    }
                }
            }
        }

        if ($success == True)
        {
            $update_string = '[' . substr($update_string, 0, -1) . ']';
            setup_redirect($redirect_url, 'true', 'Update Calendar With ID:' . $cal_edit_id . 'Changed: ' . $update_string);
        }
        else
        {
            setup_redirect($redirect_url, 'false', $reason);
        }

    }
}
/* Edit calendar end */

/* add new years to calendar */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

    if (isset($_POST['add_new_year_edit']) && !empty($_POST['add_new_year_edit']) && isset($_POST['years_added_calid']) && !empty($_POST['years_added_calid']))
    {
        global $pdo;
        $calendar_service = new CalendarService($pdo);

        $added_years = intval(test_input($_POST['add_new_year_edit']));
        $year_string = $added_years == 1 ? ' year ' : ' years ';
        $calid = intval(test_input($_POST['years_added_calid']));

        $cal_data = $calendar_service->get_calendar_by_id($calid);

        if (!$cal_data || empty($cal_data))
        {
            setup_redirect($redirect_url, 'false', 'Calendar Not Found Please Refresh the Page.');
        }

        $periods_sql = "SELECT DISTINCT period.description, period.period_date FROM calendar
    JOIN year ON calendar.id = year.cal_id JOIN month ON month.year_id =
    year.id JOIN day ON day.month_id = month.id JOIN period ON day.id =
    period.day_id JOIN slot ON period.id = slot.period_id WHERE
    calendar.id=" . $cal_data->get_id();
        $periods_data_rows = $calendar_service->free_group_query($periods_sql);
        $periods_data = array();
        for ($p = 0;$p < count($periods_data_rows);$p++)
        {
            array_push($periods_data, array(
                'description' => $periods_data_rows[$p]['description'],
                'period_date' => $periods_data_rows[$p]['period_date']
            ));
        }

        $slots_sql = "SELECT DISTINCT slot.start_from, slot.end_at FROM calendar
    JOIN year ON calendar.id = year.cal_id JOIN month ON month.year_id =
    year.id JOIN day ON day.month_id = month.id JOIN period ON day.id =
    period.day_id JOIN slot ON period.id = slot.period_id WHERE
    calendar.id=" . $cal_data->get_id();

        $slots_data_rows = $calendar_service->free_group_query($slots_sql);
        $slots_data = array();

        for ($s = 0;$s < count($slots_data_rows);$s++)
        {
            array_push($slots_data, array(
                'start_from' => $slots_data_rows[$s]['start_from'],
                'end_at' => $slots_data_rows[$s]['end_at']
            ));
        }
        create_calendar($cal_data->get_id() , $cal_data->get_title() , intval($cal_data->get_start_year()) + 1, $added_years, $cal_data->get_periods_per_day() , $cal_data->get_slots_per_period() , $cal_data->get_description() , $periods_data, $slots_data);
        $new_added_years = intval($cal_data->get_added_years()) + $added_years;
        $calendar_service->update_one_column('added_years', $new_added_years, $calid);
        setup_redirect($redirect_url, 'true', 'Successfully Add : ' . $added_years . $year_string . ' To calendar With ID: ' . $cal_data->get_id());

    }
}

/* Load periods and slots ajax !every period and slot stand alone class */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['ajax_calid_editcal']) && !empty($_POST['ajax_calid_editcal']))
    {
        global $pdo;
        $calendar_service = new CalendarService($pdo);
        $period_service = new PeriodService($pdo);
        $slot_service = new SlotService($pdo);
        $style_service = new StyleService($pdo);
        $cal_id = intval(test_input($_POST['ajax_calid_editcal']));
        $json_data = array();
        $slots_data = array();
        $periods_data = array();

        /* get periods by calendar id */

        $distinct_periods_rows = $period_service->get_distinct_periods($cal_id);

        ////free_group_query
        //////*/

          $periods_data = array();
          for ($s=0; $s<count($distinct_periods_rows); $s++){
            $sql = "SELECT period.id, period.period_index, period.period_date, period.description, period.element_id, period.element_class
            FROM period JOIN day ON period.day_id = day.id JOIN month ON day.month_id=month.id JOIN year ON month.year_id=year.id JOIN
            calendar ON year.cal_id=calendar.id WHERE calendar.id=" . $cal_id . ' AND period.period_index=' .$distinct_periods_rows[$s]['period_index'] . ' LIMIT 1';
            $row_data = $calendar_service->free_group_query($sql);
            if (count($row_data) > 0){
              array_push($periods_data,array(
                'id'=> $row_data[0]['id'],
                'period_index'=> $row_data[0]['period_index'],
                'period_date'=> $row_data[0]['period_date'],
                'description'=> $row_data[0]['description'],
                'element_id'=> $row_data[0]['element_id'],
                'element_class'=> $row_data[0]['element_class']
              )
              );
            }
          }



        $periods_fulldata = array();
        for ($p=0; $p<count($periods_data); $p++){
          $p_id = $periods_data[$p]['id'];
          $p_period_index = $periods_data[$p]['period_index'];
          $p_period_date = $periods_data[$p]['period_date'];
          $p_description = $periods_data[$p]['description'];
          $p_element_id = $periods_data[$p]['element_id'];
          $p_element_class = $periods_data[$p]['element_class'];
          $period_row_data = array(
            'id' => $p_id,
            'element_class' => $p_element_class,
            'period_index' => $p_period_index,
            'period_date' => $p_period_date,
            'description' => $p_description,
            'element_id' => $p_element_id,
            'main_styles' => array(),
            'custom_styles' => array()
          );

          $periods_titles = "SELECT DISTINCT style.title FROM style WHERE classname='".$p_element_class."' AND cal_id=".$cal_id;
          $style_titles_rows = $calendar_service->free_group_query($periods_titles);

          for($title_index=0; $title_index<count($style_titles_rows); $title_index++){
            $single_meta = "SELECT * FROM style WHERE title='".$style_titles_rows[$title_index]['title']."' AND cal_id=".$cal_id." LIMIT 1";
            $style_row = $calendar_service->free_group_query($single_meta);
            if (!empty($style_row)){
              $single_row_fromgroup = $style_row[0];
              $current_custom = $style_row[0]['custom'];

              if ($current_custom){
                array_push($period_row_data['custom_styles'], $single_row_fromgroup);
              } else {
                array_push($period_row_data['main_styles'], $single_row_fromgroup);
              }

            }
          }

          array_push($periods_fulldata, $period_row_data);
        }


        /* end get periods  */

        /* Get Slots data using calendar id */
        $distinct_slots_rows = $slot_service->get_distinct_slots($cal_id);

        for ($s1=0; $s1<count($distinct_slots_rows); $s1++){
          $sql = "SELECT slot.id, slot.slot_index, slot.start_from, slot.end_at, slot.element_id, slot.element_class
          FROM slot JOIN period ON slot.period_id = period.id JOIN day ON period.day_id = day.id JOIN month ON day.month_id=month.id JOIN year ON month.year_id=year.id JOIN
          calendar ON year.cal_id=calendar.id WHERE calendar.id=" . $cal_id . ' AND slot.slot_index=' .$distinct_slots_rows[$s1]['slot_index'] . ' LIMIT 1';
          $row_data = $calendar_service->free_group_query($sql);
          if (count($row_data) > 0){
            array_push($slots_data,array(
              'id'=> $row_data[0]['id'],
              'slot_index'=> $row_data[0]['slot_index'],
              'start_from'=> $row_data[0]['start_from'],
              'end_at'=> $row_data[0]['end_at'],
              'element_id'=> $row_data[0]['element_id'],
              'element_class'=> $row_data[0]['element_class']
            )
            );
          }
        };

        /* styles slot */
        $slots_fulldata = array();
        for ($s=0; $s<count($slots_data); $s++){
          $s_id = $slots_data[$s]['id'];
          $s_slot_index = $slots_data[$s]['slot_index'];
          $s_start_from = $slots_data[$s]['start_from'];
          $s_end_at = $slots_data[$s]['end_at'];
          $s_element_id = $slots_data[$s]['element_id'];
          $s_element_class = $slots_data[$s]['element_class'];
          $slot_row_data = array(
            'id' => $s_id,
            'slot_index' => $s_slot_index,
            'start_from' => $s_start_from,
            'end_at' => $s_end_at,
            'element_id' => $s_element_id,
            'element_class' => $s_element_class,
            'main_styles' => array(),
            'custom_styles' => array()
          );

          $slots_titles = "SELECT DISTINCT style.title FROM style WHERE classname='".$s_element_class."' AND cal_id=".$cal_id;
          $slots_style_titles = $calendar_service->free_group_query($slots_titles);

          for($title_index=0; $title_index<count($slots_style_titles); $title_index++){
            $single_meta = "SELECT * FROM style WHERE title='".$slots_style_titles[$title_index]['title']."' AND cal_id=".$cal_id." LIMIT 1";
            $style_row = $calendar_service->free_group_query($single_meta);

            if (count($style_row)> 0){
              $single_row_fromgroup = $style_row[0];
              $current_custom = $style_row[0]['custom'];

              if ($current_custom){
                array_push($slot_row_data['custom_styles'], $style_row[0]);
              } else {
                array_push($slot_row_data['main_styles'],$style_row[0]);
              }
            }
          }
         array_push($slots_fulldata, $slot_row_data);
        }
        /* end styles slot */


        /* end slot step */

        $json_data = array(
            'code' => 200,
            'cal_id' => $cal_id,
            'period_data' => $periods_fulldata,
            'slot_data' => $slots_fulldata,
            'total_periods' => count($periods_data) ,
            'total_slots' => count($slots_data)
        );
        print_r(json_encode($json_data));
        die();
    }
}

/* end add new years */

//period_date_edit  period_description_edit


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (

    isset($_POST['period_calid_edit']) && !empty($_POST['period_calid_edit']) && isset($_POST['period_index_edit']) && !empty($_POST['period_index_edit']) && isset($_POST['period_date_edit']) && !empty($_POST['period_date_edit']) && isset($_POST['period_description_edit']) && !empty($_POST['period_description_edit']))
    {
        global $pdo;
        $period_service = new PeriodService($pdo);
        $calendar_service = new CalendarService($pdo);

        $cal_id = test_input($_POST['period_calid_edit']);
        $req_period_index = test_input($_POST['period_index_edit']);
        $req_period_description = test_input($_POST['period_description_edit']);
        $req_period_period_date = test_input($_POST['period_date_edit']);

        $orginal_description = $req_period_description;
        $orginal_date = $req_period_period_date;

        $server_response = '';

        // request data valdation
        $calendar = $calendar_service->get_calendar_by_id($cal_id);
        if (!$calendar || empty($calendar))
        {
            setup_redirect($redirect_url, 'false', 'calendar not found or deleted can not update');
        }


        // this way make benfit of best perofrmance as it not make alot of actions for no changes and
        // everything is standalone object
        $unique_periods_rows = $period_service->get_distinct_periods($cal_id);
        $periods_data = array();
        if (!empty($unique_periods_rows))
        {
            $periods_data = $period_service->get_distinct_periods_data($unique_periods_rows);
        }

        for ($s = 0;$s < count($periods_data);$s++)
        {
            if ($periods_data[$s]['period_index'] == $req_period_index)
            {
                $orginal_date = $periods_data[$s]['period_date'];

                $orginal_description = $periods_data[$s]['description'];
            }
        }
        //echo $orginal_date;
        //die();
        //echo $req_period_period_date;
        // /echo $orginal_date;
        $original_time = date('H:i', strtotime($orginal_date));
        $request_time = $req_period_period_date;
        $period_date_changed = $original_time != $request_time;
        $description_changed = $orginal_description != $req_period_description;

        $req_date_new = date('Y-m-d', strtotime($orginal_date)) . ' ' . $request_time;



        $periods_sql = "SELECT period.id FROM period JOIN day ON period.day_id=day.id JOIN
      month ON day.month_id = month.id JOIN year ON month.year_id = year.id JOIN calendar ON year.cal_id = calendar.id WHERE
      calendar.id=" . $cal_id . " AND period.period_index=" . $req_period_index . " AND period.description ='" . $orginal_description . "'";

        $periods_data_rows = $calendar_service->free_group_query($periods_sql);

        //update periods description
        if ($description_changed)
        {
            $total_changed = 0;

            if (!empty($periods_data_rows))
            {
                for ($p = 0;$p < count($periods_data_rows);$p++)
                {
                    $update_column = $period_service->update_one_column('description', $req_period_description, $periods_data_rows[$p]['id']);
                    $total_changed += $update_column ? 1 : 0;
                }
                $server_response .= " " . $total_changed . " period Description";
            }
        }

        //update periods date
        if ($period_date_changed)
        {
            $total_changed = 0;
            if (!empty($periods_data_rows))
            {
                for ($p = 0;$p < count($periods_data_rows);$p++)
                {
                    $update_column = $period_service->update_one_column('period_date', $req_date_new, $periods_data_rows[$p]['id']);
                    $total_changed += $update_column ? 1 : 0;

                }
                $server_response .= " " . $total_changed . " period DateTime";
            }
        }


        $success = $period_date_changed || $description_changed ? 'true' : 'false';
        $response = $success == 'true' ? 'Successfully Update: ' . $server_response : 'No changes were detected';
        setup_redirect($redirect_url, $success, $response);
    }
}

/* Update Periods and slots main data */

/* update slots */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (

    isset($_POST['slot_calid_edit']) && !empty($_POST['slot_calid_edit']) && isset($_POST['slot_index_edit']) && !empty($_POST['slot_index_edit']) && isset($_POST['start_from_edit']) && !empty($_POST['start_from_edit']) && isset($_POST['end_at_edit']) && !empty($_POST['end_at_edit']))
    {
        global $pdo;
        $slot_service = new SlotService($pdo);
        $calendar_service = new CalendarService($pdo);

        $cal_id = test_input($_POST['slot_calid_edit']);
        $req_slot_index = test_input($_POST['slot_index_edit']);
        $req_start_from = test_input($_POST['start_from_edit']);
        $req_end_at = test_input($_POST['end_at_edit']);

        $orginal_start_from = $req_start_from;
        $orginal_end_at = $req_end_at;

        $server_response = '';

        // request data valdation
        $calendar = $calendar_service->get_calendar_by_id($cal_id);
        if (!$calendar || empty($calendar))
        {
            setup_redirect($redirect_url, 'false', 'calendar not found or deleted can not update');
        }

        // get DISTINCT slot rows
        $unique_slots_rows = $slot_service->get_distinct_slots($cal_id);
        $slots_data = array();
        if (!empty($unique_slots_rows))
        {
            $slots_data = $slot_service->get_distinct_slots_data($unique_slots_rows);
        }

        for ($s = 0;$s < count($slots_data);$s++)
        {
            if ($slots_data[$s]['slot_index'] == $req_slot_index)
            {
                $orginal_start_from = $slots_data[$s]['start_from'];
                $orginal_end_at = $slots_data[$s]['end_at'];
                break;
            }
        }
        $start_from_changed = $orginal_start_from != $req_start_from;
        $end_at_date_changed = $orginal_end_at != $req_end_at;

        $slots_sql = "SELECT slot.id FROM slot JOIN period ON slot.period_id = period.id JOIN day ON period.day_id = day.id JOIN
      month ON day.month_id = month.id JOIN year ON month.year_Id = year.id JOIN calendar ON year.cal_id = calendar.id WHERE
      calendar.id=" . $cal_id . " AND slot.slot_index=" . $req_slot_index . " AND slot.start_from ='" . $orginal_start_from . "'";
        $slots_data_rows = $calendar_service->free_group_query($slots_sql);

        // edit what changed only
        if ($start_from_changed)
        {

            if (!empty($slots_data_rows))
            {
                for ($s = 0;$s < count($slots_data_rows);$s++)
                {
                    $update_column = $slot_service->update_one_column('start_from', strval($req_start_from) , $slots_data_rows[$s]['id']);
                    $total_changed += $update_column ? 1 : 0;
                }
                $server_response .= " " . $total_changed . " Slot start_from";
            }
        }

        if ($end_at_date_changed)
        {
            if (!empty($slots_data_rows))
            {
                for ($s = 0;$s < count($slots_data_rows);$s++)
                {
                    $update_column = $slot_service->update_one_column('end_at', $req_end_at, $slots_data_rows[$s]['id']);
                    $total_changed += $update_column ? 1 : 0;
                }
                $server_response .= " " . $total_changed . " Slot end_at";
            }
        }
        $success = $start_from_changed || $end_at_date_changed ? 'true' : 'false';
        $response = $success == 'true' ? 'Successfully Update: ' . $server_response : 'No changes were detected';
        setup_redirect($redirect_url, $success, $response);
    }

}

/* end update slot */

/* Add New Periods added_periods add_periods_cal_id */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (

    isset($_POST['add_periods_cal_id']) && !empty($_POST['add_periods_cal_id']) && isset($_POST['added_periods']) && !empty($_POST['added_periods']))
    {
        global $pdo;
        $calendar_service = new CalendarService($pdo);
        $period_service = new PeriodService($pdo);
        $style_service = new StyleService($pdo);

        $cal_id = intval(test_input($_POST['add_periods_cal_id']));
        $added_periods = intval(test_input($_POST['added_periods']));
        $slots_data = array();
        $periods_data = array();

        /* get periods by calendar id */
        $calendar = $calendar_service->get_calendar_by_id($cal_id);
        $distinct_periods_rows = $period_service->get_distinct_periods($cal_id);
        //$row_data = $period_service->get_periods_where('period_index', intval($distinct_periods_rows[1]), 1);
        if (!empty($distinct_periods_rows))
        {
            $periods_data = $period_service->get_distinct_periods_data($distinct_periods_rows);
        }

        $current_periods = $calendar->get_periods_per_day();
        $calendar_days_sql = "SELECT day.id, day.day_date FROM day JOIN month ON day.month_id=month.id JOIN year
      ON month.year_id=year.id JOIN calendar ON year.cal_id=calendar.id WHERE calendar.id=" . $cal_id;
        $calendar_days = $calendar_service->free_group_query($calendar_days_sql);

        $total_periods_new = count($periods_data) + $added_periods;
        $start_index = count($periods_data) + 1;

        // add periods for all days
        $periods_db_data = array();
        $total_addedperiods = 0;
        $index_element_id = 1;
        for ($day = 0;$day < count($calendar_days);$day++)
        {
            $day_id = $calendar_days[$day]['id'];
            $day_date = $calendar_days[$day]['day_date'];
            $start_index = count($periods_data) + 1;
            for ($start_index;$start_index <= $total_periods_new;$start_index++)
            {

                $dateInput = 'period_date_' . ($start_index);
                $descInput = 'period_description_' . ($start_index);

                $period_element_id = 'period_id_'.$index_element_id;
                $period_index_classname = 'period_class_'.$start_index;
                $index_element_id += 1;


                $dateInput = isset($_POST[$dateInput]) && !empty($_POST[$dateInput]) ? $day_date . ' ' . test_input($_POST[$dateInput]) : '';
                $descInput = isset($_POST[$descInput]) && !empty($_POST[$descInput]) ? test_input($_POST[$descInput]) : '';

                /* Get Styles for periods */

                $colort = 'period_color_' . $start_index;
                $pcolor = isset($_POST[$colort]) && !empty($_POST[$colort]) ? test_input($_POST[$colort]) : False;

                $backgroundt = 'period_background_' . $start_index;
                $pbackground = isset($_POST[$backgroundt]) && !empty($_POST[$backgroundt]) ? test_input($_POST[$backgroundt]) : False;

                $fontfamilyt = 'period_fontfamily_' . $start_index;
                $pfontfamily = isset($_POST[$fontfamilyt]) && !empty($_POST[$fontfamilyt]) && $style_service->is_valid_css($_POST[$fontfamilyt]) ? test_input($_POST[$fontfamilyt]) : False;

                $fontsizet = 'period_fontsize_' . $start_index;
                $pfontsize = isset($_POST[$fontsizet]) && !empty($_POST[$fontsizet]) && $style_service->is_valid_css($_POST[$fontsizet]) ? test_input($_POST[$fontsizet]) : False;

                $bordersizet = 'period_border_size_' . $start_index;
                $pborder_size = isset($_POST[$bordersizet]) && !empty($_POST[$bordersizet]) ? test_input($_POST[$bordersizet]) : False;

                $bordertypet = 'period_border_type_' . $start_index;
                $pborder_type = isset($_POST[$bordertypet]) && !empty($_POST[$bordertypet]) ? test_input($_POST[$bordertypet]) : False;

                $bordercolort = 'period_border_color_' . $start_index;
                $pborder_color = isset($_POST[$bordercolort]) && !empty($_POST[$bordercolort]) ? test_input($_POST[$bordercolort]) : False;

                $customcsst = 'period_customcss_' . $start_index;
                $period_customcss = isset($_POST[$customcsst]) && !empty($_POST[$customcsst]) ? test_input($_POST[$customcsst]) : False;


                /* Border */
                $period_border = '';
                if ($pborder_size && $pborder_type && $pborder_color)
                {
                    $period_border_result = $pborder_size . " " . $pborder_type . " " . $pborder_color;
                    $period_border = $style_service->is_valid_css($period_border_result) ? $period_border_result : False;
                }

                $period_color = '';
                if ($pcolor)
                {
                    $period_color_result = 'color: ' . $pcolor . ';';
                    $period_color = $style_service->is_valid_css($period_color_result) ? $period_color_result : False;
                }

                $period_background = '';
                if ($pbackground)
                {
                    $period_background_result = 'background-color: ' . $pbackground . ';';
                    $period_background = $style_service->is_valid_css($period_background_result) ? $period_background_result : False;
                }

                $customcss_result = $style_service->get_advanced_style_data(explode('|', $period_customcss));
                $customcss_data = $customcss_result && isset($customcss_result) && !empty($customcss_result) ? $customcss_result : array();

                /* add period */
                $period_id = $period_service->add(intval($day_id) , $dateInput, $descInput, $start_index, $period_element_id, $period_index_classname);
                $total_addedperiods += 1;

                // insert styles
                if ($period_color && isset($period_color) && !empty($period_color))
                {
                    $style_service->add($period_index_classname, $period_element_id, $period_color, $period_id, 1, 'Period Font Color ' . $start_index, 0, $calendar->get_id(), 'color');
                }

                if ($period_background && isset($period_background) && !empty($period_background))
                {
                    $style_service->add($period_index_classname, $period_element_id, $period_background, $period_id, 1, 'Period Background Color ' . $start_index, 0, $calendar->get_id(), 'backgroundcolor');
                }

                if ($pfontsize && isset($pfontsize) && !empty($pfontsize))
                {
                    $style_service->add($period_index_classname, $period_element_id, $pfontsize, $period_id, 1, 'Period Font Size ' . $start_index, 0, $calendar->get_id(), 'fontsize');
                }

                if ($pfontfamily && isset($pfontfamily) && !empty($pfontfamily))
                {
                    $style_service->add($period_index_classname, $period_element_id, $pfontfamily, $period_id, 1, 'Period Font Family ' . $start_index, 0, $calendar->get_id(), 'fontfamily');
                }
                if ($period_border && isset($period_border) && !empty($period_border))
                {
                    $style_service->add($period_index_classname, $period_element_id, $period_border, $period_id, 1, 'Period Border ' .$start_index, 0, $calendar->get_id(), 'border');
                }

                if ($customcss_data && isset($customcss_data) && !empty($customcss_data) && count($customcss_data) > 0)
                {
                    for ($cs = 0;$cs < count($customcss_data);$cs++)
                    {
                        if ($customcss_data[$cs] != ''){
                          $custom_title = 'Period Custom: ' . $start_index . ', ' . ($cs + 1);
                          echo $style_service->add($period_index_classname, $period_element_id, $customcss_data[$cs], $period_id, 1, $custom_title, 1, $calendar->get_id(), 'custom');
                        }

                    }
                }
                /* end insert styles periods */



            }
        }

        $calendar_service->update_one_column('periods_per_day', $total_periods_new, $cal_id);
        setup_redirect($redirect_url, 'true', 'Successfully Add: ' . $total_addedperiods . ' periods to calendar with ID:' . $cal_id);
    }
}
/* Add new Periods End */

/* Add New Slot added_periods add_periods_cal_id */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (

    isset($_POST['add_slots_cal_id']) && !empty($_POST['add_slots_cal_id']) && isset($_POST['added_slots']) && !empty($_POST['added_slots']))
    {
        global $pdo;
        $slot_service = new SlotService($pdo);
        $calendar_service = new CalendarService($pdo);
        $style_service = new StyleService($pdo);
        $cal_id = test_input($_POST['add_slots_cal_id']);
        $added_slots = test_input($_POST['added_slots']);
        $slots_data = array();

        if (!is_numeric($added_slots) || !is_numeric($cal_id))
        {
            setup_redirect($redirect_url, 'false', 'Invalid period data, period cannot be added');
        }

        $calendar = $calendar_service->get_calendar_by_id($cal_id);

        $selected_calendar = $calendar_service->get_calendar_by_id($cal_id);
        if (!$selected_calendar)
        {
            setup_redirect($redirect_url, 'false', 'Slot is not found or delete please refresh the page if problem not solve contact us');
        }

        $added_slots = intval($added_slots);

        $current_total_slots = intval($selected_calendar->get_slots_per_period());
        $new_total_slots = $current_total_slots + $added_slots;

        $distinct_slots_rows = $slot_service->get_distinct_slots($cal_id);
        //$row_data = $period_service->get_periods_where('period_index', intval($distinct_periods_rows[1]), 1);
        if (!empty($distinct_slots_rows))
        {
            $slots_data = $slot_service->get_distinct_slots_data($distinct_slots_rows);
        }

        $current_slots = $calendar->get_slots_per_period();
        $calendar_slots_sql = "SELECT period.id FROM period JOIN day ON period.day_id = day.id JOIN month ON day.month_id=month.id JOIN year
      ON month.year_id=year.id JOIN calendar ON year.cal_id=calendar.id WHERE calendar.id=" . $cal_id;
        $calendar_periods = $calendar_service->free_group_query($calendar_slots_sql);

        $total_slots_new = count($slots_data) + $added_slots;

        // add Slots for all days (this mutible to mutible add)
        $total_addedslots = 0;

        $element_id_index = count($calendar_periods)+1;
        for ($slot = 0;$slot < count($calendar_periods);$slot++)
        {
            $period_id = $calendar_periods[$slot]['id'];

            $start_index = count($slots_data) + 1;
            for ($start_index;$start_index <= $total_slots_new;$start_index++)
            {
                $startFromInputString = 'start_at_slot_' . ($start_index);
                $endAtInputString = 'end_at_slot_' . ($start_index);

                $element_class = 'slot_class_'.$start_index;
                $element_id = 'slot_id_'.$element_id_index;
                $element_id_index += 1;

                $startFromInput = isset($_POST[$startFromInputString]) && !empty($_POST[$startFromInputString]) ? test_input($_POST[$startFromInputString]) : NULL;
                $endAtInput = isset($_POST[$endAtInputString]) && !empty($_POST[$endAtInputString]) ? test_input($_POST[$endAtInputString]) : NULL;
                //$slot_service->add($startFromInput, $endAtInput, $period_id, 1, $start_index);
                /* Get Styles for periods */

                $colort = 'slot_color_' . $start_index;
                $scolor = isset($_POST[$colort]) && !empty($_POST[$colort]) ? test_input($_POST[$colort]) : False;

                $backgroundt = 'slot_background_' . $start_index;
                $sbackground = isset($_POST[$backgroundt]) && !empty($_POST[$backgroundt]) ? test_input($_POST[$backgroundt]) : False;

                $fontfamilyt = 'slot_fontfamily_' . $start_index;
                $sfontfamily = isset($_POST[$fontfamilyt]) && !empty($_POST[$fontfamilyt]) && $style_service->is_valid_css($_POST[$fontfamilyt]) ? test_input($_POST[$fontfamilyt]) : False;

                $fontsizet = 'slot_fontsize_' . $start_index;
                $sfontsize = isset($_POST[$fontsizet]) && !empty($_POST[$fontsizet]) && $style_service->is_valid_css($_POST[$fontsizet]) ? test_input($_POST[$fontsizet]) : False;

                $bordersizet = 'slot_border_size_' . $start_index;
                $sborder_size = isset($_POST[$bordersizet]) && !empty($_POST[$bordersizet]) ? test_input($_POST[$bordersizet]) : False;

                $bordertypet = 'slot_border_type_' . $start_index;
                $sborder_type = isset($_POST[$bordertypet]) && !empty($_POST[$bordertypet]) ? test_input($_POST[$bordertypet]) : False;

                $bordercolort = 'slot_border_color_' . $start_index;
                $sborder_color = isset($_POST[$bordercolort]) && !empty($_POST[$bordercolort]) ? test_input($_POST[$bordercolort]) : False;

                $customcsst = 'slot_customcss_' . $start_index;
                $slot_customcss = isset($_POST[$customcsst]) && !empty($_POST[$customcsst]) ? test_input($_POST[$customcsst]) : False;


                /* Border */
                $slot_border = '';
                if ($sborder_size && $sborder_type && $sborder_color)
                {
                    $slot_border_result = $sborder_size . " " . $sborder_type . " " . $sborder_color;
                    $slot_border = $style_service->is_valid_css($slot_border_result) ? $slot_border_result : False;
                }

                $slot_color = '';
                if ($scolor)
                {
                    $slot_color_result = 'color: ' . $scolor . ';';
                    $slot_color = $style_service->is_valid_css($slot_color_result) ? $slot_color_result : False;
                }

                $slot_background = '';
                if ($sbackground)
                {
                    $slot_background_result = 'background-color: ' . $sbackground . ';';
                    $slot_background = $style_service->is_valid_css($slot_background_result) ? $slot_background_result : False;
                }

                $customcss_result = $style_service->get_advanced_style_data(explode('|', $slot_customcss));
                $customcss_data = $customcss_result && isset($customcss_result) && !empty($customcss_result) ? $customcss_result : array();


                // add the slot
                $slot_id = $slot_service->add($startFromInput, $endAtInput, $period_id, 1, $start_index, $element_id, $element_class);
                $total_addedslots += 1;

                // insert styles
                if ($slot_color && isset($slot_color) && !empty($slot_color))
                {
                    $style_service->add($element_class, $element_id, $slot_color, $slot_id, 1, 'Slot Font Color ' . $start_index, 0, $calendar->get_id(), 'color');
                }

                if ($slot_background && isset($slot_background) && !empty($slot_background))
                {
                    $style_service->add($element_class, $element_id, $slot_background, $slot_id, 1, 'Slot Background Color ' . $start_index, 0, $calendar->get_id(), 'backgroundcolor');
                }

                if ($sfontsize && isset($sfontsize) && !empty($sfontsize))
                {
                    $style_service->add($element_class, $element_id, $sfontsize, $slot_id, 1, 'Slot Font Size ' . $start_index, 0, $calendar->get_id(), 'fontsize');
                }

                if ($sfontfamily && isset($sfontfamily) && !empty($sfontfamily))
                {
                    $style_service->add($element_class, $element_id, $sfontfamily, $slot_id, 1, 'Slot Font Family ' . $start_index, 0, $calendar->get_id(), 'fontfamily');
                }
                if ($slot_border && isset($slot_border) && !empty($slot_border))
                {
                    $style_service->add($element_class, $element_id, $slot_border, $slot_id, 1, 'Slot Border ' .$start_index, 0, $calendar->get_id(), 'border');
                }

                if ($customcss_data && isset($customcss_data) && !empty($customcss_data) && count($customcss_data) > 0)
                {
                    for ($cs = 0;$cs < count($customcss_data);$cs++)
                    {
                        if ($customcss_data[$cs] != ''){
                          $custom_title = 'Slot Custom: ' . $start_index . ', ' . ($cs + 1);
                          $style_service->add($element_class, $element_id, $customcss_data[$cs], $slot_id, 1, $custom_title, 1, $calendar->get_id(), 'custom');

                        }
                    }
                }

                /* end  styles slots */

            }
        }
        $calendar_service->update_one_column('slots_per_period', $total_slots_new, $cal_id);
        setup_redirect($redirect_url, 'true', 'Successfully Add: ' . $total_addedslots . ' Slots to calendar with ID:' . $cal_id);

    }
}
/* Add new Slot End */

/* Delete period  */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (

    isset($_POST['period_delete_calid']) && !empty($_POST['period_delete_calid']) && isset($_POST['period_delete_index']) && !empty($_POST['period_delete_index']))
    {
        global $pdo;
        $period_service = new PeriodService($pdo);
        $calendar_service = new CalendarService($pdo);
        $cal_id = test_input($_POST['period_delete_calid']);
        $period_delete_index = test_input($_POST['period_delete_index']);

        $calendar = $calendar_service->get_calendar_by_id($cal_id);
        if (!$calendar)
        {
            setup_redirect($redirect_url, 'false', 'calendar is not found or delete please refresh the page if problem not solve contact us');
        }

        $calendar_total_periods = intval($calendar->get_periods_per_day());

        if ($calendar_total_periods < 1)
        {
            setup_redirect($redirect_url, 'false', 'The selected Calendar With ID: ' . $cal_id . ' Has no periods');
        }

        $periods_data_sql = "SELECT period.id FROM period JOIN day ON period.day_id = day.id JOIN
      month ON day.month_id = month.id JOIN year ON month.year_id = year.id JOIN
      calendar ON year.cal_id = calendar.id WHERE calendar.id=" . $cal_id . " AND period.period_index=" . $period_delete_index;

        $periods_to_delete = $calendar_service->free_group_query($periods_data_sql);

        if (count($periods_to_delete) < 1)
        {
            setup_redirect($redirect_url, 'false', 'No periods Found Please restart the page');
        }

        // delete periods
        $total_removed = 0;
        for ($p = 0;$p < count($periods_to_delete);$p++)
        {
            $period_id = $periods_to_delete[$p]['id'];
            // /$remove_period = $period_service->remove(intval($period_id));
            $delete_period = "DELETE FROM `period` WHERE id=" . $period_id;
            $perioddeleted = $calendar_service->excute_on_db($delete_period);
            $total_removed += $perioddeleted ? 1 : 0;
        }

        $is_all_deleted = $calendar_service->free_group_query($periods_data_sql);
        if (count($is_all_deleted) > 0)
        {
            setup_redirect($redirect_url, 'false', 'Not all periods Deleted unkown error');
        }

        $new_total_period = $calendar_total_periods <= 0 ? 0 : ($calendar_total_periods - 1);

        $calendar_service->update_one_column('periods_per_day', $new_total_period, $cal_id);
        $success = $total_removed > 0 ? 'true' : 'false';
        $message = $total_removed > 0 ? 'Removed Period With Index:' . $period_delete_index . ' Total removed Periods:' . $total_removed : 'No Periods Deleted';
        setup_redirect($redirect_url, $success, $message);

    }
}

/* Delete Slot  */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (

    isset($_POST['slot_delete_calid']) && !empty($_POST['slot_delete_calid']) && isset($_POST['slot_delete_index']) && !empty($_POST['slot_delete_index']))
    {
        global $pdo;
        $slot_service = new SlotService($pdo);
        $calendar_service = new CalendarService($pdo);
        $cal_id = test_input($_POST['slot_delete_calid']);
        $slot_delete_index = test_input($_POST['slot_delete_index']);

        $calendar = $calendar_service->get_calendar_by_id($cal_id);
        if (!$calendar)
        {
            setup_redirect($redirect_url, 'false', 'calendar is not found or delete please refresh the page if problem not solve contact us');
        }

        $calendar_total_slots = intval($calendar->get_slots_per_period());

        if ($calendar_total_slots < 1)
        {
            setup_redirect($redirect_url, 'false', 'The selected Calendar With ID: ' . $cal_id . ' Has no slots.');
        }

        $slots_data_sql = "SELECT slot.id FROM slot JOIN period ON slot.period_id = period.id JOIN
      day ON period.day_id = day.id JOIN month ON day.month_id = month.id JOIN
      year ON month.year_id = year.id JOIN  calendar ON year.cal_id = calendar.id WHERE
      calendar.id=" . $cal_id . " AND slot.slot_index=" . $slot_delete_index;

        $slots_to_delete = $calendar_service->free_group_query($slots_data_sql);

        if (count($slots_to_delete) < 1)
        {
            setup_redirect($redirect_url, 'false', 'No slots Found Please restart the page');
        }

        // delete slots
        $total_removed = 0;
        for ($s = 0;$s < count($slots_to_delete);$s++)
        {
            $slot_id = $slots_to_delete[$s]['id'];
            $delete_slot = "DELETE FROM `slot` WHERE id=" . $slot_id;
            $slotdeleted = $calendar_service->excute_on_db($delete_slot);
            $total_removed += $slotdeleted ? 1 : 0;
        }

        $is_all_deleted = $calendar_service->free_group_query($slots_data_sql);
        if (count($is_all_deleted) > 0)
        {
            setup_redirect($redirect_url, 'false', 'Not all slots Deleted unkown error');
        }

        $new_total_slots = $calendar_total_slots <= 0 ? 0 : ($calendar_total_slots - 1);

        $calendar_service->update_one_column('slots_per_period', $new_total_slots, $cal_id);
        $success = $total_removed > 0 ? 'true' : 'false';
        $message = $total_removed > 0 ? 'Action On Slot: successfully Removed Slot With Index:' . $slot_delete_index . ' Total removed Slots:' . $total_removed : 'No Slots To deleted Please restart the page';
        setup_redirect($redirect_url, $success, $message);
    }
  }


/* Styles Periods And Slots Mangment */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {

        if (
          isset($_POST['period_cremove_style_calid']) && !empty($_POST['period_cremove_style_calid']) &&
          isset($_POST['period_cremove_style_title']) && !empty($_POST['period_cremove_style_title']) &&
          isset($_POST['period_cremove_style_classname']) && !empty($_POST['period_cremove_style_classname'])
          )
        {
            global $pdo;
            $calendar_service = new CalendarService($pdo);
            $style_service = new StyleService($pdo);

            $cal_id = test_input($_POST['period_cremove_style_calid']);
            $period_style_title = test_input($_POST['period_cremove_style_title']);
            $period_style_classname = test_input($_POST['period_cremove_style_classname']);

            //"DELETE FROM style WHERE title="

            $delete_easy = "DELETE FROM style WHERE title='".$period_style_title."' AND classname='".$period_style_classname."' AND cal_id=".$cal_id;
            $deleted = $calendar_service->excute_on_db($delete_easy);
            $success = $deleted ? 'true' : 'false';
            $message = $deleted ? 'Action On Period: successfully Removed Style Rule With Title:'. $period_style_title : 'Could not remove custom style with title:'.$period_style_title ;
            setup_redirect($redirect_url, $success, $message);
        }
    }
    /* Styles Periods And Slots Mangment end */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {

        if (
          isset($_POST['slot_cremove_style_calid']) && !empty($_POST['slot_cremove_style_calid']) &&
          isset($_POST['slot_cremove_style_title']) && !empty($_POST['slot_cremove_style_title']) &&
          isset($_POST['slot_cremove_style_classname']) && !empty($_POST['slot_cremove_style_classname'])
          )
        {
            global $pdo;
            $calendar_service = new CalendarService($pdo);
            $style_service = new StyleService($pdo);

            $cal_id = test_input($_POST['slot_cremove_style_calid']);
            $slot_style_title = test_input($_POST['slot_cremove_style_title']);
            $slot_style_classname = test_input($_POST['slot_cremove_style_classname']);

            //"DELETE FROM style WHERE title="

            $delete_easy = "DELETE FROM style WHERE title='".$slot_style_title."' AND classname='".$slot_style_classname."' AND cal_id=".$cal_id;
            $deleted = $calendar_service->excute_on_db($delete_easy);
            $success = $deleted ? 'true' : 'false';
            $message = $deleted ? 'Removed Style Rule With Title:'. $slot_style_title : 'Could not remove custom style with title:'.$slot_style_title ;
            setup_redirect($redirect_url, $success, $message);
        }
  }

  /* pause period start */

  /* pause period end */
  if ($_SERVER['REQUEST_METHOD'] == 'POST')
      {

          if (
            isset($_POST['period_cpause_style_active']) &&
            isset($_POST['period_cpause_style_calid']) && !empty($_POST['period_cpause_style_calid']) &&
            isset($_POST['period_cpause_style_title']) && !empty($_POST['period_cpause_style_title']) &&
            isset($_POST['period_cpause_style_classname']) && !empty($_POST['period_cpause_style_classname'])
            )
          {
              global $pdo;
              $calendar_service = new CalendarService($pdo);
              $style_service = new StyleService($pdo);

              $cal_id = test_input($_POST['period_cpause_style_calid']);
              $period_style_title = test_input($_POST['period_cpause_style_title']);
              $period_style_classname = test_input($_POST['period_cpause_style_classname']);
              $next_status = intval(test_input($_POST['period_cpause_style_active']));
              $action = $next_status == 0 ? 'Action On Period: Puased' : 'Action On Period: Enabled';

              $active_pause_period = "UPDATE style SET active=".$next_status." WHERE title='".$period_style_title."' AND classname='".$period_style_classname."' AND cal_id=".$cal_id;
              //die();
              $updated = $calendar_service->excute_on_db($active_pause_period);
              $success = $updated ? 'true' : 'false';
              $message = $updated ? $action .' successfully Style Rule With Title:'. $period_style_title : 'Could not '.$action.' custom style with title:'.$period_style_title ;
              setup_redirect($redirect_url, $success, $message);
          }
}
  /* pause slot start */
  if ($_SERVER['REQUEST_METHOD'] == 'POST')
      {

          if (
            isset($_POST['slot_cpause_style_active']) &&
            isset($_POST['slot_cpause_style_calid']) && !empty($_POST['slot_cpause_style_calid']) &&
            isset($_POST['slot_cpause_style_title']) && !empty($_POST['slot_cpause_style_title']) &&
            isset($_POST['slot_cpause_style_classname']) && !empty($_POST['slot_cpause_style_classname'])
            )
          {
              global $pdo;
              $calendar_service = new CalendarService($pdo);
              $style_service = new StyleService($pdo);

              $cal_id = test_input($_POST['slot_cpause_style_calid']);
              $slot_style_title = test_input($_POST['slot_cpause_style_title']);
              $slot_style_classname = test_input($_POST['slot_cpause_style_classname']);
              $next_status = intval(test_input($_POST['slot_cpause_style_active']));
              $action = $next_status == 0 ? 'Puased' : 'Enabled';

              $active_pause_slot = "UPDATE style SET active=".$next_status." WHERE title='".$slot_style_title."' AND classname='".$slot_style_classname."' AND cal_id=".$cal_id;

              //die();
              $updated = $calendar_service->excute_on_db($active_pause_slot);
              $success = $updated ? 'true' : 'false';
              $message = $updated ? $action .' Style Rule With Title:'. $slot_style_title : 'Could not '.$action.' custom style with title:'.$slot_style_title ;
              setup_redirect($redirect_url, $success, $message);
          }
}
  /* pause slot end */
    /* Styles Periods And Slots Mangment end */

?>
