<?php
$modelGroups["modules"] = 
array
(
	"fields" => array
	(
		"MystixController",
		"MystixCollectorMX",
		"MystixSpiderman",
		"DocFlowApplication"
	),
	"file" => __FILE__,
	"metaTitle" => "Модули",
	"groups" => array
	(

	)
);

$models["entityBase"] = 
array
(
	"dateCreated" => "dateCreated",
	"modifications" => "modifications",
	"user" => "authorName",
	"deleted" => "deletionMark",
	"file" => __FILE__,
	"metaTitle" => "Базовые поля сущности"
);

$modelGroups["base"] =
array
(
		"file" => __FILE__,
		"groups" => array
		(

		),
		"fields" => array
		(
				"entityBase",
				"appconfig",
				"documentBase",
				"referenceBase",
				"humanInfo"
		),
		"title" => "Базовые"
);
?>