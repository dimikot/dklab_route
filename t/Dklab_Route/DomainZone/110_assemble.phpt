--TEST--
Dklab_Route_DomainZone: test assemble() call
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'domain1.com.LGN.dev.example.com', array('*.dev.example.com'));
testAssemble($r, array("app" => "app3", "subDomain" => "my"), 'Dev assemble (app3 -> app3)');

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'domain2.com.LGN.dev.example.com', array('*.dev.example.com'));
testAssemble($r, array("app" => "app3", "subDomain" => "my"), 'Dev assemble (app2 -> app3)');

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'xxx.domain2.com', array());
testAssemble($r, array("app" => "app2", "subDomain" => "my"), 'Dev assemble (app2 -> app2, no zone)');

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'domain33.com', array());
testAssemble($r, array("app" => "app3", "subDomain" => "my"), 'Dev assemble (stay on the same domain)');

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'domain3.com', array());
testAssemble($r, array("app" => "app3", "subDomain" => "my"), 'Dev assemble (stay on the same domain)');


?>
--EXPECT--
Dev assemble (app3 -> app3): 'my.domain3.com.LGN.dev.example.com'
Dev assemble (app2 -> app3): 'my.domain3.com.LGN.dev.example.com'
Dev assemble (app2 -> app2, no zone): 'my.domain2.com'
Dev assemble (stay on the same domain): 'my.domain33.com'
Dev assemble (stay on the same domain): 'my.domain3.com'
