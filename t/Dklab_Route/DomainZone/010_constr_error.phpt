--TEST--
Dklab_Route_DomainZone: constructor errors detection
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

try {
    echo "* Test non-existed app\n";
    $dz = new Dklab_Route_DomainZone($fixtureDomainZone1, "ddd.ru.LGN.dev.example.com", array("*.dev.example.com"));
    $dz->assemble(array("app" => "app1", "subDomain" => "x"));
} catch (Exception $e) {
    printr($e->getMessage(), "Exception");
}

try {
    echo "* Test non-existed app\n";
    $dz = new Dklab_Route_DomainZone($fixtureDomainZone1, "zzz.com", array());
    $dz->assemble(array("app" => "app1", "subDomain" => "x"));
} catch (Exception $e) {
    printr($e->getMessage(), "Exception");
}


?>
--EXPECT--
* Test non-existed app
Exception: 'Cannot find a match for the current domain: "ddd.ru.LGN.dev.example.com", zone ".LGN.dev.example.com"'
* Test non-existed app
Exception: 'Cannot find a match for the current domain: "zzz.com", zone ""'

