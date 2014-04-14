<?php
require_once dirname(dirname(__FILE__)).'/OsclassTestFrontend.php';
class TestItems extends OsclassTestFrontend
{

    public function testNoUser()
    {

        osc_set_preference('enabled_users', true);
        osc_set_preference('enabled_user_registration', true);
        osc_set_preference('enabled_user_validation', true);

    }

}
?>