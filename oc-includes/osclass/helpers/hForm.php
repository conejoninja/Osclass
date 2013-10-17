<?php

    /*
     *      Osclass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2013 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    function osc_form() {
        if(View::newInstance()->_exists('form')) {
            return View::newInstance()->_get('form');
        }
        return null;
    }

    function osc_form_element() {
        if(View::newInstance()->_exists('form_element')) {
            return View::newInstance()->_get('form_element');
        }
        return null;
    }

    function osc_form_element_field($field, $locale = '') {
        return osc_field(osc_form_element(), $field, $locale);
    }

    function osc_form_field($field) {
        return osc_field(osc_form(), $field, '');
    }

    function osc_form_id() {
        return osc_form_field("id");
    }
    function osc_form_action() {
        return osc_form_field("action");
    }

    function osc_form_method() {
        return osc_form_field("method");
    }

    function osc_form_class() {
        return osc_form_field("class");
    }

    function osc_form_element_class() {
        if(func_num_args()>=3) {
            $args = func_get_args();
            return OSCForm::addElementClass($args[0], $args[1], $args[2]);
        } else {
            return osc_form_element_field("class");
        }
    }

    function osc_form_element_label() {
        return osc_form_element_field("label");
    }

    function osc_form_element_name() {
        return osc_form_element_field("name");
    }

    function osc_form_element_value() {
        return osc_form_element_field("value");
    }

    function osc_form_element_type() {
        return osc_form_element_field("type");
    }

    function osc_form_element_attributes() {
        if(func_num_args()>=3) {
            $args = func_get_args();
            return OSCForm::addElementAttributes($args[0], $args[1], $args[2]);
        } else {
            return osc_form_element_field("attributes");
        }
    }

    function osc_form_has_file() {
        return osc_form_field("has_file");
    }

    function osc_form_element_options() {
        if(func_num_args()>=3) {
            $args = func_get_args();
            return OSCForm::addElementOptions($args[0], $args[1], $args[2]);
        } else {
            return osc_form_element_field("options");
        }
    }

    function osc_form_element_help() {
        return osc_form_element_field("help");
    }

    function osc_has_form_elements() {
        $result = View::newInstance()->_next('form_elements');
        View::newInstance()->_exportVariableToView('form_element', View::newInstance()->_current('form_elements'));
        return $result;
    }

    function osc_reset_form_elements() {
        View::newInstance()->_erase('form_element');
        return View::newInstance()->_reset('form_elements');
    }

    function osc_form_use($id) {
        $form = OSCForm::load($id);
        if($form!==false) {
            View::newInstance()->_exportVariableToView('form_elements', $form['elements']);
            return View::newInstance()->_exportVariableToView('form', $form);
        }
        return false;
    }

    function osc_form_load($id) {
        return OSCForm::load($id);
    }

    function osc_print_form($id) {
        osc_run_hook('print_form', $id);
        osc_form_use($id);
        $class = osc_form_class();
        if($class!="") { $class = ' class="'.$class.'" '; } else { $class = ''; }
        echo '<form id="'.$id.'" name="'.$id.'" action="'.osc_form_action().'" method="'.osc_form_method().'" '.$class.' '.(osc_form_has_file()?'enctype="multipart/form-data"':'').'>';
        while(osc_has_form_elements()) {
            osc_print_form_element();
        }
        echo '</form>';
    }

    function osc_print_form_element($element = null) {
        if($element!=null) { View::newInstance()->_exportVariableToView('form_element', $element); }
        if(osc_form_element_type()=='hidden') {
            echo '<input type="hidden" name="'.osc_esc_html(osc_form_element_name()).'" id="'.osc_esc_html(osc_form_element_name()).'" value="'.osc_esc_html(osc_form_element_value()).'" />';
        } else if(osc_form_element_type()=='html') {
            echo osc_form_element_value();
        } else {
            osc_current_web_theme_path('form-element.php');
        }
    }

    function osc_print_form_field($element = null) {
        if($element!=null) { View::newInstance()->_exportVariableToView('form_element', $element); }
        $attrs = osc_form_element_attributes();
        $common = ' name="'.osc_esc_html(osc_form_element_name()).'"';
        if(!isset($attrs['id'])) { $common .= ' id="'.osc_esc_html(osc_form_element_name()).'"'; }
        $common .= ' class="'.osc_esc_html(osc_form_element_class()).'"';
        $common .= osc_form_attributes_html($attrs).' ';
        switch(osc_form_element_type()){
            case 'reset':
            case 'submit':
            case 'button':
                echo '<button type="'.osc_form_element_type().'" '.$common.'>'.@$attrs['button-text'].'</button>';
                break;
            case 'textarea':
                echo '<textarea '.$common.'>'.osc_form_element_value().'</textarea>';
                break;

            default:
                echo '<input '.$common.
                    ' type="'.osc_esc_html(osc_form_element_type()).
                    '" value="'.osc_esc_html(osc_form_element_value()).
                    '" '.osc_form_attributes_html(osc_form_element_attributes()).' />';
                break;
        }
    }

    function osc_form_attributes_html($attrs) {
        $html = '';
        if(is_array($attrs)) {
            foreach($attrs as $k => $v) {
                $html .= ' '.osc_esc_html($k).'="'.osc_esc_html($v).'"';
            }
        }
        return $html;
    }

 ?>
