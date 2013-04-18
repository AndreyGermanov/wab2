<?php
$fields["referenceContragent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContragents",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Контрагенты",
				"editorType" => "WABWindow",
				"title" => "Контрагент",
				"fieldList" => "title Наименование~phones Телефоны~defaultEmail.title AS email Email",
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

$fields["referenceBank"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceBanks",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Банки",
				"editorType" => "WABWindow",
				"title" => "Банк",
				"fieldList" => "title Наименование,BIK БИК~KS Корр.счет",
				"sortOrder" => "BIK ASC",
				"width" => "100%",
				"selectGroup" => "0",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceBanks_DocFlowApplication_Docs_"),
		"name" => "referenceBank",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["RS"] = array("base" => "stringField",
		"params" => array("title" => "Расчетный счет","regs" => '^[1-9][0-9]{0,19}$'),
		"file" => __FILE__,
		"collection" => "fields",
		"name" => "RS"
);

$groups["ReferenceBankAccounts"] = array("title" =>"Справочник контрагентов",
		"name" => "ReferenceBankAccounts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("referenceBank","referenceContragent","RS")
);

$models["ReferenceBankAccounts"] = array("metaTitle" => "Банковские счета",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceBankAccounts",
								   "RS" => "RS",
								   "BIK" => "BIK",
								   "KS" => "KS",
								   "bank" => "referenceBank",
								   "contragent" => "referenceContragent"		
);
?>