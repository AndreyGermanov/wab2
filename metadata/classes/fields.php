<?php
$fields["wabBackgroundColor"] = 
array
(
	"base" => "stringField",
	"file" => __FILE__,
	"params" => array
	(
		"title" => "Цвет фона системы управления"
	)
);

$fields["showInfoPanel"] =
array
(
		"base" => "booleanField",
		"file" => __FILE__,
		"params" => array
		(
				"title" => "Показывать информационную панель"
		)
);

$groups["classes"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"ApacheUser",
		"FileServer",
		"DhcpServer",
		"SystemSettings",
		"WABEntity",
		"Document",
		"docflow"
	),
	"fields" => array
	(

	),
	"title" => "Поля классов объектов"
);

$groups["ApacheUser"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(

	),
	"fields" => array
	(
		"wabBackgroundColor"
	),
	"title" => "Пользователь панели управления"
);
?>