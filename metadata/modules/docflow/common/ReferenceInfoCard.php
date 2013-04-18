<?php
$fields["infoCardParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceInfoCard",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Информационные карточки",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceInfoCard_DocFlowApplication_Docs_"),
		"name" => "infoCardParent",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["ReferenceInfoCard"] = array("title" =>"Информационные карточки",
		"name" => "ReferenceInfoCard",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("infoCardParent","noteDescription")
);

$models["ReferenceInfoCard"] = array("metaTitle" => "Информационные карточки",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceInfoCard",
								   "isGroup" => "isGroup",
								   "parent" => "infoCardParent",
								   "objectId" => "stringField",			
								   "title" => "title",	
								   "description" => "noteDescription"
);
?>