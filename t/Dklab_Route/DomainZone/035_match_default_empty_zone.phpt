--TEST--
Dklab_Route_DomainZone: match the whole domain with empty zone
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainZone(
    array(
        'app1' => array(
            'domain.com',
        ),
        'appDef' => array(
            '',
        ),
    ),
    "xxx.ru",
    array()
);

testMatch($r, "my.domain.com", 'Non-default match');
testMatch($r, "aaa.ru", 'Default match');


?>
--EXPECT--
Non-default match: array (
  'app' => 'app1',
  'subDomain' => 'my',
  'baseDomain' => 'domain.com',
)
Default match: array (
  'app' => 'appDef',
  'subDomain' => 'aaa.ru',
  'baseDomain' => '',
)
