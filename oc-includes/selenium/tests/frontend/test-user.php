<?php
require_once dirname(dirname(__FILE__)).'/OsclassTestFrontend.php';
require_once LIB_PATH . '/osclass/UserActions.php';
class TestUser extends OsclassTestFrontend
{

    public function setUp() {
        parent::setUp();
    }

    public function testRegistration()
    {

        print_r($this->_mUSer);
        $this->open(TEST_SERVER_URL);
        $this->click("link=Register for a free account");
        $this->waitForPageToLoad("30000");
        $this->click("//button[@type='submit']");
        $this->assertEquals("Name: this field is required.", $this->getText("css=label.error"));
        $this->assertEquals("Email: this field is required.", $this->getText("//ul[@id='error_list']/li[2]/label"));
        $this->assertEquals("Password: this field is required.", $this->getText("//ul[@id='error_list']/li[3]/label"));
        $this->assertEquals("Second password: this field is required.", $this->getText("//ul[@id='error_list']/li[4]/label"));
        $this->type("id=s_name", "Test");
        $this->keyUp("id=s_name", "a");
        $this->type("id=s_email", "test.test.com");
        $this->keyUp("id=s_email", "a");
        $this->assertEquals("Invalid email address.", $this->getText("//ul[@id='error_list']/li[2]/label"));
        $this->type("id=s_email", "");
        $this->type("id=s_email", "test@test.com");
        $this->type("id=s_password", "testing");
        $this->keyUp("id=s_password", "a");
        $this->type("id=s_password2", "testing");
        $this->keyUp("id=s_password2", "a");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xThe user has been created. An activation email has been sent", $this->getText("id=flashmessage"));


        $user = User::newInstance()->findByEmail('test@test.com');
        $_mUserActions = new UserActions(true);
        $_mUserActions->activate($user['pk_i_id']);

        $this->assertEquals("Hi Test! ·", $this->getText("css=li.logged > span"));
        $this->click("link=My account");
        $this->waitForPageToLoad("30000");
        $this->click("link=Change username");
        $this->waitForPageToLoad("30000");
        $this->type("id=s_username", "admin");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xThe specified username is not valid, it contains some invalid words", $this->getText("id=flashmessage"));
        $this->type("id=s_username", "Testing");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xThe username was updated", $this->getText("id=flashmessage"));
        $this->select("id=b_company", "label=Company");
        $this->type("id=s_phone_mobile", "123-456-7809");
        $this->type("id=s_phone_land", "123-456-7890");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xYour profile has been updated successfully", $this->getText("id=flashmessage"));
        $this->click("link=Delete account");
        $this->assertEquals("Delete account", $this->getText("id=ui-id-1"));
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->click("link=Delete account");
        $this->assertEquals("Delete account", $this->getText("id=ui-id-1"));
        $this->click("//button[@type='button']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xYour account have been deleted", $this->getText("id=flashmessage"));
    }
}
?>