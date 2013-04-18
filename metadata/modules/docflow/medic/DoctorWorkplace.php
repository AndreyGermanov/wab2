<?php
$fields["referencePatient"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferencePatients",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Пациенты",
				"editorType" => "WABWindow",
				"title" => "Пациент",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferencePatients_DocFlowApplication_Docs_"),
		"name" => "referencePatient",
		"collection" => "fields",
		"file" => __FILE__
);

$models["DoctorWorkplace"] = array("metaTitle" => "Рабочее место врача",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "DoctorWorkplace",
								   "title" => "title",
								   "patient" => "referencePatient",
								   "fio" => "staticField",
								   "age" => "staticField",
								   "birthDate" => "staticField",
								   "photo" => "userPhoto",
								   "city" => "staticField",
								   "address" => "staticField",
								   "diagnozeYear" => "staticField",
								   "diagnozeDate" => "staticField",
								   "pcrResult" => "staticField",
								   "phResult" => "staticField",			
								   "pcrDocument" => "staticField",
								   "phDocument" => "staticField"						 		
);
?>