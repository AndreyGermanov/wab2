<?php
$fields["referenceProductKindsParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceProductKinds",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Виды номенклатуры",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceProductKinds_DocFlowApplication_Docs_"),
		"name" => "referenceProductKindsParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["isGroup"] = array("base" => "booleanField",
							"params" => array ("title" => "Является группой"));

$groups["ReferenceContragentKinds"] = array("title" =>"Виды номенклатуры",
		"name" => "ReferenceProductKinds",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("referenceProductKindsParent")
);

$models["ReferenceProductKinds"] = array("metaTitle" => "Виды номенклатуры",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceProductKinds",
								   "isGroup" => "isGroup",
								   "parent" => "referenceProductKindsParent",
								   "title" => "title"	
);
?>