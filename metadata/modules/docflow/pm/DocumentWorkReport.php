<?php
$fields["workReportParent"] = array("type" => "entity",
 											   "params" => array("type" => "entity",
										 						  "className" => "WorkReport",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Отчет о работе",
																  "editorType" => "WABWindow",
																  "title" => "Родитель",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
																  "parentEntity" => "DocumentWorkReport_DocFlowApplication_Docs_"),
											  "name" => "workReportParent",
											  "collection" => "fields",
											  "file" => __FILE__
);

$fields["entityObject"] = array("type" => "entity",
								"params" => array( "type" => "entity",
													"additionalFields" => "name,isGroup",
													"show_float_div" => "true",
													"classTitle" => "Объекты",
													"editorType" => "WABWindow",
													"title" => "Объект",
													"fieldList" => "title Наименование",
													"sortOrder" => "title ASC",
													"width" => "100%",
													"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
													"condition" => "@parent IS NOT EXISTS",
													"tableClassName" => "DocFlowDocumentTable",
													"parentEntity" => ""),
								"name" => "entityObject",
								"collection" => "fields",
								"file" => __FILE__
);

$fields["description"] = array("base" => "textField",
		"params" => array("title" => "Описание", "width" => "100%", "height" => "100%", "control_type" => "tinyMCE")
);

$models["DocumentWorkReport"] = array("metaTitle" => "Отчеты о работе",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentWorkReport",
												   "title" => "title",
												   "isGroup" => "isGroup",
												   "parent" => "workReportParent",
												   "employee" => "referenceUser",
												   "workObject" => "entityObject",
												   "dateStart" => "dateStart",
												   "dateEnd" => "dateEnd",
												   "firm" => "firm",
												   "reportText" => "description",
												   "manager" => "referenceUser"	
);
?>