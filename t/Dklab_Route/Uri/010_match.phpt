--TEST--
Dklab_Route_Uri: match() test
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_Uri($fixtureUri1);
testMatch($r, "sdm/items/123456", "Simple URL");
testMatch($r, "sdm/notfound", "Not found");
testMatch($r, "/app/123", "OK1");
testMatch($r, "/is/a/very/123/url", "Long1");
testMatch($r, "/is/a/very/long123/url", "Long2");


?>
--EXPECT--
Simple URL: array (
  'controller' => 'sdm/catalog_items_one',
  'id' => '123456',
  'name' => 'sdm_catalog_items_one',
)
Not found: NULL
OK1: array (
  'controller' => 'app',
  'id' => '123',
  'name' => 'test_app',
)
Long1: array (
  'controller' => 'app',
  'long' => '123',
  'name' => 'test_long',
)
Long2: array (
  'controller' => 'app',
  'long' => 'long123',
  'name' => 'test_long',
)
