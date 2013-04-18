<?php
$fields["contragentRequestTypesParent"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "ReferenceContragentRequestTypesParent",
																  "tableClassName" => "DocFlowReferenceTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Тип обращения контрагента",
																  "editorType" => "WABWindow",
																  "title" => "Родитель",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
																  "parentEntity" => "ReferenceContragentRequestTypes_DocFlowApplication_Docs_"),
																  "name" => "contragentRequestTypesParent",
																  "collection" => "fields",
																  "file" => __FILE__
);

$groups["ReferenceContragentRequestTypes"] = array("title" =>"Типы обращений контрагентов",
													"name" => "ReferenceContragentRequestTypes",
													"file" => __FILE__,
													"collection" => "groups",
													"fields" => array("contragentRequestTypesParent")
);

$models["ReferenceContragentRequestTypes"] = array("metaTitle" => "Типы обращений контрагентов",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "ReferenceContragentRequestTypes",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "parent" => "contragentRequestTypesParent",
);
?>