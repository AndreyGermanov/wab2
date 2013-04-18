<?php
$fields["contactParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContacts",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Контактные лица",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceContacts_DocFlowApplication_Docs_"),
		"name" => "contactParent",
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
				"fieldList" => "email Email~title Наименование",
				"sortOrder" => "email ASC",
				"width" => "100%",
				"control_type" => "email",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceEmailAddresses_DocFlowApplication_Docs_"),
		"name" => "emailAddress",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["contactDescription"] = array("base" => "textField",
		"params" => array("title" => "Описание"),
		"file" => __FILE__,
		"name" => "contactDescription",
		"collection" => "fields"
);

$fields["appointmentTitle"] = array("base" => "stringField",
		"params" => array("title" => "Должность"),
		"file" => __FILE__,
		"name" => "appointment",
		"collection" => "fields"
);

$groups["ReferenceContacts"] = array("title" =>"Контактные лица",
		"name" => "ReferenceContacts",
		"file" => __FILE__,
		"collection" => "groups",
		"groups" => array("ReferenceContactsMain","ReferenceContactsContacts")
);

$groups["ReferenceContragentsMain"] = array("title" =>"Основные параметры контактного лица",
		"name" => "ReferenceContactsMain",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("contactParent","userPhoto","emailAddress","appointment","birthDate", "contactDescription")
);

$groups["ReferenceContragentsContacts"] = array("title" =>"Контакты контактного лица",
		"name" => "ReferenceContactsContacts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("postalAddress","phones")
);

$models["ReferenceContacts"] = array("metaTitle" => "Контактные лица",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceContacts",
								   "title" => "title",
								   "description" => "contactDescription",
								   "isGroup" => "isGroup",
								   "parent" => "contactParent",
								   "postalAddress" => "postalAddress",
								   "phones" => "phones",
								   "defaultEmail" => "emailAddress",
								   "photo" => "userPhoto",
								   "birthDate" => "birthDate",
								   "appointment" => "appointmentTitle",		  
								   "groups" => array("ReferenceContactsMain","ReferenceContactsContacts")
);
?>