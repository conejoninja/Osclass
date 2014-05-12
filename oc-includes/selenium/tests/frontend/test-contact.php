<?php
require_once dirname(dirname(__FILE__)).'/OsclassTestFrontend.php';
class TestContact extends OsclassTestFrontend
{

    public function testContactWeb()
    {

        $this->open(osc_base_url());
        $this->click("link=Contact");
        $this->waitForPageToLoad("30000");
        $this->click("//button[@type='submit']");
        $this->assertEquals("Email: this field is required..", $this->getText("css=label.error"));
        $this->assertEquals("Message: this field is required.", $this->getText("//ul[@id='error_list']/li[2]/label"));
        $this->type("id=yourName", "Some name");
        $this->keyUp("id=yourName", "a");
        $this->type("id=yourEmail", "user.example.com");
        $this->keyUp("id=yourEmail", "a");
        $this->type("id=subject", "Some subject");
        $this->keyUp("id=subject", "a");
        $this->type("id=message", "Some message");
        $this->keyUp("id=message", "a");
        $this->assertEquals("Invalid email address.", $this->getText("css=label.error"));
        $this->type("id=yourEmail", "user@example.com");
        $this->keyUp("id=yourEmail", "a");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("xYour email has been sent properly. Thank you for contacting us!", $this->getText("id=flashmessage"));

    }

}
?>