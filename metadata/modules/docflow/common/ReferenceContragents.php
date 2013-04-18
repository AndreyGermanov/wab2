<?php
$fields["contragentType"] = array("base" => "integerField",
						"params" => array("title" => "Тип контрагента", "type" => "list,1~2|Юр.лицо~Физ.лицо"),
						"file" => __FILE__,
						"collection" => "fields",
						"name" => "contragentType",
						"file" => __FILE__
);

$fields["contragentParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContragents",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Контрагенты",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceContragents_DocFlowApplication_Docs_"),
		"name" => "contragentParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["bankAccount"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceBankAccounts",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Банковские счета",
				"editorType" => "WABWindow",
				"title" => "Расчетный счет",
				"fieldList" => "contragent.title AS contragent Контрагент~RS № счета~BIK БИК~KS Корр.счет~bank.title AS bank Банк",
				"sortOrder" => "RS ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceBankAccounts_DocFlowApplication_Docs_"),
		"name" => "bankAccount",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["contragentKind"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContragentKinds",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Виды контрагентов",
				"editorType" => "WABWindow",
				"title" => "Вид контрагента",
				"fieldList" => "title",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceContragentKinds_DocFlowApplication_Docs_"),
		"name" => "contragentKind",
		"collection" => "fields",
		"file" => __FILE__
);


$fields["emailAddress"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceEmailAddresses",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Адреса Email",
				"editorType" => "WABWindow",
				"title" => "Адрес Email",
				"fieldList" => "email Email~title Описание",
				"sortOrder" => "email ASC",
				"width" => "100%",
				"control_type" => "email",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceEmailAddresses_DocFlowApplication_Docs_"),
		"name" => "emailAddress",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["fizDocument"] = array("base" => "stringField",
								"params" => array("title" => "Документ физического лица"),
								"file" => __FILE__,
								"name" => "fizDocument",
								"collection" => "fields"
);

$fields["officialAddress"] = array("base" => "textField",
		"params" => array("title" => "Юридический адрес"),
		"file" => __FILE__,
		"name" => "officialAddress",
		"collection" => "fields"
);

$fields["postalAddress"] = array("base" => "textField",
		"params" => array("title" => "Почтовый адрес", "autoresize" => "true"),
		"file" => __FILE__,
		"name" => "postalAddress",
		"collection" => "fields"
);

$fields["phones"] = array("base" => "textField",
		"params" => array("title" => "Телефоны"),
		"file" => __FILE__,
		"name" => "phones",
		"collection" => "fields"		
);

$fields["INN"] = array("base" => "integerField",
						"params" => array("title" => "ИНН", "regs" => "^[1-9][0-9]{0,9}$"),
						"name" => "INN",
						"collection" => "fields",
						"file" => __FILE__
);

$fields["KPP"] = array("base" => "integerField",
		"params" => array("title" => "КПП", "regs" => "^[1-9][0-9]{0,8}$"),
		"name" => "KPP",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["OKPO"] = array("base" => "integerField",
		"params" => array("title" => "ОКПО", "regs" => "^[1-9][0-9]{0,8}$"),
		"name" => "OKPO",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["contactInfo"] = array("base" => "textField", "params" => array("title" => "Контактная информация", "class" => "wide"),"name" => "contactInfo", "collection" => "fields", "file" => __FILE__);

$fields["familyName"] = array("base" => "stringField", "params" => array("title" => "Фамилия"), "name" => "familyName", "collection" => "fields", "file" => __FILE__);

$fields["referrerNumber"] = array("base" => "stringField", "params" => array("title" => "Номер реферрера"),"name" => "referrerNumber", "collection" => "fields", "file" => __FILE__);

$fields["emailAddr"] = array("base" => "stringField", "params" => array("title" => "Адрес электронной почты"),"name" => "emailAddress", "collection" => "fields", "file" => __FILE__);

$fields["mobileNumber"] = array("base" => "integerField", "params" => array("title" => "Номер мобильного телефона"), "name" => "mobileNumber", "collection" => "fields", "file" => __FILE__);

$groups["ReferenceContragents"] = array("title" =>"Контрагенты",
		"name" => "ReferenceContragents",
		"file" => __FILE__,
		"collection" => "groups",
		"groups" => array("ReferenceContragentsMain","ReferenceContragentsContacts")
);

$groups["ReferenceContragentsMain"] = array("title" =>"Основные параметры контрагента",
		"name" => "ReferenceContragentsMain",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("contragentType","contragentParent","fizDocument","INN","KPP","OKPO", "bankAccount","emailAddress","contragentKind")
);

$groups["ReferenceContragentsContacts"] = array("title" =>"Контакты контрагента",
		"name" => "ReferenceContragentsContacts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("officialAddress","postalAddress","phones")
);

$models["ReferenceContragents"] = array("metaTitle" => "Контрагенты",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceContragents",
								   "title" => "title",
								   "isGroup" => "isGroup",
								   "type" => "contragentType",
								   "kind" => "contragentKind",
								   "INN" => "INN",
								   "KPP" => "KPP",
								   "OKPO" => "OKPO",
								   "parent" => "contragentParent",
								   "fizDocument" => "fizDocument",
								   "officialAddress" => "officialAddress",
								   "postalAddress" => "postalAddress",
								   "phones" => "phones",
								   "defaultBankAccount" => "bankAccount",
								   "defaultEmail" => "emailAddress",
								   "firstName" => "firstName",
								   "secondName" => "secondName",
								   "familyName" => "familyName",
								   "mobileNumber" => "mobileNumber",
								   "email" => "emailAddr",
								   "discountCardNumber" => "discountCardNumber",
								   "referrerNumber" => "referrerNumber",
								   "contactInfo" => "contactInfo",
								   "photo" => "userPhoto",
								   "groups" => array("ReferenceContragentsMain","ReferenceContragentsContacts")
);
?>