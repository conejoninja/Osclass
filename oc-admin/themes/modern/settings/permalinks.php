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
        .footest .category_div {
            opacity: 0.8;
        }
        .list-categories li {
            opacity: 1 !important;
        }
        .category_div {
            background: #ffffff;
        }
        .alert-custom {
            background-color: #FDF5D9;
            border-bottom: 1px solid #EEDC94;
            color: #404040;
        }
        .cat-hover,
        .cat-hover .category_row{
            background-color:#fffccc !important;
            background:#fffccc !important;
        }
    </style>
    <script type="text/javascript">
    $(function() {
        $('.category_div').on('mouseenter',function(){
            $(this).addClass('cat-hover');
        }).on('mouseleave',function(){
                $(this).removeClass('cat-hover');
            });
        var list_original = '';

        $('.sortable').nestedSortable({
            disableNesting: 'no-nest',
            forcePlaceholderSize: true,
            handle: '.handle',
            helper: 'clone',
            listType: 'ul',
            items: 'li',
            maxLevels: 1,
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            create: function(event, ui) {
            },
            start: function(event, ui) {
                list_original = $('.sortable').nestedSortable('serialize');
                $(ui.helper).addClass('footest');
                $(ui.helper).prepend('<div style="opacity: 1 !important; padding:5px;" class="alert-custom"><?php echo osc_esc_js(__('Note: You must expand the category in order to make it a subcategory.')); ?></div>');
            },
            stop: function(event, ui) {

                $(".jsMessage").fadeIn("fast");
                $(".jsMessage p").attr('class', '');
                $(".jsMessage p").html('<img height="16" width="16" src="<?php echo osc_current_admin_theme_url('images/loading.gif');?>"> <?php echo osc_esc_js(__('This action could take a while.')); ?>');

                var list = '';
                list = $('.sortable').nestedSortable('serialize');
                var array_list = $('.sortable').nestedSortable('toArray');
                var l = array_list.length;
                for(var k = 0; k < l; k++ ) {
                    if( array_list[k].item_id == $(ui.item).find('div').attr('category_id') ) {
                        if( array_list[k].parent_id == 'root' ) {
                            $(ui.item).closest('.toggle').show();
                        }
                        break;
                    }
                }
                if( !$(ui.item).parent().hasClass('sortable') ) {
                    $(ui.item).parent().addClass('subcategory');
                }
                if(list_original != list) {
                    var plist = array_list.reduce(function ( total, current, index ) {
                        total[index] = {'c' : current.item_id, 'p' : current.parent_id};
                        return total;
                    }, {});
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo osc_admin_base_url(true) . "?page=ajax&action=categories_order&" . osc_csrf_token_url(); ?>",
                        data: {'list' : plist},
                        context: document.body,
                        success: function(res){
                            var ret = eval( "(" + res + ")");
                            var message = "";
                            if( ret.error ) {
                                $(".jsMessage p").attr('class', 'error');
                                message += ret.error;
                            }
                            if( ret.ok ){
                                $(".jsMessage p").attr('class', 'ok');
                                message += ret.ok;
                            }

                            $(".jsMessage").show();
                            $(".jsMessage p").html(message);
                        },
                        error: function(){
                            $(".jsMessage").fadeIn("fast");
                            $(".jsMessage p").attr('class', '');
                            $(".jsMessage p").html('<?php echo osc_esc_js(__('Ajax error, please try again.')); ?>');
                        }
                    });

                    list_original = list;
                }
            }
        });

        $(".toggle").bind("click", function(e) {
            var list = $(this).parents('li').first().find('ul');
            var lili = $(this).closest('li').find('ul').find('li').find('ul');
            var li   = $(this).closest('li').first();
            if( $(this).hasClass('status-collapsed') ) {
                $(li).removeClass('no-nest');
                $(list).show();
                $(lili).hide();
                $(this).removeClass('status-collapsed').addClass('status-expanded');
                $(this).html('-');
            } else {
                $(li).addClass('no-nest');
                $(list).hide();
                $(this).removeClass('status-expanded').addClass('status-collapsed');
                $(this).html('+');
            }
        });

        // dialog delete
        $("#dialog-delete-category").dialog({
            autoOpen: false,
            modal: true
        });
        $("#category-delete-submit").click(function() {
            var id  = $("#dialog-delete-category").attr('data-category-id');
            var url  = '<?php echo osc_admin_base_url(true); ?>?page=ajax&action=delete_category&<?php echo osc_csrf_token_url(); ?>&id=' + id;

            $.ajax({
                url: url,
                context: document.body,
                success: function(res) {
                    var ret = eval( "(" + res + ")");
                    var message = "";
                    if( ret.error ) {
                        message += ret.error;
                        $(".jsMessage p").attr('class', 'error');
                    }
                    if( ret.ok ) {
                        message += ret.ok;
                        $(".jsMessage p").attr('class', 'ok');

                        $('#list_'+id).fadeOut("slow");
                        $('#list_'+id).remove();
                    }

                    $(".jsMessage").show();
                    $(".jsMessage p").html(message);
                },
                error: function() {
                    $(".jsMessage").show();
                    $(".jsMessage p").attr('class', '');
                    $(".jsMessage p").html("<?php echo osc_esc_js(__('Ajax error, try again.')); ?>");
                }
            });
            $('#dialog-delete-category').dialog('close');
            $('body,html').animate({
                scrollTop: 0
            }, 500);
            return false;
        });
    });

    list_original = $('.sortable').nestedSortable('serialize');

    function show_iframe(class_name, id) {
        if($('.content_list_'+id+' .iframe-category').length == 0){
            $('.iframe-category').remove();
            var name = 'frame_'+ id;
            var id_  = 'frame_'+ id;
            var url  = '<?php echo osc_admin_base_url(true); ?>?page=ajax&action=category_edit_iframe&id=' + id;
            $.ajax({
                url: url,
                context: document.body,
                success: function(res){
                    $('div.' + class_name).html(res);
                    $('div.' + class_name).fadeIn("fast");
                }
            });
        } else {
            $('.iframe-category').remove();
        }
        return false;
    }

    function delete_category(id) {
        $("#dialog-delete-category").attr('data-category-id', id);
        $("#dialog-delete-category").dialog('open');
        return false;
    }

    function enable_cat(id) {
        var enabled;

        $(".jsMessage").fadeIn("fast");
        $(".jsMessage p").attr('class', '');
        $(".jsMessage p").html('<img height="16" width="16" src="<?php echo osc_current_admin_theme_url('images/loading.gif');?>"> <?php echo osc_esc_js(__('This action could take a while.')); ?>');

        if( $('div[category_id=' + id + ']').hasClass('disabled') ) {
            enabled = 1;
        } else {
            enabled = 0;
        }

        var url  = '<?php echo osc_admin_base_url(true); ?>?page=ajax&action=enable_category&<?php echo osc_csrf_token_url(); ?>&id=' + id + '&enabled=' + enabled;
        $.ajax({
            url: url,
            context: document.body,
            success: function(res) {
                var ret = eval( "(" + res + ")");
                var message = "";
                if(ret.error) {
                    message += ret.error;
                    $(".jsMessage p").attr('class', 'error');
                }
                if(ret.ok) {
                    if( enabled == 0 ) {
                        $('div[category_id=' + id + ']').addClass('disabled');
                        $('div[category_id=' + id + ']').removeClass('enabled');
                        $('div[category_id=' + id + ']').find('a.enable').text('<?php _e('Enable'); ?>');
                        for(var i = 0; i < ret.affectedIds.length; i++) {
                            id =  ret.affectedIds[i].id;
                            $('div[category_id=' + id + ']').addClass('disabled');
                            $('div[category_id=' + id + ']').removeClass('enabled');
                            $('div[category_id=' + id + ']').find('a.enable').text('<?php _e('Enable'); ?>');
                        }
                    } else {
                        $('div[category_id=' + id + ']').removeClass('disabled');
                        $('div[category_id=' + id + ']').addClass('enabled');
                        $('div[category_id=' + id + ']').find('a.enable').text('<?php _e('Disable'); ?>');

                        for(var i = 0; i < ret.affectedIds.length; i++) {
                            id =  ret.affectedIds[i].id;
                            $('div[category_id=' + id + ']').removeClass('disabled');
                            $('div[category_id=' + id + ']').addClass('enabled');
                            $('div[category_id=' + id + ']').find('a.enable').text('<?php _e('Disable'); ?>');
                        }
                    }

                    message += ret.ok;
                    $(".jsMessage p").attr('class', 'ok');
                }

                $(".jsMessage").show();
                $(".jsMessage p").html(message);
            },
            error: function(){
                $(".jsMessage").show();
                $(".jsMessage p").attr('class', '');
                $(".jsMessage p").html("<?php echo osc_esc_js(__('Ajax error, try again.')); ?>");
            }
        });
    }
    </script>
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