<?php
define('TEST_IMAGE_PATH', dirname(__FILE__));

class OsclassTest extends PHPUnit_Extensions_SeleniumTestCase
{

    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = TEST_IMAGE_PATH;
    protected $screenshotUrl = 'http://localhost/screenshots';


  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://vps.madriguera.me/testing/Osclass");
  }


}
?>