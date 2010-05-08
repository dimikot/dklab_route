--TEST--
Dklab_Route_DomainZone: match the whole domain with non-empty zone
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
	"domain.com.LGN.dev.example.com",
	array("*.dev.example.com")
);

testMatch($r, "my.domain.com.LGN.dev.example.com", 'Non-default match');
testMatch($r, "aaa.ru.LGN.dev.example.com", 'Default match');


?>
--EXPECT--
Non-default match: array (
  'app' => 'app1',
  'subDomain' => 'my',
  'baseDomain' => 'domain.com.LGN.dev.example.com',
)
Default match: array (
  'app' => 'appDef',
  'subDomain' => 'aaa.ru',
  'baseDomain' => 'LGN.dev.example.com',
)
