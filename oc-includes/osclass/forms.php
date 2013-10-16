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
                $form->addElement(array('name' => 'attachment', 'type' => 'file'));
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
        default:
            return false;
            break;
    }
}



?>