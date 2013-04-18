<?php
$fields["departmentParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceDepartments",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Подразделения",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceDepartments_DocFlowApplication_Docs_"),
		"name" => "departmentParent",
		"collection" => "fields",
		"file" => __FILE__
);


$groups["ReferenceDepartments"] = array("title" =>"Подразделения",
		"name" => "ReferenceDepartments",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("departmentParent")
);

$models["ReferenceDepartments"] = array("metaTitle" => "Подразделения",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceDepartments",
								   "isGroup" => "isGroup",
								   "parent" => "departmentParent",
								   "title" => "title"	
);
?>