<?php

function _osc_load_form($id) {
    switch($id) {
        case 'contact_form':
            $form = new OSCForm('contact_form');
            $form->addHidden('page', 'contact');
            $form->addHidden('action', 'contact_post');
            $form->addElement(__('Your name'), 'yourName');
            $form->addElement(__('Your email address'), 'yourEmail');
            $form->addElement(__('Subject'), 'subject');
            $form->addElement(__('Message'), 'message');
            if(osc_contact_attachment()) {
                $form->addFile('attachment');
            }

            if( osc_recaptcha_public_key() ) {
                require_once osc_lib_path() . 'recaptchalib.php';
                $form->addHTML(recaptcha_get_html( osc_recaptcha_public_key()));
            };
            $form->addButton(__('Send'), 'contact-send');
            break;
        case 'register':
            $form = new OSCForm('register');
            $form->addHidden('page', 'register');
            $form->addHidden('action', 'register_post');
            $form->addElement(__('Name'), 's_name');
            $form->addElement(__('E-mail'), 's_email');
            $form->addElement(array('label' => __('Password'), 'name' => 's_password', 'type' => 'password'));
            $form->addElement(array('label' => __('Repeat password'), 'name' => 's_password2', 'type' => 'password'));
            $form->addButton(__('Create'));
            break;
        case 'comment_form':
            $form = new OSCForm('comment_form');
            $form->addHidden('page', 'item');
            $form->addHidden('action', 'add_comment');
            $form->addHidden('id', osc_item_id());
            if(osc_is_web_user_logged_in()) {
                $form->addHidden('authorName', osc_osc_logged_user_name());
                $form->addHidden('authorEmail', osc_logged_user_email());
            } else {
                $form->addElement(__('Your name'), 'authorName');
                $form->addElement(__('Your email address'), 'authorEmail');
            }
            $form->addElement(__('Title'), 'title');
            $form->addElement(__('Comment'), 'body');
            $form->addButton(__('Send'), 'comment-send');
            break;
        case 'subscribe-alert':
            $form = new OSCForm('subscribe-alert', null, 'post', 'nocsrf');
            $form->addHidden('alert', osc_search_alert());
            $form->addHidden('alert_userId', osc_logged_user_id());
            if(osc_is_web_user_logged_in()) {
                $form->addHidden('alert_email', osc_logged_user_email());
            } else {
                $form->addElement('E-mail', 'alert_email', __('Enter your e-mail'));
            };
            $form->addButton(__('Subscribe now'), 'subscribe-button');
            break;
        case 'recover_password':
            $form = new OSCForm('recover_password');
            $form->addHidden('page', 'login');
            $form->addHidden('action', 'recover_post');
            $form->addElement(__('E-mail'), 's_email');
            if( osc_recaptcha_public_key() ) {
                require_once osc_lib_path() . 'recaptchalib.php';
                $time  = Session::newInstance()->_get('recover_time');
                if((time()-$time)<=1200) {
                    $form->addHTML(recaptcha_get_html( osc_recaptcha_public_key()));
                }
            }
            $form->addButton(__('Send me a new password'), 'recover-button');
            break;
        case 'forgot-password':
            $form = new OSCForm('forgot-password');
            $form->addHidden('page', 'login');
            $form->addHidden('action', 'forgot_post');
            $form->addHidden('userId', Params::getParam('userId', true));
            $form->addHidden('code', Params::getParam('code', true));
            $form->addElement(array('label' => __('New password'), 'name' => 'new_password', 'type' => 'password'));
            $form->addElement(array('label' => __('Repeat new password'), 'name' => 'new_password2', 'type' => 'password'));
            $form->addButton(__('Change password'), 'submit');
            break;
        case 'user-profile':
            $form = new OSCForm('user-profile');
            $form->addHidden('page', 'user');
            $form->addHidden('action', 'profile_post');
            $form->addElement(__('Name'), 's_name', osc_user_name());
            $options = array(
                array('value' => '0', 'label' => __('User'), 'selected' => !osc_user_is_company()),
                array('value' => '1', 'label' => __('Company'), 'selected' => osc_user_is_company())
            );
            $form->addSelect(__('User type'), 'b_company', $options);
            $form->addElement(__('Cell phone'), 's_phone_mobile', osc_user_phone_mobile());
            $form->addElement(__('Phone'), 's_phone_land', osc_user_phone());

            $user = osc_user();
            $countries = osc_get_countries();
            if(count($countries)>=1) {
                $options = array();
                $options[] = array('value' => '', 'label' => __('Select a country...'));
                foreach($countries as $c) {
                    if($user['fk_c_country_code']==$c['pk_c_code']) {
                        $options[] = array('value' => $c['pk_c_code'], 'label' => $c['s_name'], 'selected' => true);
                    } else {
                        $options[] = array('value' => $c['pk_c_code'], 'label' => $c['s_name']);
                    }
                }
                $form->addSelect(__('Country'), 'countryId', $options);
            } else {
                $form->addElement(__('Country'), 'country', osc_user_country());
            }

            $regions = osc_get_regions();
            if(count($regions)>=1) {
                $options = array();
                $options[] = array('value' => '', 'label' => __('Select a region...'));
                foreach($regions as $r) {
                    if($user['fk_i_region_id']==$r['pk_i_id']) {
                        $options[] = array('value' => $r['pk_i_id'], 'label' => $r['s_name'], 'selected' => true);
                    } else {
                        $options[] = array('value' => $r['pk_i_id'], 'label' => $r['s_name']);
                    }
                }
                $form->addSelect(__('Region'), 'regionId', $options);
            } else {
                $form->addElement(__('Region'), 'region', osc_user_region());
            }

            $cities = osc_get_cities();
            if(count($cities)>=1) {
                $options = array();
                $options[] = array('value' => '', 'label' => __('Select a city...'));
                foreach($cities as $c) {
                    if($user['fk_i_city_id']==$c['pk_i_id']) {
                        $options[] = array('value' => $c['pk_i_id'], 'label' => $c['s_name'], 'selected' => true);
                    } else {
                        $options[] = array('value' => $c['pk_i_id'], 'label' => $c['s_name']);
                    }
                }
                $form->addSelect(__('City'), 'cityId', $options);
            } else {
                $form->addElement(__('City'), 'city', osc_user_city());
            }

            $form->addElement(__('City area'), 'cityArea', osc_user_city_area());
            $form->addElement(__('Address'), 'address', osc_user_address());
            $form->addElement(__('Website'), 's_website', osc_user_website());

            $locales = osc_get_locales();
            if(count($locales)==1 && isset($locales[0]) && isset($locales[0]['pk_c_code'])) {
                $locale = $locales[0]['pk_c_code'];
                $value = '';
                if(isset($user['locale']) && isset($user['locale'][$locale]) && isset($user['locale'][$locale]['s_info'])) { $value = $user['locale'][$locale]['s_info']; };
                $form->addTextArea(__('Description'), 's_info['.$locale.']', $value);
            } else {
                $html = '<div class="tabber">';
                foreach($locales as $locale) {
                    $html .= '<div class="tabbertab">';
                    $html .= '<h2>' . $locale['s_name'] . '</h2>';
                    $value = '';
                    if(isset($user['locale'][$locale['pk_c_code']]) && isset($user['locale'][$locale['pk_c_code']]['s_info'])) {
                        $value = $user['locale'][$locale['pk_c_code']]['s_info'];
                    }
                    $html .= '<textarea id="s_info['.$locale['pk_c_code'].']" name="s_info['.$locale['pk_c_code'].']" rows="10">' . $value . '</textarea>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                $form->addHTML($html);
            }
            osc_run_hook('user_profile_form', $form);
            $form->addButton(__('Update'), 'update-button');
            break;
        case 'contact_user_form':
            // TODO: this should be contact_form, but it collides with previous contact_form
            // contact_user_form has NO JS validation ;(
            $form = new OSCForm('contact_user_form');
            $form->addHidden('page', 'user');
            $form->addHidden('action', 'contact_post');
            $form->addHidden('id', osc_user_id());
            $form->addElement(__('Your name'), 'yourName');
            $form->addElement(__('Your email address'), 'yourEmail');
            $form->addElement(__('Subject'), 'subject');
            $form->addElement(__('Message'), 'message');

            if( osc_recaptcha_public_key() ) {
                require_once osc_lib_path() . 'recaptchalib.php';
                $form->addHTML(recaptcha_get_html( osc_recaptcha_public_key()));
            };
            $form->addButton(__('Send'), 'contact-send');
            break;
        case 'user-login':
            $form = new OSCForm('user-login');
            $form->addHidden('page', 'login');
            $form->addHidden('action', 'login_post');
            $form->addElement(__('E-mail'), 'email');
            $form->addElement(array('label' => __('Password'), 'name' => 'password', 'type' => 'password'));
            $form->addCheckbox(__('Remember me'), 'remember', 1);
            $form->addButton(__('Log in'), 'submit');
            break;
        case 'change-username':
            $form = new OSCForm('change-username');
            $form->addHidden('page', 'user');
            $form->addHidden('action', 'change_username_post');
            $form->addElement(__('Username'), 's_username');
            $form->addHTML('<div id="available"></div>');
            $form->addButton(__('Update'), 'submit');
            break;
        case 'change-password':
            $form = new OSCForm('change-password');
            $form->addHidden('page', 'user');
            $form->addHidden('action', 'change_password_post');
            $form->addElement(array('label' => __('Current password'), 'name' => 'password', 'type' => 'password'));
            $form->addElement(array('label' => __('New password'), 'name' => 'new_password', 'type' => 'password'));
            $form->addElement(array('label' => __('Repeat new password'), 'name' => 'new_password2', 'type' => 'password'));
            $form->addButton(__('Update'), 'submit');
            break;
        case 'change-email':
            $form = new OSCForm('change-password');
            $form->addHidden('page', 'user');
            $form->addHidden('action', 'change_email_post');
            $form->addText(__('Current e-mail'), osc_logged_user_email());
            $form->addElement(__('New email'), 'new_email');
            $form->addButton(__('Update'), 'submit');
            break;
        default:
            return false;
            break;
    }
}



?>