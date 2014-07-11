--TEST--
Dklab_Route_DomainZone: test match() call with longest domain name
--FILE--
<?php
require dirname(__FILE__) . '/../init.php';

$r = new Dklab_Route_DomainZone(
    array(
        'app1' => array(
            'domain1.com',
        ),
        'app2' => array(
            'test.domain1.com',
        ),
        'app3' => array(
            'test.domain3.com',
            'domain3.com',
        ), 
    ),
    'domain1.com.LGN.dev.example.com', 
    array('*.dev.example.com', 'test.example.com')
);
testMatch($r, "my.test.domain1.com.LGN.dev.example.com", 'Match');
testMatch($r, "my.test.domain3.com.LGN.dev.example.com", 'Match');


?>
--EXPECT--
Match: array (
  'app' => 'app2',
  'subDomain' => 'my',
  'baseDomain' => 'test.domain1.com.LGN.dev.example.com',
)
Match: array (
  'app' => 'app3',
  'subDomain' => 'my',
  'baseDomain' => 'test.domain3.com.LGN.dev.example.com',
)
