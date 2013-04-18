<?php
$fields["taskParent"] = array("type" => "entity",
 											   "params" => array("type" => "entity",
										 						  "className" => "DocumentTask",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Задача",
																  "editorType" => "WABWindow",
																  "title" => "Родитель",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
																  "parentEntity" => "DocumentTask_DocFlowApplication_Docs_"),
											  "name" => "taskParent",
											  "collection" => "fields",
											  "file" => __FILE__
);

$fields["parentTask"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "DocumentTask",
				"tableClassName" => "DocFlowDocumentTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Задача",
				"editorType" => "WABWindow",
				"title" => "Родительская задача",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "DocumentTask_DocFlowApplication_Docs_"),
		"name" => "parentTask",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["project"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceProjects",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Проект",
				"editorType" => "WABWindow",
				"title" => "Проект",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceProjects_DocFlowApplication_Docs_"),
		"name" => "project",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["dateStart"] = array("name" => "dateStart", "collection" => "fields", "base" => "dateField", "params" => array("title" => "Дата начала"));

$fields["dateEnd"] = array("name" => "dateEnd", "collection" => "fields", "base" => "dateField", "params" => array("title" => "Дата окончания"));

$fields["completed"] = array("name" => "completed", "collection" => "fields", "base" => "integerField", "params" => array("title" => "Процент завершения"));

$fields["manager"] = array("name" => "manager", "collection" => "fields", "base" => "referenceUser", "params" => array("title" => "Менеджер"));

$fields["taskManager"] = array("name" => "taskManager", "collection" => "fields", "base" => "referenceUser", "params" => array("title" => "Постановщик"));

$fields["worker"] = array("name" => "worker", "collection" => "fields", "base" => "referenceUser", "params" => array("title" => "Исполнитель"));

$fields["taskDescription"] = array("base" => "textField",
		"params" => array("title" => "Описание задачи", "width" => "100%", "height" => "100%", "control_type" => "tinyMCE")
);

$models["DocumentTask"] = array("metaTitle" => "Задачи",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentTask",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "parent" => "taskParent",
												   "workObject" => "entityObject",
												   "dateStart" => "dateStart",
												   "dateEnd" => "dateEnd",
												   "firm" => "firm",
												   "department" => "department",
												   "project" => "project",
												   "parentTask" => "parentTask",
												   "description" => "taskDescription",
												   "manager" => "taskManager",
												   "worker" => "worker",
												   "completed" => "completed"
);
?>