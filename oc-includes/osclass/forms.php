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
            if(osc_is_user_logged_in()) {
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
            if(osc_is_user_logged_in()) {
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
        case 'contact_item_form':
            // TODO: this should be contact_form, but it collides with previous contact_form
            // contact_item_form has NO JS validation ;(
            $form = new OSCForm('contact_item_form');
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
        case 'sendfriend':
            $form = new OSCForm('sendfriend');
            $form->addHidden('page', 'item');
            $form->addHidden('action', 'send_friend_post');
            $form->addHidden('id', osc_item_id());
            if(osc_is_user_logged_in()) {
                $form->addHidden('yourName', osc_logged_user_name());
                $form->addHidden('yourEmail', osc_logged_user_email());
            } else {
                $form->addElement(__('Your name'), 'yourName');
                $form->addElement(__('Your e-mail'), 'yourEmail');
            }
            $form->addElement(__("Your friend's name"), 'friendName');
            $form->addElement(__("Your friend's e-mail"), 'friendEmail');
            $form->addElement(__('Subject (optional)'), 'subject');
            $form->addTextArea(__('Message'), 'message');
            osc_run_hook('send_friend_form', $form);
            if( osc_recaptcha_public_key() ) {
                require_once osc_lib_path() . 'recaptchalib.php';
                $form->addHTML(recaptcha_get_html( osc_recaptcha_public_key()));
            };
            $form->addButton(__('Send'), 'submit');
            break;
        case 'search':
            $form = new OSCForm('search', null, 'get', 'nocsrf');
            $form->addHidden('page', 'search');
            $form->addHidden('sOrder', osc_search_order());
            $allowedTypesForSorting = Search::getAllowedTypesForSorting();
            $form->addHidden('iOrderType', $allowedTypesForSorting[osc_search_order_type()]);
            foreach(osc_search_user() as $userId) {
                $form->addHidden('sUser[]', $userId);
            };
            $form->addElement(__('Your search'), 'sPattern', osc_search_pattern());
            $form->addElement(__('City'), 'sCity', osc_search_city());
            if(osc_images_enabled_at_items()) {
                $form->addCheckBox(__('Show only listings with pictures'), 'bPic', 1, osc_search_has_pic());
            };
            if(osc_price_enabled_at_items()) {
                /*<div class="row price-slice">
                <h3><?php _e('Price', 'bender') ; ?></h3>
                <span><?php _e('Min', 'bender') ; ?>.</span>
                <input class="input-text" type="text" id="priceMin" name="sPriceMin" value="<?php echo osc_esc_html(osc_search_price_min()); ?>" size="6" maxlength="6" />
                <span><?php _e('Max', 'bender') ; ?>.</span>
                <input class="input-text" type="text" id="priceMax" name="sPriceMax" value="<?php echo osc_esc_html(osc_search_price_max()); ?>" size="6" maxlength="6" />
                </div>*/
            }
            if(osc_search_category_id()) {
                osc_run_hook('new_search_form', $form, osc_search_category_id());
            } else {
                osc_run_hook('new_search_form', $form);
            }
            $form->addButton(__('Apply'), 'submit');
            break;
        case 'item-form':
            $form = new OSCForm('item-form');
            $form->addHidden('action', 'item_form');


/*
                <form name="item" action="<?php echo osc_base_url(true);?>" method="post" enctype="multipart/form-data" id="item-post">
                    <fieldset>
                    <input type="hidden" name="action" value="<?php echo $action; ?>" />
                    <input type="hidden" name="page" value="item" />
                    <?php if($edit){ ?>
                        <input type="hidden" name="id" value="<?php echo osc_item_id();?>" />
                        <input type="hidden" name="secret" value="<?php echo osc_item_secret();?>" />
                    <?php } ?>
                        <h2><?php _e('General Information', 'bender'); ?></h2>
                        <div class="control-group">
                            <label class="control-label" for="select_1"><?php _e('Category', 'bender'); ?></label>
                            <div class="controls">
                                <?php ItemForm::category_select(null, null, __('Select a category', 'bender')); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="title[<?php echo osc_locale_code(); ?>]"><?php _e('Title', 'bender'); ?></label>
                            <div class="controls">
                                <?php ItemForm::title_input('title',osc_locale_code(), osc_esc_html( bender_item_title() )); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="description[<?php echo osc_locale_code(); ?>]"><?php _e('Description', 'bender'); ?></label>
                            <div class="controls">
                                <?php ItemForm::description_textarea('description',osc_locale_code(), osc_esc_html( bender_item_description() )); ?>
                            </div>
                        </div>
                        <?php if( osc_price_enabled_at_items() ) { ?>
                        <div class="control-group">
                            <label class="control-label" for="price"><?php _e('Price', 'bender'); ?></label>
                            <div class="controls">
                                <?php ItemForm::price_input_text(); ?>
                                <?php ItemForm::currency_select(); ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if( osc_images_enabled_at_items() ) { ?>
                        <div class="box photos">
                            <h2><?php _e('Photos', 'bender'); ?></h2>
                            <div class="control-group">
                                <label class="control-label" for="photos[]"><?php _e('Photos', 'bender'); ?></label>
                                <div class="controls">
                                    <div id="photos">
                                        <?php ItemForm::photos(); ?>
                                    </div>
                                </div>
                                <div class="controls">
                                    <a href="#" onclick="addNewPhoto(); return false;"><?php _e('Add new photo', 'bender'); ?></a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="box location">
                            <h2><?php _e('Listing Location', 'bender'); ?></h2>

                            <div class="control-group">
                                <label class="control-label" for="country"><?php _e('Country', 'bender'); ?></label>
                                <div class="controls">
                                    <?php ItemForm::country_select(osc_get_countries(), osc_user()); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="region"><?php _e('Region', 'bender'); ?></label>
                                <div class="controls">
                                  <?php ItemForm::region_text(osc_user()); ?>
                                </div>
                                    </div>
                                    <div class="control-group">
                                <label class="control-label" for="city"><?php _e('City', 'bender'); ?></label>
                                <div class="controls">
                                    <?php ItemForm::city_text(osc_user()); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="cityArea"><?php _e('City Area', 'bender'); ?></label>
                                <div class="controls">
                                    <?php ItemForm::city_area_text(osc_user()); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="address"><?php _e('Address', 'bender'); ?></label>
                                <div class="controls">
                                  <?php ItemForm::address_text(osc_user()); ?>
                                </div>
                            </div>
                        </div>
                        <!-- seller info -->
                        <?php if(!osc_is_user_logged_in() ) { ?>
                        <div class="box seller_info">
                            <h2><?php _e("Seller's information", 'bender'); ?></h2>
                            <div class="control-group">
                                <label class="control-label" for="contactName"><?php _e('Name', 'bender'); ?></label>
                                <div class="controls">
                                    <?php ItemForm::contact_name_text(); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="contactEmail"><?php _e('E-mail', 'bender'); ?></label>
                                <div class="controls">
                                    <?php ItemForm::contact_email_text(); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls checkbox">
                                    <?php ItemForm::show_email_checkbox(); ?> <label for="showEmail"><?php _e('Show e-mail on the listing page', 'bender'); ?></label>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        if($edit) {
                            ItemForm::plugin_edit_item();
                        } else {
                            ItemForm::plugin_post_item();
                        }
                        ?>
                        <div class="control-group">
                            <?php if( osc_recaptcha_items_enabled() ) { ?>
                                <div class="controls">
                                    <?php osc_show_recaptcha(); ?>
                                </div>
                            <?php }?>
                            <div class="controls">
                                <button type="submit" class="ui-button ui-button-middle ui-button-main"><?php if($edit) { _e("Update", 'bender'); } else { _e("Publish", 'bender'); } ?></button>
                            </div>
                        </div>
                    </fieldset>
                </form>
*/
            break;
        default:
            return false;
            break;
    }
}



?>