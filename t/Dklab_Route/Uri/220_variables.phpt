--TEST--
Dklab_Route_Uri: variables substitution test
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_Uri(
    dirname(__FILE__) . '/../fixture/routes_vars.ini',
    array('VAR' => 'v1', 'OTHER_VAR' => 'v2')
);
echo $r->getOption("some_option") . "\n";
echo $r->assemble(array('name' => 'sdm_index')) . "\n";
echo $r->assemble(array('name' => 'bad')) . "\n";

?>
--EXPECT--
some_value v1 other_value
sdm_v2/
sdm_?/
