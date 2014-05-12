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
        $this->open(osc_user_login_url());
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

    protected  function _insertItem($parentCat, $cat, $title, $description, $price, $regionId, $cityId, $cityArea, $aPhotos, $user, $email , $logged = 0)
    {
        $this->open( osc_base_url() );

        $this->click("link=Publish your ad for free");
        $this->waitForPageToLoad("10000");

        $this->select("catId", "label=regexp:\\s*$cat");
        sleep(2);
        $this->type("title[en_US]", $title);
        $this->type("description[en_US]", $description);
        $this->type("price", "12".osc_locale_thousands_sep()."34".osc_locale_thousands_sep()."56".osc_locale_dec_point()."78".osc_locale_dec_point()."90");
        $this->fireEvent("price", "blur");
        sleep(2);
        $this->assertTrue($this->getValue("price")=="123456".osc_locale_dec_point()."78", "Check price correction input");
        $this->type("price", $price);
        $this->select("currency", "label=Euro €");
        if($regionId!=NULL) {
            $this->select("countryId", "label=Spain");
            $this->type('id=region', $regionId);
            $this->type('id=city', $cityId);
        }
        if($cityArea==NULL) {
            $this->type("cityArea", "my area");
        } else {
            $this->type("cityArea", $cityArea);
        }
        $this->type("address", "my address");
        if( count($aPhotos) > 0 ) {
            sleep(2);

            $this->type("qqfile", LIB_PATH."simpletest/test/osclass/".$aPhotos[0]);
            for($k=1;$k<count($aPhotos);$k++) {
                sleep(4);
                $this->type("qqfile", LIB_PATH."simpletest/test/osclass/".$aPhotos[$k]);
            }
        }

        $this->type("contactName" , $user);
        $this->type("contactEmail", $email);

        $this->click("//button[text()='Publish']");
        $this->waitForPageToLoad("10000");
    }


}
