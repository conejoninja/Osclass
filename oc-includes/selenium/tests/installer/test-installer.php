<?php
require_once dirname(dirname(__FILE__)).'/OsclassTestInstaller.php';
class TestInstallerSuite extends OsclassTestInstaller
{

    public function testInstallerEsp()
    {
        $this->clean();

        $this->open(TEST_SERVER_URL);
        $this->click("link=Install");
        $this->waitForPageToLoad("30000");
        $this->select("id=install_locale", "label=Spanish (Spain)");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("MySQLi extensión para PHP", $this->getText("//div[@id='content']/form/div/ul/li[2]"));
        $this->select("id=install_locale", "label=English (US)");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("MySQLi extension for PHP", $this->getText("//div[@id='content']/form/div/ul/li[2]"));
        $this->select("id=install_locale", "label=Spanish (Spain)");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("MySQLi extensión para PHP", $this->getText("//div[@id='content']/form/div/ul/li[2]"));
        $this->click("css=input.button");
        $this->waitForPageToLoad("30000");
        $this->type("id=dbhost", TEST_DB_HOST);
        $this->type("id=dbname", TEST_DB_NAME);
        $this->type("id=username", TEST_DB_USER);
        $this->type("id=password", TEST_DB_PASS);
        $this->click("css=span");
        $this->click("id=createdb");
        $this->type("id=admin_username", TEST_DB_USER);
        $this->type("id=admin_password", TEST_DB_PASS);
        $this->assertEquals("Información de la base de datos", $this->getText("css=h2.target"));
        $this->click("name=submit");
        $this->waitForPageToLoad("60000");
        $this->type("id=s_passwd", TEST_ADMIN_USER);
        $this->type("id=admin_user", TEST_ADMIN_PASS);
        $this->type("id=webtitle", "Testing");
        $this->type("id=email", TEST_ADMIN_EMAIL);
        $this->select("id=country_select", "label=Spain");
        $this->click("link=Next");
        $this->waitForPageToLoad("60000");
        $this->assertEquals("¡Felicitaciones!", $this->getText("css=h2.target"));
        $this->pause(5000);
    }


    public function testInstallerEng()
    {
        $this->clean();

        $this->open(TEST_SERVER_URL);
        $this->click("link=Install");
        $this->waitForPageToLoad("30000");
        $this->select("id=install_locale", "label=Spanish (Spain)");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("MySQLi extensión para PHP", $this->getText("//div[@id='content']/form/div/ul/li[2]"));
        $this->select("id=install_locale", "label=English (US)");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("MySQLi extension for PHP", $this->getText("//div[@id='content']/form/div/ul/li[2]"));
        $this->click("css=input.button");
        $this->waitForPageToLoad("30000");
        $this->type("id=dbhost", TEST_DB_HOST);
        $this->type("id=dbname", TEST_DB_NAME);
        $this->type("id=username", TEST_DB_USER);
        $this->type("id=password", TEST_DB_PASS);
        $this->click("css=span");
        $this->click("id=createdb");
        $this->type("id=admin_username", TEST_DB_USER);
        $this->type("id=admin_password", TEST_DB_PASS);
        $this->click("name=submit");
        $this->waitForPageToLoad("60000");
        $this->type("id=s_passwd", TEST_ADMIN_USER);
        $this->type("id=admin_user", TEST_ADMIN_PASS);
        $this->type("id=webtitle", "Testing");
        $this->type("id=email", TEST_ADMIN_EMAIL);
        $this->select("id=country_select", "label=Spain");
        $this->click("link=Next");
        $this->waitForPageToLoad("60000");
        $this->assertEquals("Congratulations!", $this->getText("css=h2.target"));
        $this->pause(5000);
    }


}
