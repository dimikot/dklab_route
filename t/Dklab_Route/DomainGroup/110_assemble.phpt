--TEST--
Dklab_Route_DomainGroup: test assemble() call
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainGroup($fixtureDomainGroup1, 'app3.LGN.dev.example.com');
testAssemble($r, array("app" => "app3", "subDomain" => "my"), 'Dev assemble (app3 -> app3)');

$r = new Dklab_Route_DomainGroup($fixtureDomainGroup1, 'app2.LGN.dev.example.com');
testAssemble($r, array("app" => "app3", "subDomain" => "my"), 'Dev assemble (app2 -> app3)');

$r = new Dklab_Route_DomainGroup($fixtureDomainGroup1, 'xxx.domain2.com');
testAssemble($r, array("app" => "app2", "subDomain" => "my"), 'Dev assemble (app2 -> app2, no mask)');

$r = new Dklab_Route_DomainGroup($fixtureDomainGroup1, 'domain33.com');
testAssemble($r, array("app" => "app3", "subDomain" => "my"), 'Dev assemble (stay on the same domain)');

$r = new Dklab_Route_DomainGroup($fixtureDomainGroup1, 'domain3.com');
testAssemble($r, array("app" => "app3", "subDomain" => "my"), 'Dev assemble (stay on the same domain)');


?>
--EXPECT--
Dev assemble (app3 -> app3): 'my.app3.LGN.dev.example.com'
Dev assemble (app2 -> app3): 'my.app3.LGN.dev.example.com'
Dev assemble (app2 -> app2, no mask): 'my.domain2.com'
Dev assemble (stay on the same domain): 'my.domain33.com'
Dev assemble (stay on the same domain): 'my.domain3.com'
