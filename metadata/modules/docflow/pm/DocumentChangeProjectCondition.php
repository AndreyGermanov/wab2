<?php
$fields["changeProjectConditionParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "DocumentChangeProjectCondition",
				"tableClassName" => "DocFlowDocumentTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Изменение состояния проекта",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "DocumentChangeProjectCondition_DocFlowApplication_Docs_"),
		"name" => "changeProjectConditionParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["projectReference"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "ReferenceProjects",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Заказ",
																  "editorType" => "WABWindow",
																  "title" => "Проект",
																  "fieldList" => "title Описание проекта",
																  "sortOrder" => "docDate DESC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
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
				"sortOrder" => "orderNumber ASC",
				"width" => "100%",
				"condition" => "@parent IS NOT EXISTS",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceProjectConditions_DocFlowApplication_Docs_"),
		"name" => "referenceProjectCondition",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["DocumentChangeProjectCondition"] = array("title" =>"Изменение состояния проекта",
													"name" => "DocumentChangeProjectCondition",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("projectReference","referenceProjectCondition")
);

$models["DocumentChangeProjectCondition"] = array("metaTitle" => "Изменение состояния проекта",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentChangeProjectCondition",
												   "parent" => "changeProjectConditionParent",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "projectReference" => "projectReference",
												   "projectCondition" =>"referenceProjectCondition",
												   "manager" => "referenceUser"												   
);
?>