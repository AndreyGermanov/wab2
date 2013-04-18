<?php
$fields["orderParent"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentOrder",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Заказ",
																  "editorType" => "WABWindow",
																  "title" => "Родитель",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
																  "parentEntity" => "DocumentOrder_DocFlowApplication_Docs_"),
																  "name" => "orderParent",
																  "collection" => "fields",
																  "file" => __FILE__
);


$fields["orderSumma"] = array("base" => "decimalField",
		"params" => array("title" => "Сумма заказа"),
		"file" => __FILE__,
		"collection" => "fields",
		"name" => "orderSumma"
);

$groups["DocumentOrder"] = array("title" =>"Заказы",
													"name" => "DocumentOrder",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("orderParent","referenceContragent","referenceContact","referenceUser","contragentRequestText","orderSumma")
);

$models["DocumentOrder"] = array("metaTitle" => "Заказы",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentOrder",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "parent" => "orderParent",
												   "contragent" => "referenceContragent",
												   "contact" => "referenceContact",
												   "requestText" => "contragentRequestText",
												   "orderSumma" => "orderSumma",
												   "manager" => "referenceUser"
);
?>