<?php
$fields["changeTaskConditionParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "DocumentChangeTaskCondition",
				"tableClassName" => "DocFlowDocumentTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Изменение состояния задачи",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "DocumentChangeTaskCondition_DocFlowApplication_Docs_"),
		"name" => "changeTaskConditionParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["taskDocument"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentTask",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Задача",
																  "editorType" => "WABWindow",
																  "title" => "Задача",
																  "fieldList" => "title Описание проекта",
																  "sortOrder" => "docDate DESC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
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
				"sortOrder" => "orderNumber ASC",
				"width" => "100%",
				"condition" => "@parent IS NOT EXISTS",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceTaskConditions_DocFlowApplication_Docs_"),
		"name" => "referenceTaskCondition",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["DocumentChangeTaskCondition"] = array("title" =>"Изменение состояния задачи",
													"name" => "DocumentChangeTaskCondition",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("taskDocument","referenceTaskCondition")
);

$models["DocumentChangeTaskCondition"] = array("metaTitle" => "Изменение состояния задачи",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentChangeTaskCondition",
												   "parent" => "changeTaskConditionParent",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "taskDocument" => "taskDocument",
												   "taskCondition" =>"referenceTaskCondition",
												   "manager" => "referenceUser"												   
);
?>