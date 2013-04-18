<?php
$fields["idDocumentTypeParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceIdDocumentTypes",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Типы документов удостоверения личности",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceIdDocumentTypes_DocFlowApplication_Docs_"),
		"name" => "idDocumentTypeParent",
		"collection" => "fields",
		"file" => __FILE__
);


$groups["ReferenceIdDocumentTypes"] = array("title" =>"Типы документов удостоверения личности",
		"name" => "ReferenceIdDocumentTypes",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("idDocumentTypeParent")
);

$models["ReferenceIdDocumentTypes"] = array("metaTitle" => "Типы документов удостоверения личности",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceIdDocumentTypes",
								   "isGroup" => "isGroup",
								   "parent" => "idDocumentTypeParent",
								   "title" => "title"	
);
?>