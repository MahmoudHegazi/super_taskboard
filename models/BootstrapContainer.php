<?php
class BootstrapContainer
{

    // Propertiess
    private $id;
    private $bg;
    private $text_color;
    private $p;
    private $m;
    private $border;
    private $border_size;

    // secuirty and hacking detector eg (if request token not same like generated and some how user passed if check ) or user logged with diffrent hash and passed the login some how this for tell u but not do thing
    private $border_color;
    private $border_round;
    private $width;
    private $height;

    // user log data if professional focus on secuirty can used with previous to get the hacker if posisible or at least any info this gotten by trusted API for secuirty
    private $m_t;
    private $m_b;
    private $m_r;
    private $m_l;

    private $p_t;
    private $p_b;
    private $p_r;
    private $p_l;

    // this for reset password
    private $visibility;
    private $box_shadow;
    private $justify_content;
    private $align_items;

    // this for know if he selected to rememeber me and useful for secuirty check if the cookies I got and id I got the user requested that or not
    private $ratio;
    private $flex_flow;


    // cal id add more details anylisis for selected calendar and users who logs on it specicficly
    private $flex_type;
    private $flex_wrap;
    private $align_content;

    private $element_id;
    private $last_update;
    private $cal_id;

    function init(
      $element_id, $cal_id, $bg, $text_color, $p, $m, $border, $border_size, $border_color, $border_round, $width, $height,
      $m_t, $m_b, $m_r, $m_l, $p_t, $p_b, $p_r, $p_l, $visibility, $box_shadow, $justify_content, $align_items,
      $ratio, $flex_flow, $flex_type, $flex_wrap, $align_content
      )
    {
        $this->bg = $bg;
        $this->text_color = $text_color;
        $this->p = $p;
        $this->m = $m;
        $this->border = $border;
        $this->border_size = $border_size;
        $this->border_color = $border_color;
        $this->border_round = $border_round;
        $this->width = $width;
        $this->height = $height;
        $this->m_t = $m_t;
        $this->m_b = $m_b;
        $this->m_r = $m_r;
        $this->m_l = $m_l;
        $this->p_t = $p_t;
        $this->p_b = $p_b;
        $this->p_r = $p_r;
        $this->p_l = $p_l;
        $this->visibility = $visibility;
        $this->box_shadow = $box_shadow;
        $this->justify_content = $justify_content;
        $this->align_items = $align_items;
        $this->ratio = $ratio;
        $this->flex_flow = $flex_flow;
        $this->flex_type = $flex_type;
        $this->flex_wrap = $flex_wrap;
        $this->align_content = $align_content;
        $this->element_id  = $element_id;
        $this->cal_id = $cal_id;
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


    function set_user_id($bg)
    {
        $this->bg = $bg;
    }
    function get_bg()
    {
        return $this->bg;
    }


    function set_text_color($text_color)
    {
        $this->text_color = $text_color;
    }
    function get_text_color()
    {
        return $this->text_color;
    }


    function set_p($p)
    {
        $this->p = $p;
    }
    function get_p()
    {
        return $this->p;
    }


    function set_m($m)
    {
        $this->m = $m;
    }
    function get_m()
    {
        return $this->m;
    }


    function set_border($border)
    {
        $this->border = $border;
    }
    function get_border()
    {
        return $this->border;
    }


    function set_border_size($border_size)
    {
        $this->border_size = $border_size;
    }
    function get_border_size()
    {
        return $this->border_size;
    }

    function set_border_color($border_color)
    {
        $this->border_color = $border_color;
    }
    function get_border_color()
    {
        return $this->border_color;
    }


    function set_border_round($border_round)
    {
        $this->border_round = $border_round;
    }
    function get_border_round()
    {
        return $this->border_round;
    }


    function set_width($width)
    {
        $this->width = $width;
    }
    function get_width()
    {
        return $this->width;
    }


    function set_height($height)
    {
        $this->height = $height;
    }
    function get_height()
    {
        return $this->height;
    }


    function set_m_t($m_t)
    {
        $this->m_t = $m_t;
    }
    function get_m_t()
    {
        return $this->m_t;
    }

    function set_m_b($m_b)
    {
        $this->m_b = $m_b;
    }
    function get_m_b()
    {
        return $this->m_b;
    }


    function set_m_r($m_r)
    {
        $this->m_r = $m_r;
    }
    function get_m_r()
    {
        return $this->m_r;
    }


    function set_m_l($m_l)
    {
        $this->m_l = $m_l;
    }
    function get_m_l()
    {
        return $this->m_l;
    }

    function set_p_t($p_t)
    {
        $this->p_t = $p_t;
    }
    function get_p_t()
    {
        return $this->p_t;
    }

    function set_p_b($p_b)
    {
        $this->p_b = $p_b;
    }
    function get_p_b()
    {
        return $this->p_b;
    }

    function set_banned($p_r)
    {
        $this->p_r = $p_r;
    }
    function get_p_r()
    {
        return $this->p_r;
    }

    function set_p_l($p_l)
    {
        $this->p_l = $p_l;
    }
    function get_p_l()
    {
        return $this->p_l;
    }

    function set_visibility($visibility)
    {
        $this->visibility = $visibility;
    }
    function get_visibility()
    {
        return $this->visibility;
    }

    function set_box_shadow($box_shadow)
    {
        $this->box_shadow = $box_shadow;
    }
    function get_box_shadow()
    {
        return $this->box_shadow;
    }

    function set_justify_content($justify_content)
    {
        $this->justify_content = $justify_content;
    }
    function get_justify_content()
    {
        return $this->justify_content;
    }

    function set_align_items($align_items)
    {
        $this->align_items = $align_items;
    }
    function get_align_items()
    {
        return $this->align_items;
    }

    function set_ratio($ratio)
    {
        $this->ratio = $ratio;
    }
    function get_ratio()
    {
        return $this->ratio;
    }

    function set_flex_flow($flex_flow)
    {
        $this->flex_flow = $flex_flow;
    }
    function get_flex_flow()
    {
        return $this->flex_flow;
    }

    function set_flex_type($flex_type)
    {
        $this->flex_type = $flex_type;
    }
    function get_flex_type()
    {
        return $this->flex_type;
    }

    function set_flex_wrap($flex_wrap)
    {
        $this->flex_wrap = $flex_wrap;
    }
    function get_flex_wrap()
    {
        return $this->flex_wrap;
    }


    function set_align_content($align_content)
    {
        $this->align_content = $align_content;
    }
    function get_align_content()
    {
        return $this->align_content;
    }

    function set_element_id($element_id)
    {
        $this->element_id = $element_id;
    }

    function get_element_id()
    {
        return $this->element_id;
    }

    function set_last_update($last_update)
    {
        $this->last_update = $last_update;
    }

    function get_last_update()
    {
        return $this->last_update;
    }

    function set_cal_id($cal_id)
    {
        $this->cal_id = $cal_id;
    }
    function get_cal_id()
    {
        return $this->cal_id;
    }

}
?>
