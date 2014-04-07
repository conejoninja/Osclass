<?php

require_once dirname(__FILE__) . '/OsclassTest.php';
// WE CAN NOT LOAD oc-load, it will trigger not installed error
define('TEST_ABS_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');

class OsclassTestInstaller extends OsclassTest
{

    public function clean()
    {
        // DROP DATABASE
        $mysqli = new mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS);
        $mysqli->query("DROP DATABASE " . TEST_DB_NAME);
        // REMOVE config.php file
        @unlink( TEST_ABS_PATH . 'config.php' );
    }

}
?>