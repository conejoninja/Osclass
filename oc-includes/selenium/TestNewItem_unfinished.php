<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost/osclass/Osclass/");
  }

  public function testMyTestCase()
  {
    $this->open("/osclass/Osclass/");
    $this->click("link=Publish your ad for free");
    $this->waitForPageToLoad("30000");
    $this->select("id=catId", "label=regexp:\\s+Women looking for Men");
    $this->click("css=option[value=\"69\"]");
    $this->type("id=titleen_US", "Test title");
    $this->click("//button[@type='submit']");
    $this->assertEquals("Email: this field is required.", $this->getText("css=label.error"));
    $this->type("id=contactEmail", "test@example.com");
    $this->click("//button[@type='submit']");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("xDescription too short (en_US).", $this->getText("id=flashmessage"));
    $this->type("id=descriptionen_US", "Test description");
    $this->click("//button[@type='submit']");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("xYour listing has been published", $this->getText("id=flashmessage"));
  }
}
?>