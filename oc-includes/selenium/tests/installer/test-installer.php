<?php
require_once dirname(dirname(__FILE__)).'/OsclassTest.php';
class Example extends OsclassTest
{

  public function testMyTestCase()
  {
    $this->open("/testing/Osclass/");
    $this->click("link=Install");
    $this->waitForPageToLoad("30000");
    $this->select("id=install_locale", "label=Spanish");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Extensión MySQLi para PHP", $this->getText("//div[@id='content']/form/div/ul/li[2]"));
    $this->click("css=input.button");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Información de la base de datos", $this->getText("css=h2.target"));
    $this->type("id=dbname", "osclass_test_db");
    $this->type("id=username", "root");
    $this->type("id=password", "D_Rj9+AZ");
    $this->click("css=span");
    $this->click("id=createdb");
    $this->type("id=admin_username", "root");
    $this->type("id=admin_password", "D_Rj9+AZ");
    $this->click("name=submit");
    $this->waitForPageToLoad("30000");
    $this->type("id=s_passwd", "q1w2e3r4t5y6");
    $this->type("id=webtitle", "Testing");
    $this->type("id=email", "nodani@gmail.com");
    $this->select("id=country_select", "label=Spain");
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Congratulations!", $this->getText("css=h2.target"));
  }
}
?>