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
        case 'forgot_password':
            $form = new OSCForm('forgot_password');
            $form->addHidden('page', 'login');
            $form->addHidden('action', 'forgot_post');
            $form->addHidden('adminId', Params::getParam('adminId', true));
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
            //TODO :

/*

<div class="control-group">
    <label class="control-label" for="country"><?php _e('Country', 'bender'); ?></label>
    <div class="controls">
        <?php UserForm::country_select(osc_get_countries(), osc_user()); ?>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="region"><?php _e('Region', 'bender'); ?></label>
    <div class="controls">
        <?php UserForm::region_select(osc_get_regions(), osc_user()); ?>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="city"><?php _e('City', 'bender'); ?></label>
    <div class="controls">
        <?php UserForm::city_select(osc_get_cities(), osc_user()); ?>
    </div>
</div>        */

            $form->addElement(__('City area'), 'cityArea', osc_user_city_area());
            $form->addElement(__('Address'), 'address', osc_user_address());
            $form->addElement(__('Website'), 's_website', osc_user_website());
            //TODO :
/*
<div class="control-group">
    <label class="control-label" for="s_info"><?php _e('Description', 'bender'); ?></label>
    <div class="controls">
        <?php UserForm::info_textarea('s_info', osc_locale_code(), @$osc_user['locale'][osc_locale_code()]['s_info']); ?>
    </div>
</div>
<div class="control-group">
    <div class="controls">
        <button type="submit" class="ui-button ui-button-middle ui-button-main"><?php _e("Update", 'bender');?></button>
    </div>
</div>      */
            $form->addButton(__('Update'), 'update-button');

            //TODO :
/*
<div class="control-group">
    <div class="controls">
        <?php osc_run_hook('user_form'); ?>
    </div>
</div>
</form>   */



            break;
        default:
            return false;
            break;
    }
}



?>