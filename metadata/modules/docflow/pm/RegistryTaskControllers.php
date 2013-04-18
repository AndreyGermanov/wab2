<?php
$fields["documentTask"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "DocumentTask",
				"tableClassName" => "DocFlowDocumentTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Задача",
				"editorType" => "WABWindow",
				"title" => "Задача",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "DocumentTask_DocFlowApplication_Docs_"),
		"name" => "taskParent",
		"collection" => "fields",
		"file" => __FILE__
);

$models["RegistryTaskControllers"] = array("metaTitle" => "Контролеры задач",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryTaskControllers",
												   "controller" => "referenceUser",
												   "task" => "documentTask",
												   "firm" => "firm"
);
?>