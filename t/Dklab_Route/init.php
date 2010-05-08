<?php
header("Content-type: text/plain");
chdir(dirname(__FILE__));
error_reporting(E_ALL);
ini_set('track_errors', 1);

include_once "../../lib/config.php";
include_once "Dklab/Route/DomainGroup.php";
include_once "Dklab/Route/DomainZone.php";
include_once "Dklab/Route/MultiApp.php";
include_once "Dklab/Route/Uri.php";

$fixtureDomainGroup1 = array(
	'app1' => array(
		'prod' => 'domain1.com',
		'rel' => 'app1.prod.example.com',
		'dev' => 'app1.*.dev.example.com'
	),
	'app2' => array(
		'prod' => 'domain2.com',
		'rel' => 'app2.prod.example.com',
		'dev' => 'app2.*.dev.example.com'
	),
	'app3' => array(
		'prod' => array('domain3.com', 'domain33.com'),
		'rel' => 'app3.prod.example.com',
		'dev' => 'app3.*.dev.example.com'
	),
);

$fixtureDomainZone1 = array(
	'app1' => array(
		'domain1.com',
	),
	'app2' => array(
		'domain2.com',
	),
	'app3' => array(
		'domain3.com', 
		'domain33.com',
	),
);

$fixtureUri1File = dirname(__FILE__) . '/fixture/routes.ini';
$fixtureUri1 = parse_ini_file($fixtureUri1File, true);


function testMatch($router, $data, $comment)
{
	try {
		printr($router->match($data), $comment);
	} catch (Exception $e) {
		printr($e->getMessage(), "Exception");
	}
}

function testAssemble($router, $data, $comment)
{
	try {
		printr($router->assemble($data), $comment);
	} catch (Exception $e) {
		printr($e->getMessage(), "$comment - exception");
	}
}

function printr($value, $comment=null)
{
    if ($comment !== null) echo "$comment: ";
    var_export($value);
    echo "\n";
}
