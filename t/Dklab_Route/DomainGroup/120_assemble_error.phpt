--TEST--
Dklab_Route_DomainGroup: assemble errors detection
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainGroup($fixtureDomainGroup1, 'app3.LGN.dev.example.com');
testAssemble($r, array("app" => "none", "subDomain" => "my"), 'No such app');

?>
--EXPECT--
No such app - exception: 'No such app: none, available are: (app1, app2, app3)'
