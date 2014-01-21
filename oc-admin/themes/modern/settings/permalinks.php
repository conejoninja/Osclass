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
        });
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
                    <a onclick="show_iframe('content_list_<?php echo $route['pk_s_id'];?>','<?php echo $route['pk_s_id']; ?>');"><?php _e('Edit'); ?></a>
                    &middot;
                    <a onclick="delete_route(<?php echo $route['pk_s_id']; ?>)"><?php _e('Delete'); ?></a>
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
<?php osc_current_admin_theme_path( 'parts/footer.php' ); ?>