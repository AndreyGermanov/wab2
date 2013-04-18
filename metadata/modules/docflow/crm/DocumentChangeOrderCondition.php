<?php
$fields["changeOrderConditionParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "DocumentChangeOrderCondition",
				"tableClassName" => "DocFlowDocumentTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Изменение состояния заказа",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "DocumentChangeOrderCondition_DocFlowApplication_Docs_"),
		"name" => "orderParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["orderDocument"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentOrder",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Заказ",
																  "editorType" => "WABWindow",
																  "title" => "Заказ",
																  "fieldList" => "docDate Дата~number Номер~contragent.title AS contragent Контрагент~orderCondition Состояние заказа~title Описание заказа~orderSumma Сумма",
																  "sortOrder" => "docDate DESC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
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
				"sortOrder" => "orderNumber ASC",
				"width" => "100%",
				"condition" => "@parent IS NOT EXISTS",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceOrderConditions_DocFlowApplication_Docs_"),
		"name" => "orderParent",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["DocumentChangeOrderCondition"] = array("title" =>"Изменение состояния заказа",
													"name" => "DocumentChangeOrderCondition",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("orderDocument","referenceOrderCondition")
);

$models["DocumentChangeOrderCondition"] = array("metaTitle" => "Изменение состояния заказа",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentChangeOrderCondition",
												   "parent" => "changeOrderConditionParent",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "orderDocument" => "orderDocument",
												   "orderCondition" =>"referenceOrderCondition",
												   "name" => "sysname",
												   "manager" => "referenceUser"												   
);
?>