<?php
require_once dirname(dirname(__FILE__)).'/OsclassTestFrontend.php';
class TestItems extends OsclassTestFrontend
{

    public function testNoUser()
    {

        osc_set_preference('items_wait_time', 0);
        osc_set_preference('selectable_parent_categories', 1);
        osc_set_preference('reg_user_post', 0);
        osc_set_preference('moderate_items', -1);

        include TEST_ABS_PATH . 'assets/ItemData.php';
        $this->insertItem($item['parentCatId'], $item['catId'], $item['title'],
            $item['description'], $item['price'],
            $item['regionId'], $item['cityId'], $item['cityArea'],
            $item['photo'], $item['contactName'],
            $this->_email);
        $this->assertTrue($this->isTextPresent("Your listing has been published"),"Items, insert item, no user, no validation.") ;

        osc_set_preference('moderate_items', 111);
        $this->insertItem($item['parentCatId'], $item['catId'], $item['title'],
            $item['description'], $item['price'],
            $item['regionId'], $item['cityId'], $item['cityArea'],
            $item['photo'], $item['contactName'],
            $this->_email);
        $this->assertTrue($this->isTextPresent("Check your inbox"),"Items, insert item, no user, with validation.") ;

        osc_set_preference('reg_user_post', 1);
        $this->open( osc_base_url() );
        $this->click("link=Publish your ad for free");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent("Only registered users are allowed to post listings"), "Items, insert item, no user, can't publish");


        $aItem = Item::newInstance()->listAll('s_contact_email = '.$this->_email." AND fk_i_user IS NULL");
        foreach($aItem as $item){
            $url = osc_item_delete_url( $item['s_secret'] , $item['pk_i_id'] );
            $this->open( $url );
            $this->assertTrue($this->isTextPresent("Your listing has been deleted"), "Delete item.");
        }
    }

}
?>