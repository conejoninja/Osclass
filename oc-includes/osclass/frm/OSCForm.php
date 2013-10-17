<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

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

    class OSCForm {

        private static $_forms;
        private $_self;

        public function __construct($id, $action = null, $method = 'post', $class = '') {
            $this->_self = &OSCForm::$_forms[$id];
            $this->_self['id'] = $id;
            if($action==null) {
                $this->_self['action'] = osc_base_url(true);
            } else {
                $this->_self['action'] = $action;
            }
            $this->_self['method'] = $method;
            $this->_self['class'] = osc_apply_filter('form_class', $class, $id);
            $this->_self['elements'] = array();
            $this->_self['has_files'] = false;
        }

        public function addElement() {
            if(func_num_args()==1) {
                $args = func_get_arg(0);
                if(isset($args['name'])) {
                    if(!isset($args['type'])) { $args['type'] = 'text'; };
                    if(!isset($args['attributes']) || !is_array($args['attributes'])) { $args['attributes'] = array(); };
                    if(!isset($args['options']) || !is_array($args['options'])) { $args['options'] = array(); };
                    $this->_self['elements'][$args['name']] = array(
                        'label' => @$args['label'],
                        'name' => $args['name'],
                        'value' => @$args['value'],
                        'type' => $args['type'],
                        'class' => @$args['class'],
                        'attributes' => $args['attributes'],
                        'options' => $args['options'],
                        'help' => @$args['help']
                    );
                }
            } else if(func_num_args()>=2) {
                $this->_self['elements'][func_get_arg(1)] = array(
                    'label' => func_get_arg(0),
                    'name' => func_get_arg(1),
                    'value' => @func_get_arg(2),
                    'type' => 'text',
                    'class' => '',
                    'attributes' => array(),
                    'options' => array(),
                    'help' => ''
                );
            }
        }

        public function addFile($name = '') {
            $this->_self['has_files'] = true;
            $this->addElement(array('name' => $name,'type' => 'file'));
        }

        public function addButton($text, $name = '', $type = 'submit') {
            $this->addElement(array('name' => ($name!=''?$name:'button-'.$type),'type' => $type, 'attributes' => array('button-text' => $text)));
        }

        public function addHidden($name, $value = '') {
            $this->addElement(array('name' => $name,'type' => 'hidden', 'value' => $value));
        }

        public function addHTML($html) {
            $this->addElement(array('name' => 'html','type' => 'html', 'value' => $html));
        }

        public static function addElementClass($form_id, $element_name, $class) {
            OSCForm::load($form_id);
            if($class!='' && isset(OSCForm::$_forms[$form_id]) && isset(OSCForm::$_forms[$form_id]['elements'][$element_name])) {
                OSCForm::$_forms[$form_id]['elements'][$element_name]['class'] .= " ".$class;
                return true;
            }
            return false;
        }

        public static function addElementAttributes($form_id, $element_name, $attributes) {
            OSCForm::load($form_id);
            if(is_array($attributes) && isset(OSCForm::$_forms[$form_id]) && isset(OSCForm::$_forms[$form_id]['elements'][$element_name])) {
                OSCForm::$_forms[$form_id]['elements'][$element_name]['attributes'] = array_merge(OSCForm::$_forms[$form_id]['elements'][$element_name]['attributes'], $attributes);
                return true;
            }
            return false;
        }

        public static function addElementOptions($form_id, $element_name, $options) {
            OSCForm::load($form_id);
            if(is_array($options) && isset(OSCForm::$_forms[$form_id]) && isset(OSCForm::$_forms[$form_id]['elements'][$element_name])) {
                OSCForm::$_forms[$form_id]['elements'][$element_name]['options'] = array_merge(OSCForm::$_forms[$form_id]['elements'][$element_name]['options'], $options);
                return true;
            }
            return false;
        }

        public static function load($id) {
            if(isset(OSCForm::$_forms[$id])) {
                return OSCForm::$_forms[$id];
            } else {
                require_once LIB_PATH.'osclass/forms.php';
                _osc_load_form($id);
                if(isset(OSCForm::$_forms[$id])) {
                    return OSCForm::$_forms[$id];
                }
            }
            return false;
        }


        public static function forms() {
            return OSCForm::$_forms;
        }

        public static function form($id) {
            if(isset(OSCForm::$_forms[$id])) {
                return OSCForm::$_forms[$id];
            }
            return false;
        }


    }

?>