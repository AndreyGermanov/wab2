<?php
$fields["orderDocument"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentOrder",
																  "tableClassName" => "DocFlowReferenceTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Заказ",
																  "editorType" => "WABWindow",
																  "title" => "Родитель",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@parent IS NOT EXISTS",
																  "parentEntity" => "DocumentOrder_DocFlowApplication_Docs_"),
																  "name" => "orderParent",
																  "collection" => "fields",
																  "file" => __FILE__
);

$fields["referenceOrderCondition"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceOrderConditions",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Состояния заказа",
				"editorType" => "WABWindow",
				"title" => "Состояние заказа",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceOrderConditions_DocFlowApplication_Docs_"),
		"name" => "orderParent",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["RegistryOrderConditions"] = array("title" =>"Регистр состояний заказов",
													"name" => "RegistryOrderConditions",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("orderDocument","referenceOrderCondition")
);

$models["RegistryOrderConditions"] = array("metaTitle" => "Регистр состояний заказов",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryOrderConditions",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "orderDocument" => "orderDocument",
												   "orderCondition" =>"referenceOrderCondition"												   
);
?>