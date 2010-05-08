--TEST--
Dklab_Route_MultiApp: match() test
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

testMatch($multiApp, "http://sdm.domain1.com/items/12345", "Matched");
testMatch($multiApp, "http://xxx.com/items/12345", "App not found");
testMatch($multiApp, "http://zzz.domain1.com/items/12345", "URI not found, but app is OK");
testMatch($multiApp, "http://domain3.com/items/12345", "No router");

?>
--EXPECT--
Matched: array (
  'controller' => 'sdm/catalog_items_one',
  'id' => '12345',
  'name' => 'sdm_catalog_items_one',
  'app' => 'app1',
)
Exception: 'Cannot find an App for host: "xxx.com"'
URI not found, but app is OK: array (
  'app' => 'app1',
)
Exception: 'Cannot find App "app3" in the list of available routers: (app1, app2)'

