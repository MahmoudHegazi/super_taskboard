<?php
class Logs
{

    // Propertiess
    private $id;
    private $user_id;
    private $user_email;
    private $valid;
    private $cookies_enabled;
    private $admin_user;
    private $log_time;

    // secuirty and hacking detector eg (if request token not same like generated and some how user passed if check ) or user logged with diffrent hash and passed the login some how this for tell u but not do thing
    private $form_token;
    private $class_token;
    private $hash_password;

    // user log data if professional focus on secuirty can used with previous to get the hacker if posisible or at least any info this gotten by trusted API for secuirty
    private $ip;
    private $loc;
    private $os_type;
    private $browser_type;
    private $browser_language;

    // this for reset password
    private $completed;

    // this for know if he selected to rememeber me and useful for secuirty check if the cookies I got and id I got the user requested that or not
    private $remember_me;
    private $remember_me_token;


    // cal id add more details anylisis for selected calendar and users who logs on it specicficly
    private $cal_id;
    private $notes;

    function init($user_id, $user_email, $valid, $admin_user, $cal_id, $form_token, $class_token, $hash_password, $cookies_enabled, $ip, $loc, $os_type, $browser_type, $browser_language, $completed, $remember_me, $notes, $remember_me_token)
    {
        $this->user_id = $user_id;
        $this->user_email = $user_email;
        $this->valid = $valid;
        $this->cookies_enabled = $cookies_enabled;
        $this->admin_user = $admin_user;
        $this->form_token = $form_token;
        $this->class_token = $class_token;
        $this->hash_password = $hash_password;
        $this->ip = $ip;
        $this->loc = $loc;
        $this->os_type = $os_type;
        $this->browser_type = $browser_type;
        $this->browser_language = $browser_language;
        $this->completed = $completed;
        $this->remember_me = $remember_me;
        $this->cal_id = $cal_id;
        $this->notes = $notes;
        $this->remember_me_token = $remember_me_token;
    }

    // Setter and geter
    function set_id($id)
    {
        $this->id = $id;
    }
    function get_id()
    {
        return $this->id;
    }


    function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }
    function get_user_id()
    {
        return $this->user_id;
    }


    function set_user_email($user_email)
    {
        $this->user_email = $user_email;
    }
    function get_user_email()
    {
        return $this->user_email;
    }


    function set_valid($valid)
    {
        $this->valid = $valid;
    }
    function get_valid()
    {
        return $this->valid;
    }


    function set_admin_user($admin_user)
    {
        $this->admin_user = $admin_user;
    }
    function get_admin_user()
    {
        return $this->admin_user;
    }


    function set_cookies_enabled($cookies_enabled)
    {
        $this->cookies_enabled = $cookies_enabled;
    }
    function get_cookies_enabled()
    {
        return $this->cookies_enabled;
    }


    function set_log_time($log_time)
    {
        $this->log_time = $log_time;
    }
    function get_log_time()
    {
        return $this->log_time;
    }


    function set_form_token($form_token)
    {
        $this->form_token = $form_token;
    }
    function get_form_token()
    {
        return $this->form_token;
    }


    function set_class_token($class_token)
    {
        $this->class_token = $class_token;
    }
    function get_class_token()
    {
        return $this->class_token;
    }


    function set_hash_password($hash_password)
    {
        $this->hash_password = $hash_password;
    }
    function get_hash_password()
    {
        return $this->hash_password;
    }


    function set_ip($ip)
    {
        $this->ip = $ip;
    }
    function get_ip()
    {
        return $this->ip;
    }


    function set_loc($loc)
    {
        $this->loc = $loc;
    }
    function get_loc()
    {
        return $this->loc;
    }


    function set_os_type($os_type)
    {
        $this->os_type = $os_type;
    }
    function get_os_type()
    {
        return $this->os_type;
    }


    function set_browser_type($browser_type)
    {
        $this->browser_type = $browser_type;
    }
    function get_browser_type()
    {
        return $this->browser_type;
    }


    function set_browser_language($browser_language)
    {
        $this->browser_language = $browser_language;
    }
    function get_browser_language()
    {
        return $this->browser_language;
    }
    function set_remember_me($remember_me)
    {
        $this->remember_me = $remember_me;
    }
    function get_remember_me()
    {
        return $this->remember_me;
    }

    function set_cal_id($cal_id)
    {
        $this->cal_id = $cal_id;
    }
    function get_cal_id()
    {
        return $this->cal_id;
    }

    function set_notes($notes)
    {
        $this->notes = $notes;
    }
    function get_notes()
    {
        return $this->notes;
    }

    function set_completed($completed)
    {
        $this->completed = $completed;
    }
    function get_completed()
    {
        return $this->completed;
    }

    function set_remember_me_token($remember_me_token)
    {
        $this->remember_me_token = $remember_me_token;
    }
    function get_remember_me_token()
    {
        return $this->remember_me_token;
    }




}
?>
