<?php
$fields["cityParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceCities",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Города",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceCities_DocFlowApplication_Docs_"),
		"name" => "cityParent",
		"collection" => "fields",
		"file" => __FILE__
);


$groups["ReferenceCities"] = array("title" =>"Города",
		"name" => "ReferenceCities",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("cityParent")
);

$models["ReferenceCities"] = array("metaTitle" => "Города",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceCities",
								   "isGroup" => "isGroup",
								   "parent" => "cityParent",
								   "title" => "title"	
);
?>