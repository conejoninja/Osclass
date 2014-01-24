<?php if ( ! defined('OC_ADMIN')) exit('Direct access is not allowed.');
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

    osc_enqueue_script('jquery-validate');
    $routes = __get('routes');

    //customize Head
    function customHead() { ?>
        <script type="text/javascript">
            function showhide() {
                $("#inner_routes").toggle();
                if($("#show_hide a").html()=='<?php echo osc_esc_js(__('Show routes')); ?>') {
                    $("#show_hide a").html('<?php echo osc_esc_js(__('Hide routes')); ?>');
                    resetLayout();
                } else {
                    $("#show_hide a").html('<?php echo osc_esc_js(__('Show routes')); ?>')
                }
            }

            $(function() {
                $("#rewrite_enabled").click(function(){
                    $("#custom_routes").toggle();
                });
            });
        </script>
        <?php
    }
    osc_add_hook('admin_header','customHead', 10);

    function render_offset(){
        return 'row-offset';
    }
    osc_add_hook('admin_page_header','customPageHeader');

    function addHelp() {
        echo '<p>' . __("Activate this option if you want your site's URLs to be more attractive to search engines and intelligible for users. <strong>Be careful</strong>: depending on your hosting service, this might not work correctly.") . '</p>';
    }
    osc_add_hook('help_box','addHelp');

    function customPageHeader(){ ?>
        <h1><?php _e('Settings'); ?>
            <a href="#" class="btn ico ico-32 ico-help float-right"></a>
        </h1>
    <?php
    }

    function customPageTitle($string) {
        return sprintf(__('Permalinks &raquo; %s'), $string);
    }
    osc_add_filter('admin_title', 'customPageTitle');

    osc_current_admin_theme_path( 'parts/header.php' ); ?>
    <style>
        .placeholder {
            background-color: #cfcfcf;
        }
        .footest .route_div {
            opacity: 0.8;
        }
        .list-routes li {
            opacity: 1 !important;
        }
        .route_div {
            background: #ffffff;
        }
        .alert-custom {
            background-color: #FDF5D9;
            border-bottom: 1px solid #EEDC94;
            color: #404040;
        }
        .route-hover,
        .route-hover .route_row{
            background-color:#fffccc !important;
            background:#fffccc !important;
        }
    </style>
    <script type="text/javascript">
        $(function() {
            $( ".sortable" ).sortable();
            $( ".sortable" ).disableSelection();

            $("#dialog-route").dialog({
                autoOpen: false,
                modal: true,
                width: 600,
                title: '<?php echo osc_esc_js( __('Route') ); ?>'
            });

            $("#route-field-location").on("change", function() {
                var options = {
                    'contact': { '': 'Contact page' },
                    'item': { 'item_add': 'Add new listing', 'item_edit': 'Edit listing', 'mark': 'Mark listing as spam', '': 'Listing page', 'item_delete': 'Delete a listing', 'activate': 'Activate a listing', 'send_friend': 'Send listing to a friend', 'deleteResource': 'Delete an image of a listing'},
                    'language': { '': 'Change current language' },
                    'login': { '': 'User login', 'forgot': 'Forgot password', 'recover': 'Recover password' },
                    'main': { 'logout': 'User logout' },
                    'page': { '': 'Page' },
                    'register': { 'register': 'User registration' },
                    'search': { '': 'Search page' },
                    'user': { 'items': 'Listings of an user', 'alerts': 'Alerts of an user', 'change_email': 'Change email', 'profile': 'Profile', 'activate_alert': 'Activate alert',
                        'change_email_confirm': 'Change email confirmation', 'validate': 'Activate user', 'dashboard': 'Dashboard', 'change_password': 'Change password', 'change_username': 'Change username',
                        'pub_profile': 'Public profile'}
                }
                var location = $("#route-field-location").attr("value");
                switch(location) {
                    case 'contact':
                    case 'item':
                    case 'language':
                    case 'login':
                    case 'main':
                    case 'page':
                    case 'register':
                    case 'search':
                    case 'user':
                        $("#route-field-section-p").show();
                        $("#route-field-section option").remove();
                        $("#route-field-section").append('<option value="-1" ><?php _e('Select a route action'); ?></option>');
                        for (var key in options[location]) {
                            $("#route-field-section").append('<option value="'+key+'" >'+options[location][key]+'</option>');
                        }
                        break;
                    case '-1':
                    default:
                        $("#route-field-section-p").hide();
                        break;
                }
            });

        });

        function show_edit(id) {
            $.getJSON(
                "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=route",
                {"route" : id},
                function(data){
                    $("#dialog-route").dialog('open');
                }
            );
        }

    </script>
<?php
function drawRoute($route) {
    ?>
    <li id="list_<?php echo $route['pk_s_id']; ?>" class="route_li" >
        <div class="route_div" route_id="<?php echo $route['pk_s_id']; ?>" >
            <div class="route_row">
                <div class="handle ico ico-32 ico-droppable"></div>
                <div class="route-name" ><?php echo $route['pk_s_id']; ?></div>
                <div class="route-url" ><?php echo $route['s_url']; ?></div>
                <div class="actions-route">
                    <a onclick="show_edit('<?php echo $route['pk_s_id']; ?>');"><?php _e('Edit'); ?></a>
                    <?php if($route['b_indelible']!=1) { ?>
                        &middot;
                        <a onclick="delete_route(<?php echo $route['pk_s_id']; ?>)"><?php _e('Delete'); ?></a>
                    <?php }; ?>
                </div>
            </div>
            <div class="edit content_list_<?php echo $route['pk_s_id']; ?>"></div>
        </div>
    </li>
<?php
} //End drawCategory
?>


    <style>
        .sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
        .sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
        .sortable li span { position: absolute; margin-left: -1.3em; }
    </style>


