<?php
require_once dirname(dirname(__FILE__)).'/OsclassTestFrontend.php';
require_once LIB_PATH . '/osclass/UserActions.php';
class TestUser extends OsclassTestFrontend
{

    private $_mUser;
    public function setUp() {
        parent::setUp();
        $this->_mUser = User::newInstance();
    }

    public function testRegistrationWithValidation()
    {

        osc_set_preference('enabled_users', true);
        osc_set_preference('enabled_user_registration', true);
        osc_set_preference('enabled_user_validation', true);

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
        $this->type("id=s_email", TEST_USER_EMAIL);
        $this->type("id=s_password", TEST_USER_PASS);
        $this->keyUp("id=s_password", "a");
        $this->type("id=s_password2", TEST_USER_PASS);
        $this->keyUp("id=s_password2", "a");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xThe user has been created. An activation email has been sent", $this->getText("id=flashmessage"));

        $user = $this->_mUser->findByEmail(TEST_USER_EMAIL);
        $this->_mUser->deleteByPrimaryKey($user['pk_i_id']);
    }


    public function testRegistrationWithoutValidation()
    {

        osc_set_preference('enabled_users', true);
        osc_set_preference('enabled_user_registration', true);
        osc_set_preference('enabled_user_validation', false);

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
        $this->type("id=s_email", TEST_USER_EMAIL);
        $this->type("id=s_password", TEST_USER_PASS);
        $this->keyUp("id=s_password", "a");
        $this->type("id=s_password2", TEST_USER_PASS);
        $this->keyUp("id=s_password2", "a");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xYour account has been created successfully", $this->getText("id=flashmessage"));
        $this->assertEquals("Hi Test! ·", $this->getText("css=li.first.logged > span"));

    }

    public function testLogin()
    {
        $this->open("/osclass/Osclass/index.php?page=login");
        $this->click("id=login_open");
        $this->waitForPageToLoad("30000");

        // WRONG EMAIL
        $this->type("id=email", "user.example.com");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xThe user doesn't exist", $this->getText("id=flashmessage"));

        // WRONG PASSWORD
        $this->type("id=email", TEST_USER_EMAIL);
        $this->type("id=password", "wrong_password");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xThe password is incorrect", $this->getText("id=flashmessage"));

        // CORRECT LOGIN
        $this->_login();
        $this->assertEquals("Hi Test! ·", $this->getText("css=li.first.logged > span"));
    }

    public function testUpdateUsername()
    {
        $this->_login();

        $this->click("link=My account");
        $this->waitForPageToLoad("30000");
        $this->click("link=Change username");
        $this->waitForPageToLoad("30000");

        // INVALID USERNAME
        $this->type("id=s_username", "admin");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xThe specified username is not valid, it contains some invalid words", $this->getText("id=flashmessage"));

        // CORRECT USERNAME
        $this->type("id=s_username", TEST_USER_USER);
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xThe username was updated", $this->getText("id=flashmessage"));

    }

    public function testUpdateProfile()
    {
        $this->_login();

        $this->click("link=My account");
        $this->waitForPageToLoad("30000");
        $this->click("css=li.opt_account > a");
        $this->waitForPageToLoad("30000");
        $this->select("id=b_company", "label=Company");
        $this->type("id=s_phone_mobile", "123-456-7809");
        $this->type("id=s_phone_land", "123-456-7890");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xYour profile has been updated successfully", $this->getText("id=flashmessage"));

    }

    public function testUserDelete()
    {
        $this->_login();

        $this->click("link=My account");
        $this->waitForPageToLoad("30000");
        $this->click("link=Delete account");
        $this->click("//button[@type='button']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xYour account have been deleted", $this->getText("id=flashmessage"));

    }

}
?>