--TEST--
Dklab_Route_DomainZone: test match() call with empty zone (production mode)
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainZone($fixtureDomainZone1, 'domain1.com', array());
testMatch($r, "my.domain2.com", 'Non-masked match');
testMatch($r, "domain2.com", 'No subdomain');
testMatch($r, "aaa.com", 'Not found totally');


?>
--EXPECT--
Non-masked match: array (
  'app' => 'app2',
  'subDomain' => 'my',
  'baseDomain' => 'domain2.com',
)
No subdomain: array (
  'app' => 'app2',
  'subDomain' => '',
  'baseDomain' => 'domain2.com',
)
Not found totally: NULL
