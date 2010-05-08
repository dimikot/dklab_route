--TEST--
Dklab_Route_DomainZone: test match() call with non-empty zone
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'domain1.com.LGN.dev.example.com', array('*.dev.example.com', 'test.example.com'));
testMatch($r, "my.domain2.com.LGN.dev.example.com", 'Match');
testMatch($r, "my.domain2.com---ABC.dev.example.com", 'Not found');
testMatch($r, "domain2.com.LGN.dev.example.com", 'No subdomain');
testMatch($r, "aaa.com.LGN.dev.example.com", 'Not found totally');

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'domain1.com.test.example.com', array('*.dev.example.com', 'test.example.com'));
testMatch($r, "my.domain2.com.test.example.com", 'Match by test zone');


?>
--EXPECT--
Match: array (
  'app' => 'app2',
  'subDomain' => 'my',
  'baseDomain' => 'domain2.com.LGN.dev.example.com',
)
Not found: NULL
No subdomain: array (
  'app' => 'app2',
  'subDomain' => '',
  'baseDomain' => 'domain2.com.LGN.dev.example.com',
)
Not found totally: NULL
Match by test zone: array (
  'app' => 'app2',
  'subDomain' => 'my',
  'baseDomain' => 'domain2.com.test.example.com',
)
