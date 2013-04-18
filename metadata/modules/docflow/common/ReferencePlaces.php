<?php
$fields["placeParent"] = array("type" => "entity",
	"params" => array("type" => "entity",
			"className" => "ReferencePlaces",
			"tableClassName" => "DocFlowReferenceTable",
			"additionalFields" => "name,isGroup",
			"show_float_div" => "true",
			"classTitle" => "Места",
			"editorType" => "WABWindow",
			"title" => "Родитель",
			"fieldList" => "title Наименование",
			"sortOrder" => "title ASC",
			"width" => "100%",
			"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
			"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
			"parentEntity" => "ReferencePlaces_DocFlowApplication_Docs_"),
	"name" => "placeParent",
	"collection" => "fields",
	"file" => __FILE__
);

$fields["placeDepartment"] = array("type" => "entity",
	"params" => array("type" => "entity",
		"className" => "ReferenceDepartments",
		"tableClassName" => "DocFlowReferenceTable",
		"additionalFields" => "name,isGroup",
		"show_float_div" => "true",
		"classTitle" => "Места",
		"editorType" => "WABWindow",
		"title" => "Место",
		"fieldList" => "title Наименование",
		"sortOrder" => "title ASC",
		"width" => "100%",
		"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
		"condition" => "@parent IS NOT EXISTS",
		"parentEntity" => "ReferencePlaces_DocFlowApplication_Docs_"),
	"name" => "placeDepartment",
	"collection" => "fields",
	"file" => __FILE__
);

$groups["ReferencePlaces"] = array("title" =>"Места",
	"name" => "ReferencePlaces",
	"file" => __FILE__,
	"collection" => "groups",
	"fields" => array("placeParent","placeDepartment")
);

$models["ReferencePlaces"] = array("metaTitle" => "Места",
   "file" => __FILE__,
   "collection" => "models",
   "name" => "ReferencePlaces",
   "isGroup" => "isGroup",
   "parent" => "placeParent",
   "department" => "placeDepartment",
   "title" => "title"	
);
?>