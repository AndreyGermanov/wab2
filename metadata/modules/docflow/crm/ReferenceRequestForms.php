<?php
$fields["requestFormsParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceRequestForms",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Формы обращения",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceRequestForms_DocFlowApplication_Docs_"),
		"name" => "requestFormsParent",
		"collection" => "fields",
		"file" => __FILE__
);


$groups["ReferenceRequestForms"] = array("title" =>"Формы обращения",
		"name" => "ReferenceRequestForms",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("requestFormsParent")
);

$models["ReferenceRequestForms"] = array("metaTitle" => "Формы обращения",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceRequestForms",
								   "isGroup" => "isGroup",
								   "parent" => "requestFormsParent",
								   "title" => "title"	
);
?>