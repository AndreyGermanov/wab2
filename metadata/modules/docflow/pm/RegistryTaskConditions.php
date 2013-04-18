<?php
$fields["taskDocument"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentTask",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Задача",
																  "editorType" => "WABWindow",
																  "title" => "Задача",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@parent IS NOT EXISTS",
																  "parentEntity" => "DocumentTask_DocFlowApplication_Docs_"),
																  "name" => "taskDocument",
																  "collection" => "fields",
																  "file" => __FILE__
);

$fields["referenceTaskCondition"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceTaskConditions",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Состояния задачи",
				"editorType" => "WABWindow",
				"title" => "Состояние задачи",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceTaskConditions_DocFlowApplication_Docs_"),
		"name" => "referenceTaskCondition",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["RegistryTaskConditions"] = array("title" =>"Регистр состояний задач",
													"name" => "RegistryTaskConditions",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("taskDocument","referenceTaskCondition")
);

$models["RegistryTaskConditions"] = array("metaTitle" => "Регистр состояний задач",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryTaskConditions",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "taskDocument" => "taskDocument",
												   "taskCondition" =>"referenceTaskCondition"												   
);
?>