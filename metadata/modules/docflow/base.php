<?php

$fields["documentTable"] = array("type" => "text",
		"params" => array("type" => "text", "hide" => "true",
				"title" => "Табличная часть документа"),
		"name" => "documentTable",
		"file" => __FILE__,
		"collection" => "fields"		
);

$groups["docflow"] = array("title" => "Документооборот",
						   "file" => __FILE__,
						   "name" => "docflow",
						   "collection" => "groups",
						   "groups" => array("docflowMedic","docflowCommon"),
						   "fields" => array("documentTable")
);


$modelGroups["docflow"] = array("title" => "Документооборот",
		"name" => "docflow",
		"file" => __FILE__,
		"collection" => "modelGroups",
		"groups" => array("docflowMedic","docflowCommon"),
		"fields" => array("Document","Reference")
);

$models["Document"] = array("title" => "Документ",
							 "file" => __FILE__,
							 "name" => "Document",
							 "collection" => "models",
							 "number" => "number",
							 "name" => "systemName",
							 "docDate" => "docDate",
							 "registered" => "registered",
							 "modifications" => "modifications"							 
);

$models["documentBase"] =
array
(
		"file" => __FILE__,
		"groups" => array
		(
				"base" => "base"
		),
		"number" => "number",
		"docDate" => "docDate",
		"registered" => "registered",
		"modifications" => "modifications",
		"metaTitle" => "Базовые поля документа"
);

$models["referenceBase"] =
array
(
		"name" => "systemName",
		"file" => __FILE__,
		"metaTitle" => "Базовые поля справочника"
);

$groups["Document"] =
array
(
		"file" => __FILE__,
		"groups" => array
		(

		),
		"fields" => array
		(
				"docDate",
				"registered",
				"number"
		),
		"title" => "Документ"
);

$fields["number"] =
array
(
		"type" => "integer",
		"params" => array
		(
				"type" => "integer",
				"title" => "Номер"
		),
		"file" => __FILE__
);

$fields["docDate"] =
array
(
		"type" => "integer",
		"params" => array
		(
				"type" => "date",
				"title" => "Дата"
		),
		"file" => __FILE__
);

$fields["registered"] =
array
(
		"type" => "boolean",
		"params" => array
		(
				"type" => "boolean",
				"title" => "Проведен",
				"hide" => "true"
		),
		"file" => __FILE__
);

$fields["deletionMark"] =
array
(
		"type" => "boolean",
		"params" => array
		(
				"type" => "boolean",
				"title" => "Метка удаления",
				"hide" => "true"
		),
		"file" => __FILE__
);
?>