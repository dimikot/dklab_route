--TEST--
Dklab_Route_DomainGroup: match with the whole domain name (default app)
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainGroup(array(
    'app1' => array(
        'dev' => 'app1.*.dev.example.com',
        'rel' => 'app1.prod.example.com',
        'prod' => 'domain1.com',
    ),
    'appDef' => array(
        'dev' => 'default.*.dev.example.com',
        'rel' => 'default.prod.example.com',
        'prod' => '',
    )
), 'xxx.ru');

printr($r->match("my.app1.LGN.dev.example.com"), 'Non-default match');
printr($r->match("aaa.ru"), 'Default match');
printr($r->match("xxx.default.LGN.dev.example.com"), 'Default match with mask');


?>
--EXPECT--
Non-default match: array (
  'app' => 'app1',
  'subDomain' => 'my',
  'group' => 'dev',
  'mask' => 'app1.*.dev.example.com',
  'baseDomain' => 'app1.LGN.dev.example.com',
  'matches' => 
  array (
    0 => 'LGN',
  ),
)
Default match: array (
  'app' => 'appDef',
  'subDomain' => 'aaa.ru',
  'group' => 'prod',
  'mask' => '',
  'baseDomain' => '',
  'matches' => 
  array (
  ),
)
Default match with mask: array (
  'app' => 'appDef',
  'subDomain' => 'xxx',
  'group' => 'dev',
  'mask' => 'default.*.dev.example.com',
  'baseDomain' => 'default.LGN.dev.example.com',
  'matches' => 
  array (
    0 => 'LGN',
  ),
)
