--TEST--
Dklab_Route_MultiApp: assemble() test
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$multiApp = new Dklab_Route_MultiApp(
    new Dklab_Route_DomainZone($fixtureDomainZone1, "domain1.com", array()),
    array(
        'app1' => new Dklab_Route_Uri($fixtureUri1File),
        'app2' => new Dklab_Route_Uri($fixtureUri1File),
    )
);

testAssemble($multiApp, array(), "No app");
testAssemble($multiApp, array('app' => 'none'), "Bad app");
testAssemble($multiApp, array('app' => 'app1'), "No route name");
testAssemble($multiApp, array('app' => 'app1', 'name' => 'none'), "Bad route name");
testAssemble($multiApp, array('app' => 'app1', 'name' => 'sdm_catalog_items_one', 'id' => '1234'), "OK");

?>
--EXPECT--
No app - exception: 'Key "app" is required at Dklab_Route_MultiApp::assemble()'
Bad app - exception: 'Cannot find App "none" in the list of available routers: (app1, app2)'
No route name - exception: 'No \'name\' parameter found'
Bad route name - exception: 'No URL map item \'none\' found'
OK: '//sdm.domain1.com/items/1234/'

