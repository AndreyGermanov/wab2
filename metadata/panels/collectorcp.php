<?php
$panels["collector"] = 
array
(
	"name" => "collector",
	"collection" => "panels",
	"file" => __FILE__,
	"modules" => array
	(
		"MystixCollectorMX" => $modules["MystixCollectorMX"]
	),
	"metaTitle" => "Почтовый сервер",
	"defaultModule" => "MystixCollectorMX"
);
?>