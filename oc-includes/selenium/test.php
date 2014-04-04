<?php
class Example extends PHPUnit_Extensions_Selenium2TestCase
{
  protected function setUp()
  {
    //$this->setBrowser("*firefox");
    $this->setBrowserUrl("http://madriguera.me/osclass/Osclass");
  }

  public function testMyTestCase()
  {
    $this->open("/osclass/Osclass/");
    $this->click("link=Publish your ad for free");
    $this->waitForPageToLoad("30000");
    $this->select("id=catId", "label=regexp:\\s+Art - Collectibles");
    $this->type("id=titleen_US", "me cago en");
    $this->type("id=descriptionen_US", "selenium!! mother fucker!");
    $this->click("//button[@type='submit']");
    $this->verifyText("css=label.error", "Email: this field is required.ffff");
  }
}
?>