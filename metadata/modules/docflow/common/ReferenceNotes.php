<?php
$fields["noteParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceNotes",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Заметки",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "date Дата~title Наименование",
				"sortOrder" => "date DESC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceNotes_DocFlowApplication_Docs_"),
		"name" => "noteParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["noteDescription"] = array("base" => "textField",
									"params" => array("title" => "Описание", "width" => "100%", "height" => "100%", "control_type" => "tinyMCE"),
									"name" => "noteDescription",
									"collection" => "fields",
									"file" => __FILE__
);

$fields["noteDate"] = array("base" => "dateField", 
							 "params" => array("title" => "Дата", "width" => "100%"),
							 "name" => "noteDate",
							 "collection" => "fields",
							 "file" => __FILE__
);

$groups["ReferenceNotes"] = array("title" =>"Справочник заметок",
		"name" => "ReferenceNotes",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("noteParent","noteDescription","noteDate")
);

$models["ReferenceNotes"] = array("metaTitle" => "Заметки",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceFiles",
								   "isGroup" => "isGroup",
								   "parent" => "noteParent",
								   "date" => "noteDate",			
								   "title" => "title",	
								   "description" => "noteDescription"
);
?>