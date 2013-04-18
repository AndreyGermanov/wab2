<?php
$fields["contractTemplateParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceContractTemplates",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Шаблоны договоров",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title DESC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceContractTemplates_DocFlowApplication_Docs_"),
		"name" => "contractTemplateParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["contractText"] = array("base" => "textField",
								 "params" => array("title" => "Текст договора", "width" => "100%", "height" => "100%", "control_type" => "tinyMCE")
);

$groups["ReferenceContractTemplates"] = array("title" =>"Шаблоны договоров",
											   "name" => "ReferenceContractTemplates",
											   "file" => __FILE__,
											   "collection" => "groups",
											   "fields" => array("contractTemplateParent","contractText")
);

$models["ReferenceContractTemplates"] = array("metaTitle" => "Шаблоны договоров",
											   "file" => __FILE__,
											   "collection" => "models",
											   "name" => "ReferenceContractTemplates",
											   "isGroup" => "isGroup",
											   "parent" => "contractTemplateParent",
											   "title" => "title",	
											   "contract" => "contractText"
);
?>