<?php

    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
    <head>
        <?php osc_current_web_theme_url('head.php') ; ?>

        <script type="text/javascript">
            function validateForm() {
                var validator = new FormValidator();
                try {
                    validator.addValidation('email', FormValidator.TYPE_EMAIL) ;
                    return validator.run();
                } catch(e) {
                    alert(e);
                    return false;
                }
            }
        </script>
    </head>
    <body>

        <div class="container">

            <?php osc_current_web_theme_url('header.php') ; ?>

            <div class="content user_forms">
                <div class="inner">
                    <h1><?php _e('Recover your password') ; ?></h1>
                    <form action="<?php echo osc_base_url(true) ; ?>" method="post" onsubmit="javascript:return validateForm() ;">
                        <input type="hidden" name="page" value="login" />
                        <input type="hidden" name="action" value="recover_post" />
                        <fieldset>
                            <label for="email"><?php _e('E-mail') ; ?></label> <?php UserForm::email_text() ; ?><br />
                            <button type="submit"><?php _e('Send me a new password') ; ?></button>
                        </fieldset>
                    </form>
                </div>
            </div>

            <?php osc_current_web_theme_url('footer.php') ; ?>

        </div>

        <?php osc_show_flash_message() ; ?>

    </body>

</html>