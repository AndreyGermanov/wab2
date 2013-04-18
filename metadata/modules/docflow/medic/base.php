<?php
$fields["patient"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferencePatients",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Пациенты",
				"editorType" => "WABWindow",
				"title" => "ФИО",
				"fieldList" => "title ФИО",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferencePatients_DocFlowApplication_Docs_"),
				"name" => "patient",
				"collection" => "fields",
);

$fields["analyzeType"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "BloodAnalyzeTypesReference",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Типы анализа",
				"editorType" => "WABWindow",
				"title" => "Тип анализа крови",
				"fieldList" => "title Тип анализа крови",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "BloodAnalyzeTypesReference_DocFlowApplication_Docs_"),
				"name" => "analyzeType",
				"collection" => "fields",
				"file" => __FILE__
);

$fields["analyzeDef"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "BloodDefinitionsReference",
				"additionalFields" => "name",
				"tableClassName" => "DocFlowReferenceTable",
				"show_float_div" => "true",
				"classTitle" => "Показатель анализа крови",
				"editorType" => "WABWindow",
				"title" => "Тип анализа",
				"fieldList" => "title Показатель",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "BloodDefinitionsReference_DocFlowApplication_Docs_"),
				"name" => "analyzeDef",
				"collection" => "fields",
				"file" => __FILE__
);

$fields["analyzeDefValue"] = array("type" => "decimal",
		"params" => array("type" => "decimal",
				"title" => "Значение показателя крови"),
				"name" => "analyzeDefValue",
				"collection" => "fields",
				"file" => __FILE__
);

$fields["fullName"] =
array
(
		"type" => "string",
		"params" => array
		(
				"type" => "string",
				"title" => "ФИО"
		),
		"file" => __FILE__
);

$fields["comment"] =
array
(		
		"base" => "stringField",		
		"params" => array
		(
				"title" => "Комментарий"
		),
		"file" => __FILE__
);

$fields["birthDate"] =
array
(
		"type" => "integer",
		"params" => array
		(
				"type" => "date",
				"title" => "Дата рождения",
				"show_time" => "false"
		),
		"file" => __FILE__
);

$fields["regDate"] =
array
(
		"base" => "dateField",
		"params" => array
		(
				"title" => "Дата регистрации",
				"show_time" => "false"
		),
		"file" => __FILE__
);

$fields["document"] = array("type" => "entity",		
								"params" => array( "type" => "entity",
													"additionalFields" => "name,isGroup",
													"show_float_div" => "true",
													"classTitle" => "Регистраторы",
													"editorType" => "WABWindow",
													"title" => "Регистратор",
													"fieldList" => "title Наименование",
													"sortOrder" => "title ASC",
													"width" => "100%",
													"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
													"condition" => "@parent IS NOT EXISTS",
													"tableClassName" => "DocFlowDocumentTable",
													"parentEntity" => ""),
								"name" => "document",
								"collection" => "fields",
								"file" => __FILE__
);

$fields["gender"] =
array
(
		"type" => "integer",
		"params" => array
		(
				"type" => "list,1~2|Мужской~Женский",
				"title" => "Пол"
		),
		"file" => __FILE__
);

$fields["account"] =
array
(
		"type" => "string",
		"params" => array
		(
				"type" => "list,",
				"title" => "Учетная запись"
		),
		"file" => __FILE__
);

$fields["referenceCity"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceCities",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Города",
				"editorType" => "WABWindow",
				"title" => "Город",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceCities_DocFlowApplication_Docs_"),
		"name" => "referenceCity",
		"collection" => "fields",
		"file" => __FILE__
);


$groups["docflowBlood"] = array("title" => "Основные медицинские показатели по крови",
									   "name" => "docflowBlood",
									   "file" => __FILE__,
									   "collection" => "groups",
									   "fields" => array("patient","analyzeType","analyzeDef","analyzeDefValue")
);

$groups["docflowMedic"] = array("title" =>"Основные медицинские показатели",
								"name" => "docflowMedic",
								"file" => __FILE__,
								"collection" => "groups",
								"groups" => array("docflowBlood","ReferencePatients")
);

$groups["ReferencePatients"] =
array
(
		"file" => __FILE__,
		"groups" => array
		(

		),
		"fields" => array
		(
				"fullName",
				"birthDate",
				"gender",
				"account",
				"referenceCity"
		),
		"title" => "Справочник пациентов"
);

$fields["address"] = array("name" => "address", "collection" => "fields", "file" => __FILE__, "params" => array ("title" => "Адрес"));

$fields["diagnozeDate"] = array("name" => "diagnozeDate", "collection" => "fields", "file" => __FILE__, "params" => array ("title" => "Дата диагноза", "show_time" => "false"));

$modelGroups["docflowBlood"] = array("title" => "Основные медицинские классы по крови",
									 "name" => "docflowBlood",
									 "file" => __FILE__,
									 "collection" => "modelGroups",
									 "fields" => array("DocumentBloodAnalyze","ReportBloodAnalyze","ReferencePatients")
);

$models["ReportBloodAnalyze"] = array("metaTitle" => "Отчет 'Анализ движения показателя крови'",
									  "name" => "ReportBloodAnalyze",
									  "file" => __FILE__,
									  "collection" => "models",
									  "def" => "analyzeDef",
									  "patient" => "patient"
);

$models["DocumentBloodAnalyze"] = array("metaTitle" => "Документ 'Анализ крови'",
		"name" => "DocumentBloodAnalyze",
		"file" => __FILE__,
		"collection" => "models",
		"analyzeType" => "analyzeType",
		"patient" => "patient",
		"documentTable" => "documentTable",
		"comment" => "comment"
);

$models["ReferencePatients"] =
array
(
		"title" => "fullName",
		"birthDate" => "birthDate",
		"gender" => "gender",
		"account" => "account",
		"address" => "address",
		"photo" => "userPhoto",
		"city" => "referenceCity",
		"diagnozeDate" => "diagnozeDate",
		"file" => __FILE__,
		"metaTitle" => "Базовая информация о человеке"
);
?>