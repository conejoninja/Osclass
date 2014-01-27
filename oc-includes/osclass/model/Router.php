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

    class Router extends DAO
    {
        private static $instance;
        private $routes;
        private $request_uri;
        private $raw_request_uri;
        private $uri;
        private $location;
        private $section;
        private $title;
        private $http_referer;

        public function __construct()
        {
            parent::__construct();
            $this->setTableName('t_route');
            $this->setPrimaryKey('pk_s_id');
            $array_fields = array(
                'pk_s_id',
                's_regexp',
                's_url',
                's_file',
                'b_user_menu',
                's_location',
                's_section',
                's_title',
                'b_indelible',
                'i_order'
            );
            $this->setFields($array_fields);

            $this->request_uri = '';
            $this->raw_request_uri = '';
            $this->uri = '';
            $this->location = '';
            $this->section = '';
            $this->title = '';
            $this->http_referer = '';
            $this->routes = $this->routes();
        }

        public static function newInstance()
        {
            if(!self::$instance instanceof self) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function routes()
        {
            $this->dao->select('*');
            $this->dao->from($this->getTableName());
            $this->dao->orderBy('i_order', 'DESC');
            $result = $this->dao->get();

            if($result===false) {
                return array();
            }

            $this->routes = $result->result();
            return $this->routes;
        }

        public function addRoute($id, $regexp, $url, $file = '', $user_menu = false, $location = "custom", $section = "custom", $indelible = 0)
        {
            $regexp = trim($regexp);
            $file = trim($file);
            if($regexp!='') {
                $order = $this->lastOrder()+1;
                $params = array(
                    'pk_s_id' => $id,
                    's_regexp' => $regexp,
                    's_url' => $url,
                    's_file' => $file,
                    'b_user_menu' => $user_menu,
                    's_location' => $location,
                    's_section' => $section,
                    'i_order' => $order,
                    'b_indelible' => $indelible
                );
                return $this->dao->insert($this->getTableName(), $params);
            }
        }

        public function lastOrder()
        {
            $this->dao->select('i_order');
            $this->dao->from($this->getTableName());
            $this->dao->orderBy('i_order', 'DESC');
            $result = $this->dao->get();

            if($result->numRows==0) {
                return 0;
            }
            $row = $result->row();
            return $row['i_order'];
        }

        /*
         * @deprecated to be removed in 5.0
         */
        public function getRoutes()
        {
            return $this->routes();
        }

        public function init()
        {
            // $_SERVER is not supported by Params Class... we should fix that
            if(isset($_SERVER['REQUEST_URI'])) {
                if(preg_match('|[\?&]{1}http_referer=(.*)$|', urldecode($_SERVER['REQUEST_URI']), $ref_match)) {
                    $this->http_referer = $ref_match[1];
                    $_SERVER['REQUEST_URI'] = preg_replace('|[\?&]{1}http_referer=(.*)$|', "", urldecode($_SERVER['REQUEST_URI']));
                }
                $request_uri = preg_replace('@^' . REL_WEB_URL . '@', "", urldecode($_SERVER['REQUEST_URI']));
                $this->raw_request_uri = $request_uri;
                if(Params::getParam('r')!='') { $request_uri = Params::getParam('r'); }
                $tmp = explode("?", $request_uri);
                $request_uri = $tmp[0];
                foreach($this->routes as $id => $route) {
                    // UNCOMMENT TO DEBUG
                    //echo 'Request URI: '.$request_uri." # Match : ".$route['s_regexp']." # URI to go : ".$route['s_url']." <br />";
                    if(preg_match('#^'.$route['s_regexp'].'#', $request_uri, $m)) {
                        if(!preg_match_all('#\{([^\}]+)\}#', $route['url'], $args)) {
                            $args[1] = array();
                        }
                        $l = count($m);
                        for($p=1;$p<$l;$p++) {
                            if(isset($args[1][$p-1])) {
                                Params::setParam($args[1][$p-1], $m[$p]);
                            } else {
                                Params::setParam('route_param_'.$p, $m[$p]);
                            }
                        }
                        if($route['s_file']!='') {
                            Params::setParam('page', 'custom');
                            Params::setParam('route', $id);
                        } else {
                            Params::setParam('page', $route['s_location']);
                            Params::setParam('action', $route['s_section']);
                        }

                        //$this->extractParams($request_uri);
                        $this->request_uri = $request_uri;

                        $this->location = $route['s_location'];
                        $this->section = $route['s_section'];
                        $this->title = $route['s_title'];
                        break;
                    }
                }
            }
        }

        public function extractURL($uri = '')
        {
            $uri_array = explode('?', str_replace('index.php', '', $uri));
            if(substr($uri_array[0], 0, 1)=="/") {
                return substr($uri_array[0], 1);
            } else {
                return $uri_array[0];
            }
        }

        public function extractParams($uri = '')
        {
            $uri_array = explode('?', $uri);
            $length_i = count($uri_array);
            for($var_i = 1;$var_i<$length_i;$var_i++) {
                if(preg_match_all('|&([^=]+)=([^&]*)|', '&'.$uri_array[$var_i].'&', $matches)) {
                    $length = count($matches[1]);
                    for($var_k = 0;$var_k<$length;$var_k++) {
                        Params::setParam($matches[1][$var_k], $matches[2][$var_k]);
                    }
                }
            }
        }

        public function get_request_uri()
        {
            return $this->request_uri;
        }

        public function get_raw_request_uri()
        {
            return $this->raw_request_uri;
        }

        public function set_location($location)
        {
            $this->location = $location;
        }

        public function get_location()
        {
            return $this->location;
        }

        public function get_section()
        {
            return $this->section;
        }
        
        public function get_title()
        {
            return $this->title;
        }
        
        public function get_http_referer()
        {
            return $this->http_referer;
        }
        
    }

?>