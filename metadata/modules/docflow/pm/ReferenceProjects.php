<?php
$fields["projectParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceProjects",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Проекты",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title DESC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceProjects_DocFlowApplication_Docs_"),
		"name" => "projectParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["department"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceDepartments",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Подразделения",
				"editorType" => "WABWindow",
				"title" => "Подразделение",
				"fieldList" => "title Наименование",
				"sortOrder" => "title DESC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceDepartments_DocFlowApplication_Docs_"),
		"name" => "department",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["projectDescription"] = array("base" => "textField",
								 "params" => array("title" => "Описание проекта", "width" => "100%", "height" => "100%", "control_type" => "tinyMCE")
);

$fields["isArchive"] = array("base" => "booleanField",
							  "params" => array ("title" => "Архивный"));

$groups["ReferenceProjects"] = array("title" =>"Проекты",
											   "name" => "ReferenceProjects",
											   "file" => __FILE__,
											   "collection" => "groups",
											   "fields" => array("projectDescription","department","firm")
);

$models["ReferenceProjects"] = array("metaTitle" => "Проекты",
											   "file" => __FILE__,
											   "collection" => "models",
											   "name" => "ReferenceProjects",
											   "isGroup" => "isGroup",
											   "parent" => "projectParent",
											   "title" => "title",											   
											   "description" => "projectDescription",
											   "dateStart" => "dateStart",
											   "dateEnd" => "dateEnd",
											   "firm" => "firm",
											   "department" => "department",
											   "manager" => "referenceUser",
											   "workObject" => "entityObject",
											   "isArchive" => "isArchive"
);
?>