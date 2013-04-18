<?php
$fields["projectReference"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "ReferenceProjects",
																  "tableClassName" => "DocFlowReferenceTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Заказ",
																  "editorType" => "WABWindow",
																  "title" => "Проект",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@parent IS NOT EXISTS",
																  "parentEntity" => "ReferenceProjects_DocFlowApplication_Docs_"),
																  "name" => "projectReference",
																  "collection" => "fields",
																  "file" => __FILE__
);

$fields["referenceProjectCondition"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceProjectConditions",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Состояния проекта",
				"editorType" => "WABWindow",
				"title" => "Состояние проекта",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceProjectConditions_DocFlowApplication_Docs_"),
		"name" => "referenceProjectCondition",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["RegistryProjectConditions"] = array("title" =>"Регистр состояний проектов",
													"name" => "RegistryProjectConditions",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("projectReference","referenceProjectCondition")
);

$models["RegistryProjectConditions"] = array("metaTitle" => "Регистр состояний проектов",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryProjectConditions",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "projectReference" => "projectReference",
												   "projectCondition" =>"referenceProjectCondition"												   
);
?>