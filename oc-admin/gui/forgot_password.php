<?php
    /**
     * Osclass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2012 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="robots" content="noindex, nofollow, noarchive" />
        <meta name="googlebot" content="noindex, nofollow, noarchive" />
        <title><?php echo osc_page_title(); ?> &raquo; <?php _e('Change your password'); ?></title>
        <script type="text/javascript" src="<?php echo osc_assets_url('js/jquery.min.js'); ?>"></script>
        <link type="text/css" href="style/backoffice_login.css" media="screen" rel="stylesheet" />
        <?php osc_run_hook('admin_login_header'); ?>
    </head>
    <body class="forgot">
        <div id="login">
            <h1>
                <a href="<?php echo View::newInstance()->_get('login_admin_url'); ?>" title="<?php echo View::newInstance()->_get('login_admin_title'); ?>">
                    <img src="<?php echo View::newInstance()->_get('login_admin_image'); ?>" border="0" title="<?php echo View::newInstance()->_get('login_admin_title'); ?>" alt="<?php echo View::newInstance()->_get('login_admin_title'); ?>" />
                </a>
            </h1>
            <?php osc_show_flash_message('admin'); ?>
            <div class="flashmessage">
                <?php _e('Type your new password'); ?>.
            </div>
            <?php osc_print_form('forgot_password'); ?>
            <p id="nav">
                <a title="<?php _e('Log in'); ?>" href="<?php echo osc_admin_base_url(); ?>"><?php _e('Log in'); ?></a>
            </p>
        </div>
        <p id="backtoblog"><a href="<?php echo osc_base_url(); ?>" title="<?php printf( __('Back to %s'), osc_page_title() ); ?>">&larr; <?php printf( __('Back to %s'), osc_page_title() ); ?></a></p>
        <script type="text/javascript">
            $(document).ready(function(){
                $('#new_password, #new_password2').focus(function(){
                        $(this).prev().hide();
                }).blur(function(){
                    if($(this).val() == '') {
                        $(this).prev().show();
                    }
                }).prev().click(function(){
                    $(this).hide();
                });

                $(".ico-close").click(function(){
                    $(this).parent().hide();
                });
            });
        </script>
    </body>
</html>