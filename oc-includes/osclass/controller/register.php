<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

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

    class CWebRegister extends BaseModel
    {
        function __construct()
        {
            parent::__construct();

            if( !osc_users_enabled() ) {
                osc_add_flash_error_message( _m('Users not enabled') );
                $this->redirectTo( osc_base_url() );
            }

            if( !osc_user_registration_enabled() ) {
                osc_add_flash_error_message( _m('User registration is not enabled') );
                $this->redirectTo( osc_base_url() );
            }

            if( osc_is_user_logged_in() ) {
                $this->redirectTo( osc_base_url() );
            }
        }

        function doModel()
        {
            switch( $this->action ) {
                case('register'):       //register user
                                        $this->doView('user-register.php');
                break;
                case('register_post'):  //register user
                                        osc_csrf_check();
                                        if( !osc_users_enabled() ) {
                                            osc_add_flash_error_message( _m('Users are not enabled') );
                                            $this->redirectTo( osc_base_url() );
                                        }

                                        osc_run_hook('before_user_register');

                                        $banned = osc_is_banned(Params::getParam('s_email'));
                                        if($banned==1) {
                                            osc_add_flash_error_message( _m('Your current email is not allowed'));
                                            $this->redirectTo(osc_register_account_url());
                                        } else if($banned==2) {
                                            osc_add_flash_error_message( _m('Your current IP is not allowed'));
                                            $this->redirectTo(osc_register_account_url());
                                        }

                                        require_once LIB_PATH . 'osclass/UserActions.php';
                                        $userActions = new UserActions(false);
                                        $success     = $userActions->add();

                                        switch($success) {
                                            case 1: osc_add_flash_ok_message( _m('The user has been created. An activation email has been sent'));
                                                    $this->redirectTo( osc_base_url() );
                                            break;
                                            case 2: osc_add_flash_ok_message( _m('Your account has been created successfully'));
                                                    Params::setParam('action', 'login_post');
                                                    Params::setParam('email', Params::getParam('s_email'));
                                                    Params::setParam('password', Params::getParam('s_password', false, false));
                                                    require_once(osc_lib_path() . 'osclass/controller/login.php');
                                                    $do = new CWebLogin();
                                                    $do->doModel();
                                            break;
                                            case 3: osc_add_flash_warning_message( _m('The specified e-mail is already in use'));
                                            break;
                                            case 4: osc_add_flash_error_message( _m('The reCAPTCHA was not entered correctly'));
                                            break;
                                            case 5: osc_add_flash_warning_message( _m('The email is not valid'));
                                            break;
                                            case 6: osc_add_flash_warning_message( _m('The password cannot be empty'));
                                            break;
                                            case 7: osc_add_flash_warning_message( _m("Passwords don't match"));
                                            break;
                                            case 8: osc_add_flash_warning_message( _m("Username is already taken"));
                                            break;
                                            case 9: osc_add_flash_warning_message( _m("The specified username is not valid, it contains some invalid words"));
                                            break;
                                            case 10: osc_add_flash_warning_message( _m('The name cannot be empty'));
                                            break;
                                        }
                                        $this->doView('user-register.php');
                break;
                case('validate'):       //validate account
                                        $id          = intval( Params::getParam('id') );
                                        $code        = Params::getParam('code');
                                        $userManager = new User();
                                        $user        = $userManager->findByIdSecret($id, $code);

                                        if ( !$user ) {
                                            osc_add_flash_error_message( _m('The link is not valid anymore. Sorry for the inconvenience!') );
                                            $this->redirectTo( osc_base_url() );
                                        }

                                        if ( $user['b_active'] == 1 ) {
                                            osc_add_flash_error_message( _m('Your account has already been validated'));
                                            $this->redirectTo( osc_base_url() );
                                        }

                                        $userManager = new User();
                                        $userManager->update(
                                                 array('b_active' => '1')
                                                ,array('pk_i_id' => $id, 's_secret' => $code)
                                        );

                                        // Auto-login
                                        Session::newInstance()->_set('userId', $user['pk_i_id']);
                                        Session::newInstance()->_set('userName', $user['s_name']);
                                        Session::newInstance()->_set('userEmail', $user['s_email']);

                                        osc_run_hook('hook_email_user_registration', $user);
                                        osc_run_hook('validate_user', $user);

                                        osc_add_flash_ok_message( _m('Your account has been validated'));
                                        $this->redirectTo( osc_base_url() );
                break;
            }
        }

        function doView($file)
        {
            osc_run_hook( 'before_html' );
            osc_current_web_theme_path( $file );
            Session::newInstance()->_clearVariables();
            osc_run_hook( 'after_html' );
        }
    }

    /* file end: ./register.php */
?>