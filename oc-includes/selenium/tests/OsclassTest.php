<?php

define('TEST_ABS_PATH', dirname(__FILE__) . '/');
require_once TEST_ABS_PATH . '/config.php';

class OsclassTest extends PHPUnit_Extensions_SeleniumTestCase
{

    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = TEST_IMAGE_PATH;
    protected $screenshotUrl = TEST_IMAGE_URL;


  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl(TEST_SERVER_URL);
  }


}
