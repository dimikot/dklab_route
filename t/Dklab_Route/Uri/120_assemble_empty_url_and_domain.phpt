--TEST--
Dklab_Route_Uri: root URL must return empty string
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_Uri($fixtureUri1);
testAssemble($r, array('name' => 'test_root'), "Root URL");

?>
--EXPECT--
Root URL: ''
