<?php
error_reporting(0);
require_once $_SERVER["DOCUMENT_ROOT"].'/boot.php';
if (isset($_GET["o"]))
	$object_class = $bcobjects[$_GET["o"]];
else {
	$adapter = $Objects->get("DocFlowDataAdapter_DocFlowApplication_Docs_1");
	$adapter->connect();
	if ($adapter->connected) {
		$stmt = $adapter->dbh->prepare("SELECT classname FROM dbEntity WHERE id=".@$_GET["i"]);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($result and count($result)>0) {
			$object_class = $result[0]["classname"];
		}
	}
}
$obj = $Objects->get($object_class."_DocFlowApplication_Docs_");
if (is_object($obj)) {
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>ЛВА Конструктор Web-приложений 2 (версия <?=trim(file_get_contents('/etc/WAB2/config/version'))?>)</title>
    </head>
    <body style="margin-top:0px;margin-left:0px;margin-right:0px;margin-bottom:0px">
    <?
        require_once "scripts.php";
	$obj->getBarCode(@$_GET);
}
?>
</body></html>