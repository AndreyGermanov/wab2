<?php
$fields["quickSaleParent"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentQuickSale",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Розничная продажа",
																  "editorType" => "WABWindow",
																  "title" => "Наименование",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
																  "parentEntity" => "DocumentQuickSale_DocFlowApplication_Docs_"),
																  "name" => "quickSaleParent",
																  "collection" => "fields",
																  "file" => __FILE__
);

$fields["referenceContragentRozn"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContragents",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "false",
				"classTitle" => "Контрагенты",
				"editorType" => "WABWindow",
				"title" => "Контрагент",
				"fieldList" => "familyName Фамилия~firstName Имя~secondName Отчество~discountCardNumber № дисконтной карты~referrerNumber Номер реферрера~mobileNumber Мобильный номер",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"selectGroup" => "0",
				"condition" => "@parent IS NOT EXISTS",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceContragents_DocFlowApplication_Docs_"),
		"name" => "referenceContragent",
		"collection" => "fields",
		"file" => __FILE__
);


$fields["prihodSumma"] = array("base" => "decimalField", "name" => "prihodSumma", "collection" => "fields", "file" => __FILE__, "params" => array("title" => "Получено от клиента")); 

$fields["discountSumma"] = array("base" => "decimalField", "name" => "discountSumma", "collection" => "fields", "file" => __FILE__, "params" => array("title" => "Сумма скидки"));

$fields["smsSent"] = array("base" => "booleanField", "name" => "smsSent", "collection" => "fields", "file" => __FILE__, "params" => array("title" => "SMS-сообщение отправлено"));

$fields["emailSent"] = array("base" => "booleanField", "name" => "emailSent", "collection" => "fields", "file" => __FILE__, "params" => array("title" => "Email-сообщение отправлено"));

$models["DocumentQuickSale"] = array("metaTitle" => "Розничные продажи",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentQuickSale",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "parent" => "quickSaleParent",
												   "contragent" => "referenceContragentRozn",
												   "documentTable" => "documentTable",
												   "manager" => "referenceUser",
												   "orderSumma" => "summa",
												   "prihodSumma" => "prihodSumma",
												   "discountSumma" => "discountSumma",
												   "smsSent" => "smsSent",
												   "emailSent" => "emailSent"
);
?>