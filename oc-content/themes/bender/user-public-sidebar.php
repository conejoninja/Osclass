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
?>
<div id="sidebar">
    <?php if(osc_logged_user_id() !=  osc_user_id()) { ?>
    <?php     if(osc_reg_user_can_contact() && osc_is_web_user_logged_in() || !osc_reg_user_can_contact() ) { ?>
        <div id="contact" class="widget-box form-container form-vertical">
            <h2><?php _e("Contact", 'bender'); ?></h2>
            <ul id="error_list"></ul>
            <?php //osc_form_element_class('contact_user_form', 'send-button', 'ui-button ui-button-middle ui-button-main');
            osc_print_form('contact_user_form'); ?>
            <?php ContactForm::js_validation(); ?>
        </div>
        <?php
        }
    }
    ?>

</div><!-- /sidebar -->