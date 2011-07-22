<?php
require "../src/Kant/Proxy/DataSource/CSV.php";

$csvLoader = new \Kant\Proxy\DataSource\CSV("./files/proxies.csv");
$proxy = $csvLoader->getProxy();

if($proxy) {
	if(!isset($proxy['ip'])) {
		echo "No ip retrieved\n";
		exit;
	}
	if(!isset($proxy['port'])) {
		echo "No Port retrieved\n";
		exit;
	}
} else {
	echo "Unable to load proxy list.\n";
	exit;
}
echo "Proxy successfuly retrieved!\n";
