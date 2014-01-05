<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    /*
     *      Osclass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2012 OSCLASS
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

    class CAdminSettingsPermalinks extends AdminSecBaseModel
    {
        function __construct()
        {
            parent::__construct();
        }

        //Business Layer...
        function doModel()
        {
            switch($this->action) {
                case('permalinks'):
                    // calling the permalinks view
                    $htaccess = Params::getParam('htaccess_status');
                    $file     = Params::getParam('file_status');

                    $this->_exportVariableToView('htaccess', $htaccess);
                    $this->_exportVariableToView('file', $file);

                    $this->doView('settings/permalinks.php');
                break;
                case('permalinks_post'):
                    // updating permalinks option
                    osc_csrf_check();
                    $htaccess_file  = osc_base_path() . '.htaccess';
                    $rewriteEnabled = (Params::getParam('rewrite_enabled') ? true : false);

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

                    if( $rewriteEnabled ) {
                        osc_set_preference('rewriteEnabled', '1');;

                        // 1. OK (ok)
                        // 2. OK no apache module detected (warning)
                        // 3. No se puede crear + apache
                        // 4. No se puede crear + no apache
                        // 5. .htaccess exists, no overwrite
                        $status = 3;
                        if( file_exists($htaccess_file) ) {
                            $status = 5;
                        } else {
                            if( is_writable(osc_base_path()) && file_put_contents($htaccess_file, $htaccess) ) {
                                $status = 1;
                            }
                        }

                        if( !@apache_mod_loaded('mod_rewrite') ) {
                            $status++;
                        }

                        osc_set_preference('seo_url_search_prefix', rtrim(Params::getParam('seo_url_search_prefix'), '/'));

                        switch($status) {
                            case 1:
                                $msg  = _m("Permalinks structure updated");
                                osc_add_flash_ok_message($msg, 'admin');
                            break;
                            case 2:
                                $msg  = _m("Permalinks structure updated.");
                                $msg .= " ";
                                $msg .= _m("However, we can't check if Apache module <b>mod_rewrite</b> is loaded. If you experience some problems with the URLs, you should deactivate <em>Friendly URLs</em>");
                                osc_add_flash_warning_message($msg, 'admin');
                            break;
                            case 3:
                                $msg  = _m("File <b>.htaccess</b> couldn't be filled out with the right content.");
                                $msg .= " ";
                                $msg .= _m("Here's the content you have to add to the <b>.htaccess</b> file. If you can't create the file, please deactivate the <em>Friendly URLs</em> option.");
                                $msg .= "</p><pre>" . htmlentities($htaccess, ENT_COMPAT, "UTF-8") . '</pre><p>';
                                osc_add_flash_error_message($msg, 'admin');
                            break;
                            case 4:
                                $msg  = _m("File <b>.htaccess</b> couldn't be filled out with the right content.");
                                $msg .= " ";
                                $msg .= _m("Here's the content you have to add to the <b>.htaccess</b> file. If you can't create the file or experience some problems with the URLs, please deactivate the <em>Friendly URLs</em> option.");
                                $msg .= "</p><pre>" . htmlentities($htaccess, ENT_COMPAT, "UTF-8") . '</pre><p>';
                                osc_add_flash_error_message($msg, 'admin');
                            break;
                            case 5:
                                $warning = false;
                                if( file_exists($htaccess_file) ) {
                                    $htaccess_content = file_get_contents($htaccess_file);
                                    if($htaccess_content!=$htaccess) {
                                        $msg  = _m("File <b>.htaccess</b> already exists and was not modified.");
                                        $msg .= " ";
                                        $msg .= _m("Here's the content you have to add to the <b>.htaccess</b> file. If you can't modify the file or experience some problems with the URLs, please deactivate the <em>Friendly URLs</em> option.");
                                        $msg .= "</p><pre>" . htmlentities($htaccess, ENT_COMPAT, "UTF-8") . '</pre><p>';
                                        $warning = true;
                                    } else {
                                        $msg  = _m("Permalinks structure updated");
                                    }
                                }
                                osc_add_flash_ok_message($msg, 'admin');
                            break;
                        }
                    } else {
                        osc_set_preference('rewriteEnabled', 0);
                        osc_set_preference('mod_rewrite_loaded', 0);

                        $deleted = true;
                        if( file_exists($htaccess_file) ) {
                            $htaccess_content = file_get_contents($htaccess_file);
                            if($htaccess_content==$htaccess) {
                                $deleted = @unlink($htaccess_file);
                                $same_content = true;
                            } else {
                                $deleted = false;
                                $same_content = false;
                            }
                        }
                        if($deleted) {
                            osc_add_flash_ok_message(_m('Friendly URLs successfully deactivated'), 'admin');
                        } else {
                            if($same_content) {
                                osc_add_flash_warning_message(_m('Friendly URLs deactivated, but .htaccess file could not be deleted. Please, remove it manually'), 'admin');
                            } else {
                                osc_add_flash_warning_message(_m('Friendly URLs deactivated, but .htaccess file was modified outside Osclass and was not deleted'), 'admin');
                            }
                        }
                    }

                    $this->redirectTo( osc_admin_base_url(true) . '?page=settings&action=permalinks' );
                break;
            }
        }
    }

    // EOF: ./oc-admin/controller/settings/permalinks.php