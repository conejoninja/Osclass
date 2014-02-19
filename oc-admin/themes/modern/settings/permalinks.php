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
            $( ".sortable" ).sortable({
                start: function(event, ui) {
                    list_original = $('.sortable').sortable('serialize');
                },
                stop: function(event, ui) {

                    $(".jsMessage").fadeIn("fast");
                    $(".jsMessage p").attr('class', '');
                    $(".jsMessage p").html('<img height="16" width="16" src="<?php echo osc_current_admin_theme_url('images/loading.gif');?>"> <?php echo osc_esc_js(__('This action could take a while.')); ?>');

                    var list = $('.sortable').sortable('toArray');
                    if(list_original != list) {
                        var plist = list.reduce(function ( total, current, index ) {
                            total[index] = $("#"+current).attr("route_id");
                            return total;
                        }, {});
                        $.getJSON(
                            "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=routes_order&<?php echo osc_csrf_token_url(); ?>",
                            {"list" : plist},
                            function(data){
                                drawRoutes();
                                $(".jsMessage").fadeIn("fast");
                                $(".jsMessage p").attr('class', data.error==1?'error':'ok');
                                $(".jsMessage p").html(data.msg);
                            }
                        );
                        list_original = list;
                    }
                }
            });
            $( ".sortable" ).disableSelection();

            $("#dialog-route").dialog({
                autoOpen: false,
                modal: true,
                width: 600,
                title: '<?php echo osc_esc_js( __('Route') ); ?>'
            });

            $("#dialog-route-delete").dialog({
                autoOpen: false,
                modal: true,
                title: '<?php echo osc_esc_js( __('Route') ); ?>'
            });

            $("#form-route-submit").on("click", function(e) {
                e.preventDefault();
                $.getJSON(
                    "<?php echo osc_admin_base_url(true); ?>?page=ajax&<?php echo osc_csrf_token_url(); ?>",
                    {
                        "action" : $("#form-route-action").attr("value"),
                        "id" : $("#form-route-id").attr("value"),
                        "route_id" : $("#route-field-id").attr("value"),
                        "location" : $("#route-field-location").attr("value"),
                        "section" : $("#route-field-section").attr("value"),
                        "regexp" : $("#route-field-regexp").attr("value"),
                        "url" : $("#route-field-url").attr("value"),
                        "file" : $("#route-field-file").attr("value")
                    },
                    function(data){
                        drawRoutes();
                        $(".jsMessage").fadeIn("fast");
                        $(".jsMessage p").attr('class', data.error==1?'error':'ok');
                        $(".jsMessage p").html(data.msg);
                        $("#dialog-route").dialog('close');
                    }
                );
            });

            $("#route-delete-submit").on("click", function(e) {
                e.preventDefault();
                $.getJSON(
                    "<?php echo osc_admin_base_url(true); ?>?page=ajax&<?php echo osc_csrf_token_url(); ?>",
                    {
                        "action" : "delete_route",
                        "id" : $("#form-delete-route-id").attr("value")
                    },
                    function(data){
                        drawRoutes();
                        $(".jsMessage").fadeIn("fast");
                        $(".jsMessage p").attr('class', data.error==1?'error':'ok');
                        $(".jsMessage p").html(data.msg);
                        $("#dialog-route-delete").dialog('close');
                    }
                );
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
                        $("#route-field-file-p").hide();
                        break;
                    case '-1':
                    default:
                        $("#route-field-section-p").hide();
                        $("#route-field-file-p").show();
                        break;
                }
            });


            drawRoutes();

        });

        function show_edit(id) {
            $.getJSON(
                "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=route",
                {"route" : id},
                function(data){
                    if(data!=false) {
                        $("#form-route-action").attr("value", 'edit_route');
                        $("#form-route-id").attr("value", data.pk_s_id);
                        $("#route-field-id").attr("value", data.pk_s_id);
                        $("#route-field-location").attr("value", data.s_location);
                        $("#route-field-section").attr("value", data.s_section);
                        $("#route-field-regexp").attr("value", data.s_regexp);
                        $("#route-field-url").attr("value", data.s_url);
                        $("#route-field-file").attr("value", data.s_file);
                        if(data.s_location=='custom') { $("#route-field-file-p").show(); } else { $("#route-field-file-p").hide(); }
                        if(data.b_indelible==1) { $("#route-field-id").attr("disabled", "disabled"); } else { $("#route-field-id").attr("disabled"); }

                        $("#dialog-route").dialog('open');
                    }
                }
            );
        }

        function delete_route(id) {
            $("#form-delete-route-id").attr("value", id);
            $("#dialog-route-delete").dialog('open');
        }

        function show_add() {
            $("#form-route-action").attr("value", 'add_route');
            $("#form-route-id").attr("value", '');
            $("#route-field-id").attr("value", '');
            $("#route-field-location").attr("value", '');
            $("#route-field-section").attr("value", '');
            $("#route-field-regexp").attr("value", '');
            $("#route-field-url").attr("value", '');
            $("#route-field-file").attr("value", '');
            $("#route-field-id").attr("disabled");

            $("#dialog-route").dialog('open');
        }

        function drawRoutes() {
            $(".list-routes ul li").remove();
            $.getJSON(
                "<?php echo osc_admin_base_url(true); ?>?page=ajax",
                {"action" : "routes"},
                function(routes){
                    if(routes!=false) {
                        for(var k in routes) {
                            var data = routes[k];
                            var html = '<li id="list_'+data.pk_s_id+'" route_id="'+data.pk_s_id+'" class="route_li" >';
                            html += '<div class="route_div" id="row_'+data.pk_s_id+'" >';
                            html += '<div class="route_row" >';
                            html += '<div class="handle ico ico-32 ico-droppable"></div>';
                            html += '<div class="route-name" >'+data.pk_s_id+'</div>';
                            html += '<div class="route-url" >'+data.s_url+'</div>';
                            html += '<div class="actions-route">';
                            html += '<a onclick="show_edit(\''+data.pk_s_id+'\');"><?php _e('Edit'); ?></a>';

                            if(data.b_indelible!=1) {
                                html += '<a onclick="delete_route(\''+data.pk_s_id+'\')"><?php _e('Delete'); ?></a>';
                            }

                            html += '</div>';
                            html += '</div>';
                            html += '<div class="edit content_list_'+data.pk_s_id+'"></div>';
                            html += '</div>';
                            html += '</li>';

                            $(".list-routes ul").append(html);
                        }
                    }
                }
            );
        }

    </script>
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
                                    <div><a onclick="javascript:show_add();"><?php _e('Add route'); ?></a></div>
                                    <div class="list-routes">
                                        <ul class="sortable">
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
        <input type="hidden" name="page" value="ajax" />
        <input type="hidden" name="action" id="form-route-action" value="add_route" />
        <input type="hidden" name="id" id="form-route-id" value="" />
        <p id="route-field-id-p" >
            <label><?php _e('Identifier'); ?>: </label><br />
            <input type="text" id="route-field-id" name="route_id" value="" />
        </p>
        <p id="route-field-location-p" >
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
        <p id="route-field-url-p" >
            <label><?php _e('URL'); ?>: </label><br />
            <?php echo WEB_PATH; ?><input type="text" id="route-field-url" name="s_url" value="" />
        </p>
        <p id="route-field-regexp-p" >
            <label><?php _e('Regexp'); ?>: </label><br />
            <input type="text" id="route-field-regexp" name="s_regexp" value="" />
        </p>
        <p id="route-field-file-p" >
            <label><?php _e('File'); ?>: </label><br />
            <input type="text" id="route-field-file" name="s_file" value="<?php echo ABS_PATH; ?>" />
        </p>
        <div class="form-actions">
            <div class="wrapper">
                <button class="btn btn-red close-dialog" ><?php _e('Cancel'); ?></button>
                <button id="form-route-submit" type="submit" class="btn btn-submit" ><?php _e('Ok'); ?></button>
            </div>
        </div>
    </form>

    <form id="dialog-route-delete" method="get" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide">
        <input type="hidden" name="id" id="form-delete-route-id" value="" />
        <div class="form-horizontal">
            <div class="form-row">
                <?php _e('Are you sure you want to delete this route?'); ?>
            </div>
            <div class="form-actions">
                <div class="wrapper">
                    <a class="btn" href="javascript:void(0);" onclick="$('#dialog-route-delete').dialog('close');"><?php _e('Cancel'); ?></a>
                    <input id="route-delete-submit" type="submit" value="<?php echo osc_esc_html( __('Delete') ); ?>" class="btn btn-red" />
                </div>
            </div>
        </div>
    </form>

<?php osc_current_admin_theme_path( 'parts/footer.php' ); ?>