<?php
require_once $_SERVER["DOCUMENT_ROOT"].'/boot.php';
$app = $Objects->get("Application");
if (!$app->initiated)
	$app->initModules();
$app->raiseRemoteEvent("SCAN_CODE","o=".@$_GET[o].",i=".@$_GET[i],$app->User,array($app->User => $app->User));