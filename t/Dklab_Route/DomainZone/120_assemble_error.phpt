--TEST--
Dklab_Route_DomainZone: assemble errors detection
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'domain3.com.LGN.dev.example.com', array('*.dev.example.com'));
testAssemble($r, array("app" => "none", "subDomain" => "my"), 'No such app');

?>
--EXPECT--
No such app - exception: 'No such app: none, available are: (app1, app2, app3)'