<div id="mail-setting">
    <!-- settings form -->
                    <div id="mail-settings">
                        <h2 class="render-title"><?php _e('Permalinks'); ?></h2>
                        <?php _e('By default Osclass uses web URLs which have question marks and lots of numbers in them. However, Osclass offers you friendly urls. This can improve the aesthetics, usability, and forward-compatibility of your links'); ?>
                        <ul id="error_list"></ul>
                        <form name="settings_form" action="<?php echo osc_admin_base_url(true); ?>" method="post">
                            <input type="hidden" name="page" value="settings" />
                            <input type="hidden" name="action" value="permalinks_post" />
                            <fieldset>
                            <div class="form-horizontal">
                            <div class="form-row">
                                <div class="form-label"><?php _e('Enable friendly urls'); ?></div>
                                <div class="form-controls">
                                    <div class="form-label-checkbox"><input type="checkbox" <?php echo ( osc_rewrite_enabled() ? 'checked="checked"' : '' ); ?> name="rewrite_enabled" id="rewrite_enabled" value="1" />
                                    </div>
                                </div>
                            </div>
                            <div id="custom_routes" <?php if( !osc_rewrite_enabled() ) { echo 'class="hide"'; } ?>>
                                <div id="show_hide" ><a href="#" onclick="javascript:showhide();"><?php _e('Show routes'); ?></a></div>
                                <div id="inner_routes" class="hide">
                                    <div class="list-routes">
                                        <ul class="sortable">
                                            <?php foreach($routes as $route) {
                                                drawRoute($route);
                                            } ?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <?php if( osc_rewrite_enabled() ) { ?>
                            <?php if( file_exists(osc_base_path() . '.htaccess') ) { ?>
                            <div class="form-row">
                                <h3 class="separate-top"><?php _e('Your .htaccess file') ?></h3>
                                <pre><?php
                                    $htaccess_content =  file_get_contents(osc_base_path() . '.htaccess');
                                    echo htmlentities($htaccess_content);
                                ?></pre>
                            </div>
                            <div class="form-row">
                                <h3 class="separate-top"><?php _e('What your .htaccess file should look like') ?></h3>
                                <pre><?php
                                    $rewrite_base = REL_WEB_URL;
                                    $htaccess     = <<<HTACCESS
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {$rewrite_base}
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . {$rewrite_base}index.php [L]
</IfModule>
HTACCESS;
                                    echo htmlentities($htaccess);
                                ?></pre>
                            </div>
                            <?php } ?>
                            <?php } ?>
                            <div class="form-actions">
                                <input type="submit" id="save_changes" value="<?php echo osc_esc_html( __('Save changes') ); ?>" class="btn btn-submit" />
                            </div>
                        </div>
                        </fieldset>
                    </form>
                </div>
                <!-- /settings form -->
</div>

    <form id="dialog-route" method="get" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide">
        <input type="hidden" name="page" value="settings" />
        <input type="hidden" name="action" id="form-route-action" value="new" />
        <input type="hidden" name="id" id="form-route-id" value="" />
        <p>
            <label><?php _e('Identifier'); ?>: </label><br />
            <input type="text" id="route-field-id" name="s_id" value="" />
        </p>
        <p>
            <label><?php _e('Route type'); ?>: </label><br />
            <select id="route-field-location" name="route-field-location" >
                <option value="-1" ><?php _e('Select a route type'); ?></option>
                <option value="custom" ><?php _e('Custom'); ?></option>
                <?php /* <option value="url" ><?php _e('URL'); ?></option> */ ?>
                <option value="item" ><?php _e('Item'); ?></option>
                <option value="search" ><?php _e('Search'); ?></option>
                <option value="user" ><?php _e('User'); ?></option>
                <option value="page" ><?php _e('Page'); ?></option>
                <option value="contact" ><?php _e('Contact'); ?></option>
                <option value="login" ><?php _e('Login'); ?></option>
                <option value="register" ><?php _e('Register'); ?></option>
                <option value="main" ><?php _e('Main'); ?></option>
                <option value="language" ><?php _e('Language'); ?></option>
            </select>
        </p>
        <p id="route-field-section-p" style="display: none;">
            <label><?php _e('Route action'); ?>: </label><br />
            <select id="route-field-section" name="route-field-section" >
                <option value="-1" ><?php _e('Select a route action'); ?></option>
            </select>
        </p>
        <p>
            <label><?php _e('URL'); ?>: </label><br />
            <?php echo WEB_PATH; ?><input type="text" id="route-field-url" name="s_url" value="" />
        </p>
        <p>
            <label><?php _e('Regexp'); ?>: </label><br />
            <input type="text" id="route-field-regexp" name="s_regexp" value="" />
        </p>
        <p>
            <label><?php _e('File'); ?>: </label><br />
            <input type="text" id="route-field-file" name="s_file" value="<?php echo ABS_PATH; ?>" />
        </p>
        <div class="form-actions">
            <div class="wrapper">
                <button class="btn btn-red close-dialog" ><?php _e('Cancel'); ?></button>
                <button type="submit" class="btn btn-submit" ><?php _e('Ok'); ?></button>
            </div>
        </div>
    </form>

<?php osc_current_admin_theme_path( 'parts/footer.php' ); ?>