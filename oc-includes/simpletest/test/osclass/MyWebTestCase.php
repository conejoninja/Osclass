<?php
require_once(dirname(__FILE__).'/../../web_tester.php');

class MyWebTestCase extends WebTestCase {

    function __construct($label = false) {
        parent::__construct($label);
    }

    function assert($expectation, $compare, $message = '%s')
    {
        $res = parent::assert( $expectation, $compare, $message );

        $bt = debug_backtrace();
        $function = $bt[2]['function'];

        $date = $function."_".time().".png";
        if(defined('TEST_IMAGE_PATH')) {
            $path = TEST_IMAGE_PATH;
        } else {
            $path = "/var/www/vm-test-osclass.office/subdomains/images_test/httpdocs/img/";
        }
        $img  = $path.$date;

        if(!$res) {
            if(defined('TEST_IMAGE_URL')) {
                $a = "<a target='_blank' href='".TEST_IMAGE_URL.$date."'>Image test failed</a><br /><br />";
            } else {
                $a = "<a target='_blank' href='http://images_test.vm-test-osclass.office/img/$date'>Image test failed</a><br /><br />";
            }
            $this->reporter->addFail($message . " " . $a);
            $cmd = "DISPLAY=:1 import -window root ".$img;
            system($cmd);
            $this->selenium->captureScreenshot($date);
        }

        return $res;
    }

    function _lastItemId()
    {
        // get last id from t_item.
        $item   = Item::newInstance()->dao->query('select pk_i_id from '.DB_TABLE_PREFIX.'t_item order by pk_i_id DESC limit 0,1');
        $aItem  = $item->result();
        return $aItem[0]['pk_i_id'];
    }
}

?>