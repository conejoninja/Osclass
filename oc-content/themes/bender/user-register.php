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

// meta tag robots
osc_add_hook('header','bender_nofollow_construct');

bender_add_body_class('register');
osc_enqueue_script('jquery-validate');
osc_current_web_theme_path('header.php');

osc_form_element_class('register', 'button-submit', 'ui-button ui-button-middle ui-button-main');

?>
    <div class="form-container form-horizontal form-container-box">
        <div class="header">
            <h1><?php _e('Register an account for free', 'bender'); ?></h1>
        </div>
        <div class="resp-wrapper">
            <ul id="error_list"></ul>
            <?php osc_print_form('register'); ?>
        </div>
    </div>
<?php UserForm::js_validation(); ?>
<?php osc_current_web_theme_path('footer.php') ; ?>