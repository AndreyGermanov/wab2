<?php
$fields["firmParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceFirms",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Организации",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceFirms_DocFlowApplication_Docs_"),
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

$fields["emailAccount"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceEmailAccount",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Учетные записи Email",
				"editorType" => "WABWindow",
				"title" => "Адрес Email",
				"fieldList" => "email EMail~title Описание",
				"sortOrder" => "email ASC",
				"width" => "100%",
				"control_type" => "email",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceEmailAccounts_DocFlowApplication_Docs_"),
		"name" => "emailAddress",
		"collection" => "fields",
		"file" => __FILE__
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

$fields["OGRN"] = array("base" => "stringField",
		"params" => array("title" => "ОГРН", "regs" => "^[1-9][0-9]{0,12}$"),
		"name" => "OGRN",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["OKVED"] = array("base" => "stringField",
		"params" => array("title" => "ОКВЭД"),
		"name" => "OKVED",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["OKATO"] = array("base" => "stringField",
		"params" => array("title" => "ОКАТО"),
		"name" => "OKATO",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["OKFS"] = array("base" => "stringField",
		"params" => array("title" => "ОКФС"),
		"name" => "OKFS",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["OKOPF"] = array("base" => "stringField",
		"params" => array("title" => "ОКОПФ"),
		"name" => "OKOPF",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["regPFR"] = array("base" => "stringField",
		"params" => array("title" => "Регистрационный номер ПФР"),
		"name" => "regPFR",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["regFSS"] = array("base" => "stringField",
		"params" => array("title" => "Регистрационный номер ФСС"),
		"name" => "regFSS",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["fullTitle"] = array("base" => "stringField",
		"params" => array("title" => "Полное наименование"),
		"name" => "regFullTitle",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["kodPodFSS"] = array("base" => "stringField",
		"params" => array("title" => "Код подчиненности ФСС"),
		"name" => "kodPodFSS",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["TFOMS"] = array("base" => "stringField",
		"params" => array("title" => "Регистрационный номер ТФОМС"),
		"name" => "TFOMS",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["firmDirector"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceUsers",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Пользователи",
				"editorType" => "WABWindow",
				"title" => "Директор",
				"fieldList" => "title Фамилия~firstName Имя~secondName Отчество",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceUsers_DocFlowApplication_Docs_"),
		"name" => "firmDirector",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["firmBuhgalter"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceUsers",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Пользователи",
				"editorType" => "WABWindow",
				"title" => "Бухгалтер",
				"fieldList" => "title Фамилия~firstName Имя~secondName Отчество",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceUsers_DocFlowApplication_Docs_"),
		"name" => "firmBuhgalter",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["firmKassir"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceUsers",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Пользователи",
				"editorType" => "WABWindow",
				"title" => "Кассир",
				"fieldList" => "title Фамилия~firstName Имя~secondName Отчество",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceUsers_DocFlowApplication_Docs_"),
		"name" => "firmKassir",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["firmLogo"] = array("base" => "fileField",
		"params" => array("title" => "Логотип", "control_type" => "image", "show_preview" => "true"),
		"name" => "firmLogo",
		"file" => __FILE__,
		"collection" => "fields"
);

$groups["ReferenceFirms"] = array("title" =>"Организации",
		"name" => "ReferenceFirms",
		"file" => __FILE__,
		"collection" => "groups",
		"groups" => array("ReferenceFirmsMain","ReferenceFirmsContacts","ReferenceFirmsCodes","referenceFirmsFonds","referenceFirmsFaces")
);

$groups["ReferenceFirmsMain"] = array("title" =>"Основные параметры организации",
		"name" => "ReferenceFirmsMain",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("firmParent", "title", "fullTitle", "logo", "bankAccount", "emailAddress", "firmLogo")
);

$groups["ReferenceFirmsContacts"] = array("title" =>"Контакты организации",
		"name" => "ReferenceFirmsContacts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("officialAddress","postalAddress","phones")
);

$groups["ReferenceFirmsCodes"] = array("title" =>"Коды",
		"name" => "ReferenceFirmsCodes",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("INN", "KPP", "OGRN", "OKPO", "OKATO", "OKVED", "OKOPF", "OKFS")
);

$groups["ReferenceFirmsFonds"] = array("title" =>"Фонды",
		"name" => "ReferenceFirmsFonds",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("TFOMS", "regPFR", "regFSS", "kodPodFSS")
);

$groups["ReferenceFirmsFaces"] = array("title" =>"Ответственные лица организации",
		"name" => "ReferenceFirmsContacts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("director","buhgalter","kassir")
);

$models["ReferenceFirms"] = array("metaTitle" => "Организации",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceFirms",
								   "title" => "title",
								   "fullTitle" => "fullTitle",
								   "logo" => "firmLogo",
								   "isGroup" => "isGroup",
								   "OGRN" => "OGRN",
								   "INN" => "INN",
								   "KPP" => "KPP",
								   "OKPO" => "OKPO",
								   "OKVED" => "OKVED",
								   "OKOPF" => "OKOPF",
								   "OKFS" => "OKFS",
								   "OKATO" => "OKATO",
								   "TFOMS" => "TFOMS",
								   "regPFR" => "regPFR",
								   "regFSS" => "regFSS",
								   "kodPodFSS" => "kodPodFSS",
								   "parent" => "firmParent",
								   "officialAddress" => "officialAddress",
								   "postalAddress" => "postalAddress",
								   "phones" => "phones",
								   "defaultBankAccount" => "bankAccount",
								   "defaultEmail" => "emailAccount",
								   "director" => "firmDirector",
								   "buhgalter" => "firmBuhgalter",
								   "kassir" => "firmKassir",
								   "groups" => array("ReferenceFirmsMain","ReferenceFirmsContacts","ReferenceFirmsCodes","ReferenceFirmsFonds","ReferenceFirmsFaces")
);
?>