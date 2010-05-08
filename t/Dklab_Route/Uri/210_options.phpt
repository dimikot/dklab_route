--TEST--
Dklab_Route_Uri: getOption() test
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_Uri($fixtureUri1);
echo $r->getOption("some_option");

?>
--EXPECT--
some_value
