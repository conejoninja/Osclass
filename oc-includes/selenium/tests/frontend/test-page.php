<?php
require_once dirname(dirname(__FILE__)).'/OsclassTestFrontend.php';
class TestPage extends OsclassTestFrontend
{

    public function testVisitPage()
    {

        $aFields = array( 's_internal_name' => 'internal name'
        , 'b_indelible' => 0
        , 'b_link' => 1);

        $aFieldsDescription = array();
        $aFieldsDescription['en_US']['s_title'] = 'TITLE NEW PAGE';
        $aFieldsDescription['en_US']['s_text'] = 'TEXT<br> TEST PAGE <p>end</p>';

        $mPage = new Page();
        if( $mPage->insert($aFields, $aFieldsDescription) ){
            $page = $mPage->findByInternalName('internal name');
            $pageId =  $page['pk_i_id'];

            // go directly with url
            View::newInstance()->_exportVariableToView('page', $page);
            $url_page = osc_static_page_url();
            $this->open($url_page);
            $this->waitForPageToLoad("30000");
            $this->assertTrue( $this->isTextPresent("TITLE NEW PAGE"));

            // go through footer
            $this->open( osc_base_url() );
            $this->click("link=TITLE NEW PAGE");
            $this->waitForPageToLoad("30000");
            $this->assertTrue( $this->isTextPresent("TITLE NEW PAGE"));

            // delete page
            if( Page::newInstance()->deleteByPrimaryKey($pageId) ){
                $this->assertTrue( true , "Delete page.");
            } else {
                $this->assertTrue( false , "Delete page.");
            }
        } else {
            $this->assertTrue( false , "Insert new page.");
        }

    }

}
?>