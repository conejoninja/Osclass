<?php

require_once dirname(__FILE__) . '/OsclassTest.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/oc-load.php';

class OsclassTestFrontend extends OsclassTest
{

    private $_mUser;
    public function setUp() {
        parent::setUp();
        $this->_mUser = User::newInstance();
    }

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

    protected function _removeUserByEmail($email)
    {
        $user = $this->_mUser->findByEmail($email);
        $this->_mUser->deleteByPrimaryKey($user['pk_i_id']);
    }

}
?>