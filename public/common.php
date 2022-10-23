<?php

require_once(__DIR__ . "/../vendor/autoload.php");


$lines = file(__DIR__ . "/../.env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $l) {
	if (strpos(trim($l), "#") === 0) {
		continue;
	}
	$pair = explode("=", $l);
	if (count($pair) == 2) {
		putenv(trim($l));
		$_ENV[trim($pair[0])] = trim($pair[1]);
	}
}
$unifi_connection = new UniFi_API\Client($_ENV["CONTROLLER_USER"], $_ENV["CONTROLLER_PASSWORD"], $_ENV["CONTROLLER_URL"]);
$login = $unifi_connection->login();
$site = $unifi_connection->set_site($_ENV["CONTROLLER_SITE"]);