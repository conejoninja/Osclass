<?php

require_once dirname(__FILE__) . '/OsclassTest.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/oc-load.php';

class OsclassTestFrontend extends OsclassTest
{

    protected function _login($email = TEST_USER_EMAIL, $pass = TEST_USER_PASS)
    {
        $this->open("/osclass/Osclass/index.php?page=login");
        $this->click("id=login_open");
        $this->waitForPageToLoad("30000");
        $this->type("id=email", $email);
        $this->type("id=password", $pass);
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
    }

}
?>