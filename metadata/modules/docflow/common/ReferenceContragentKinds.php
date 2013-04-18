<?php
$fields["referenceContragentKindsParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContragentKinds",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Виды контрагентов",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceContragentKinds_DocFlowApplication_Docs_"),
		"name" => "referenceContragentKindsParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["isGroup"] = array("base" => "booleanField",
							"params" => array ("title" => "Является группой"));

$groups["ReferenceContragentKinds"] = array("title" =>"Виды контрагентов",
		"name" => "ReferenceContragentKinds",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("referenceContragentKindsParent")
);

$models["ReferenceContragentKinds"] = array("metaTitle" => "Виды контрагентов",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceContragentKinds",
								   "isGroup" => "isGroup",
								   "parent" => "referenceContragentKindsParent",
								   "title" => "title"	
);
?>