--TEST--
Dklab_Route_DomainGroup: test match() call
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainGroup($fixtureDomainGroup1, 'app3.LGN.dev.example.com');
testMatch($r, "my.domain2.com", 'Non-masked match');
testMatch($r, "my.app1.ABC.dev.example.com", 'Masked match');
testMatch($r, "my.app2---ABC.dev.example.com", 'Not found');
testMatch($r, "domain2.com", 'No subdomain');
testMatch($r, "aaa.com", 'Not found totally');


?>
--EXPECT--
Non-masked match: array (
  'app' => 'app2',
  'subDomain' => 'my',
  'group' => 'prod',
  'mask' => 'domain2.com',
  'baseDomain' => 'domain2.com',
  'matches' => 
  array (
  ),
)
Masked match: array (
  'app' => 'app1',
  'subDomain' => 'my',
  'group' => 'dev',
  'mask' => 'app1.*.dev.example.com',
  'baseDomain' => 'app1.ABC.dev.example.com',
  'matches' => 
  array (
    0 => 'ABC',
  ),
)
Not found: NULL
No subdomain: array (
  'app' => 'app2',
  'subDomain' => '',
  'group' => 'prod',
  'mask' => 'domain2.com',
  'baseDomain' => 'domain2.com',
  'matches' => 
  array (
  ),
)
Not found totally: NULL
