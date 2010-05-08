--TEST--
Dklab_Route_DomainGroup: constructor errors detection
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

try {
	echo "* Test invalid group names\n";
	new Dklab_Route_DomainGroup(array(
		'app1' => array(
			'domain1.com',
			'app1.prod.example.com',
			'app1.*.dev.example.com',
		), 
	), 'domain1.com');
} catch (Exception $e) {
	printr($e->getMessage(), "Exception");
}


try {
	echo "* Test invalid group set\n";
	new Dklab_Route_DomainGroup(array(
		'app1' => array(
			'prod' => 'domain1.com',
			'rel' => 'app1.prod.example.com',
		), 
		'app2' => array(
			'dev' => 'app1.dev.example.com',
			'prod' => 'domain2.com',
		), 
	), 'domain1.com');
} catch (Exception $e) {
	printr($e->getMessage(), "Exception");
}


try {
	echo "* Test OK group set\n";
	new Dklab_Route_DomainGroup(array(
		'app1' => array(
			'prod' => 'domain1.com',
			'rel' => 'app1.prod.example.com',
		), 
		'app2' => array(
			'rel' => 'app1.prod.example.com',
			'prod' => 'domain2.com',
		), 
	), 'domain1.com');
} catch (Exception $e) {
	printr($e->getMessage(), "Exception");
}


try {
	echo "* Test no current domain found\n";
	new Dklab_Route_DomainGroup(array(
		'app1' => array(
			'prod' => 'domain1.com',
			'rel' => 'app1.prod.example.com',
		), 
	), 'aaa.com');
} catch (Exception $e) {
	printr($e->getMessage(), "Exception");
}


?>
--EXPECT--
* Test invalid group names
Exception: 'DomainGroup name must be alphanumeric, "0" given for app app1'
* Test invalid group set
Exception: 'App app2 contains different groups set than app app1: (dev, prod) != (prod, rel)'
* Test OK group set
* Test no current domain found
Exception: 'Cannot find a match for the current domain: "aaa.com". It is needed for group detection.'
