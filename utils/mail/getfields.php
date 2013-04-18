#!/usr/bin/php5
<?php
#error_reporting(0);
chdir("/opt/WAB2");
require_once 'boot.php';
$address = $argv[1];
$file = $argv[2];
$app = $Objects->get("Application");
$app->systemScript = true;
$app->User = "mail";
$obj = $Objects->get("AddressBookDefaultFields_MailApplication_Mail_100_first");
$defFields = $obj->getFields(); 
foreach($addressbooks as $key=>$value) {
	$obj = $Objects->get("LDAPAddressBook_MailApplication_Mail_100_".$key);
	$fields = $obj->getFields(trim($address));
	if ($fields) {		
		break;
	}
}
$fields = mergeArrays($defFields, $fields);
if (file_exists("/etc/postfix/addrbook/".$address.".data")) {
	$strings = file("/etc/postfix/addrbook/".$address.".data");
	foreach ($strings as $line) {
		$parts = explode(":",$line);
		if (count($parts)==2)
		$fields["{".trim($parts[0])."}"] = trim($parts[1]);
	}
	
}

if (file_exists($file)) {
	echo strtr(file_get_contents($file),$fields);
}
?>