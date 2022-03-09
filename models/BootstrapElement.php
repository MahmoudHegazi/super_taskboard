<?php
class BootstrapElement
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

    private $flex_fill;
    private $flex_grow;
    private $ms_auto;
    private $flex_order;
    private $vertical_align;
    private $col_sm;
    private $h;
    private $display;
    private $text_wrap;
    private $font_weight;
    private $text_case;
    private $badge;
    private $float_position;
    private $text_align;
    private $text_break;
    private $center_content;

    private $element_id;
    private $last_update;
    private $cal_id;

    function init(
      $element_id, $cal_id, $bg, $text_color, $p, $m, $border, $border_size, $border_color, $border_round, $width, $height,
      $m_t, $m_b, $m_r, $m_l, $p_t, $p_b, $p_r, $p_l, $visibility, $box_shadow, $flex_fill, $flex_grow, $ms_auto,
      $flex_order, $vertical_align, $col_sm, $h, $display, $text_wrap, $font_weight, $text_case, $badge,
      $float_position, $text_align, $text_break, $center_content
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
        $this->flex_fill = $flex_fill;
        $this->flex_grow = $flex_grow;
        $this->ms_auto = $ms_auto;
        $this->flex_order = $flex_order;
        $this->vertical_align = $vertical_align;
        $this->col_sm = $col_sm;
        $this->h = $h;
        $this->display = $display;
        $this->text_wrap = $text_wrap;
        $this->font_weight = $font_weight;
        $this->text_case = $text_case;
        $this->badge = $badge;
        $this->float_position = $float_position;
        $this->text_align = $text_align;
        $this->text_break  = $text_break;
        $this->center_content = $center_content;
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


    function set_flex_flow($flex_flow)
    {
        $this->flex_flow = $flex_flow;
    }
    function get_flex_flow()
    {
        return $this->flex_flow;
    }

    function set_flex_fill($flex_fill)
    {
        $this->flex_fill = $flex_fill;
    }
    function get_flex_fill()
    {
        return $this->flex_fill;
    }

    function set_flex_grow($flex_grow)
    {
        $this->flex_grow = $flex_grow;
    }
    function get_flex_grow()
    {
        return $this->flex_grow;
    }

    function set_ms_auto($ms_auto)
    {
        $this->ms_auto = $ms_auto;
    }
    function get_ms_auto()
    {
        return $this->ms_auto;
    }

    function set_flex_order($flex_order)
    {
        $this->flex_order = $flex_order;
    }
    function get_flex_order()
    {
        return $this->flex_order;
    }

    function set_vertical_align($vertical_align)
    {
        $this->vertical_align = $vertical_align;
    }
    function get_vertical_align()
    {
        return $this->vertical_align;
    }

    function set_col_sm($col_sm)
    {
        $this->col_sm = $col_sm;
    }
    function get_col_sm()
    {
        return $this->col_sm;
    }

    function set_h($h)
    {
        $this->h = $h;
    }
    function get_h()
    {
        return $this->h;
    }

    function set_display($display)
    {
        $this->display = $display;
    }
    function get_display()
    {
        return $this->display;
    }

    function set_text_wrap($text_wrap)
    {
        $this->text_wrap = $text_wrap;
    }
    function get_text_wrap()
    {
        return $this->text_wrap;
    }

    function set_font_weight($font_weight)
    {
        $this->font_weight = $font_weight;
    }
    function get_font_weight()
    {
        return $this->font_weight;
    }

    function get_text_case()
    {
        return $this->text_case;
    }

    function set_text_case($text_case)
    {
        $this->text_case = $text_case;
    }
    function set_badge($badge)
    {
        $this->badge = $badge;
    }

    function get_badge()
    {
        return $this->badge;
    }

    function set_float_position($float_position)
    {
        $this->float_position = $float_position;
    }
    function get_float_position()
    {
        return $this->float_position;
    }

    function set_text_align($text_align)
    {
        $this->text_align = $text_align;
    }
    function get_text_align()
    {
        return $this->text_align;
    }

    function set_text_break($text_break)
    {
        $this->text_break = $text_break;
    }
    function get_text_break()
    {
        return $this->text_break;
    }

    function set_center_content($center_content)
    {
        $this->center_content = $center_content;
    }
    function get_center_content()
    {
        return $this->center_content;
    }


    function set_last_update($last_update)
    {
        $this->last_update = $last_update;
    }
    function get_last_update()
    {
        return $this->last_update;
    }

    function set_element_id($element_id)
    {
        $this->element_id = $element_id;
    }

    function get_element_id()
    {
        return $this->element_id;
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
