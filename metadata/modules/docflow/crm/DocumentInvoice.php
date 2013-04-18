<?php
$fields["invoiceParent"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentInvoice",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Заказ",
																  "editorType" => "WABWindow",
																  "title" => "Наименование",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
																  "parentEntity" => "DocumentInvoice_DocFlowApplication_Docs_"),
																  "name" => "invoiceParent",
																  "collection" => "fields",
																  "file" => __FILE__
);

$fields["isStamp"] = array("name" => "isStamp", "collection" => "fields", "base" => "booleanField", "params" => array ("title" => "Подписать"));

$fields["includeNDS"] = array("name" => "includeNDS", "collection" => "fields", "base" => "booleanField", "params" => array ("title" => "Сумма включает НДС"));

$fields["documentTable"] = array("name" => "documentTable", "collection" => "fields", "base" => "textField", "params" => array ("title" => "Таблица документа", "hide" => "true"));

$models["DocumentInvoice"] = array("metaTitle" => "Счета",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentOrder",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "parent" => "invoiceParent",
												   "contragent" => "referenceContragent",
												   "contragentAccount" => "bankAccount",
												   "firm" => "firm",
												   "firmAccount" => "bankAccount",
												   "documentTable" => "documentTable",
												   "manager" => "referenceUser",
												   "isStamp" => "isStamp",
												   "includeNDS" => "includeNDS",
												   "invoiceSumma" => "summa",
												   "invoiceNDS" => "NDS"
);
?>