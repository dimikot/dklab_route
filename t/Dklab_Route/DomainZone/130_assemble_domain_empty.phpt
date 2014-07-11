--TEST--
Dklab_Route_DomainZone: test assemble() call for empty (default) domain
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainZone(
    array(
        "appCur" => array("cur"),
        "app1" => array("")
    ),
    'cur.dev.example.com',
    array('dev.example.com')
);
testAssemble($r, array("app" => "app1", "subDomain" => "my.xyz.ru"), 'Default domain assemble');

// Bugfix test: 'my.xyz.ru..dev.example.com' returned before.
?>
--EXPECT--
Default domain assemble: 'my.xyz.ru.dev.example.com'