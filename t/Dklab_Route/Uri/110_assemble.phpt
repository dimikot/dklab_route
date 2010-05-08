--TEST--
Dklab_Route_Uri: assemble() test
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_Uri($fixtureUri1);
testAssemble($r, null, "NULL");
testAssemble($r, array('abc' => 'def'), "Bad");
testAssemble($r, array('name' => 'sdm_catalog_items_one'), "No ID param");
testAssemble($r, array('name' => 'notnexistent'), "Not found");
testAssemble($r, array('name' => 'sdm_catalog_items_one', 'id' => 12345), "OK1");
testAssemble($r, array('name' => 'sdm_catalog_items_one', 'id' => array()), "Wrong type");


?>
--EXPECT--
NULL - exception: 'No \'name\' parameter found'
Bad - exception: 'No \'name\' parameter found'
No ID param - exception: 'No parameter \'id\' found in parsed URL (name  \'sdm_catalog_items_one\')'
Not found - exception: 'No URL map item \'notnexistent\' found'
OK1: 'sdm/items/12345/'
Wrong type - exception: 'Parameter \'id\' must be scalar, given: array (
)\' (name  \'sdm_catalog_items_one\')'

