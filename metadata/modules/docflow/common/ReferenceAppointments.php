<?php
$fields["appointmentParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceAppointments",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Должности",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceAppointments_DocFlowApplication_Docs_"),
		"name" => "appointmentParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["isGroup"] = array("base" => "booleanField",
							"params" => array ("title" => "Является группой"));

$groups["ReferenceAppointments"] = array("title" =>"Должности",
		"name" => "ReferenceAppointments",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("appointmentParent")
);

$models["ReferenceAppointments"] = array("metaTitle" => "Должности",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceAppointments",
								   "isGroup" => "isGroup",
								   "parent" => "appointmentParent",
								   "title" => "title"	
);
?>