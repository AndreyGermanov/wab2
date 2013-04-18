<?php
$fields["emailAddressParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceEmailAddresses",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Адреса E-Mail",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceEmailAddresses_DocFlowApplication_Docs_"),
		"name" => "emailAddressParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["emailAddressDescription"] = array("base" => "textField",
											"params" => array("title" => "Описание", "width" => "100%", "height" => "100%"),
											"name" => "emailAddressDescription",
											"collection" => "fields",
											"file" => __FILE__
);

$fields["email"] = array(
							"base" => "stringField", 
							"params" => array("title" => "Адрес Email", "control_type" => "email"),
							"name" => "email",
							"collection" => "fields",
							"file" => __FILE__						
);

$groups["ReferenceEmailAddresses"] = array("title" =>"Email-адреса",
		"name" => "ReferenceEmailAddresses",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("emailAddressParent","emailAddressDescription","email")
);

$models["ReferenceEmailAddresses"] = array("metaTitle" => "Email-адреса",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceEmailAddresses",
								   "isGroup" => "isGroup",
								   "parent" => "emailAddressParent",
								   "title" => "email",			
								   "description" => "emailAddressDescription"
);
?>