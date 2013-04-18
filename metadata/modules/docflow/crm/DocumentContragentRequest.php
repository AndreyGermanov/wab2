<?php
$fields["contragentRequestParent"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentContragentRequest",
																  "tableClassName" => "DocFlowReferenceTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Обращение контрагента",
																  "editorType" => "WABWindow",
																  "title" => "Родитель",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
																  "parentEntity" => "DocumentContragentRequest_DocFlowApplication_Docs_"),
																  "name" => "contragentRequestParent",
																  "collection" => "fields",
																  "file" => __FILE__
);

$fields["referenceContact"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContacts",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Контактные лица",
				"editorType" => "WABWindow",
				"title" => "Контактное лицо",
				"fieldList" => "title ФИО",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"selectGroup" => "0",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceContacts_DocFlowApplication_Docs_"),
		"name" => "referenceContact",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["referenceContragentRequestType"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContragentRequestTypes",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Типы обращений контрагентов",
				"editorType" => "WABWindow",
				"title" => "Тип обращения",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"selectGroup" => "0",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceContragentRequestTypes_DocFlowApplication_Docs_"),
		"name" => "referenceContragentRequestType",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["referenceContragentRequestForm"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceRequestForms",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Формы обращения",
				"editorType" => "WABWindow",
				"title" => "Форма обращения",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"selectGroup" => "0",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceRequestForms_DocFlowApplication_Docs_"),
		"name" => "referenceContragentRequestForm",
		"collection" => "fields",
		"file" => __FILE__
);


$fields["referenceUser"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceUsers",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Менеджеры",
				"editorType" => "WABWindow",
				"title" => "Пользователь",
				"fieldList" => "title Фамилия~firstName Имя~secondName Отчество",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"selectGroup" => "0",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceUsers_DocFlowApplication_Docs_"),
		"name" => "referenceUser",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["contragentRequestText"] = array("base" => "textField",
										 "params" => array("title" => "Предмет обращения", "control_type" => "tinyMCE"),
										 "file" => __FILE__,
										 "collection" => "fields",
										 "name" => "contragentRequestText"
);

$fields["sign"] = array("base" => "booleanField",
		"params" => array("title" => "Подписать","description" => "Подписать","show_description" => "true"),
		"file" => __FILE__,
		"collection" => "fields",
		"name" => "sign"
);

$groups["DocumentContragentRequest"] = array("title" =>"Общращения контрагентов",
													"name" => "DocumentContragentRequest",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("contragentRequestParent","referenceContragent","referenceContact","referenceContragentRequestType","referenceUser","contragentRequestText")
);

$models["DocumentContragentRequest"] = array("metaTitle" => "Обращения клиентов",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentContragentRequest",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "parent" => "contragentRequestParent",
												   "contragent" => "referenceContragent",
												   "contact" => "referenceContact",
												   "requestType" => "referenceContragentRequestType",
												   "requestForm" => "referenceContragentRequestForm",
												   "requestText" => "contragentRequestText",
												   "sign" => "sign",
												   "manager" => "referenceUser"
);
?>