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

    bender_add_body_class('user user-profile');
    osc_add_hook('before-main','sidebar');
    function sidebar(){
        osc_current_web_theme_path('user-sidebar.php');
    }
    osc_add_filter('meta_title_filter','custom_meta_title');
    function custom_meta_title($data){
        return __('Update account', 'bender');
    }
    osc_current_web_theme_path('header.php') ;
    $osc_user = osc_user();
?>
<h1><?php _e('Update account', 'bender'); ?></h1>
<?php UserForm::location_javascript(); ?>
<div class="form-container form-horizontal">
    <div class="resp-wrapper">
        <ul id="error_list"></ul>
        <?php osc_form_element_class('user-profile', 'update-button', 'ui-button ui-button-middle ui-button-main');
        osc_print_form('user-profile'); ?>
    </div>
</div>
<?php osc_current_web_theme_path('footer.php') ; ?>