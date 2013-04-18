<?php
$fields["parent"] = array("type" => "string",
		"params" => array("type" => "entity",
				"className" => "WebTemplate",
				"tableClassName" => "EntityDataTable",
				"show_float_div" => "true",
				"classTitle" => "Шаблоны",
				"adapterId" => "TemplatesDataAdapter_WebServerApplication_Web_select",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"condition" => "@parent IS NOT EXISTS",
				"treeClassName" => "WebTemplateTree",
				"sortOrder" => "title ASC string",
				"must_set" => "false",
				"parentEntity" => "WebTemplate_WebServerApplication_Web_"),
		"name" => "parent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["template_file"] = array("type" => "string",
		"params" => array("type" => "file",
				"control_type" => "fileManager",
				"title" => "Файл разметки",
				"absolutePath" => "false",
				"must_set" => "false"),
		"name" => "template_file",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["css_file"] = array("type" => "string",
		"params" => array("type" => "file",
				"control_type" => "fileManager",
				"title" => "Файл оформления",
				"absolutePath" => "false",
				"must_set" => "false"),
		"name" => "css_file",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["handler_file"] = array("type" => "string",
		"params" => array("type" => "file",
				"control_type" => "fileManager",
				"title" => "Файл обработчика",
				"absolutePath" => "false",
				"must_set" => "false"),
		"name" => "handler_file",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["class_file"] = array("type" => "string",
		"params" => array("type" => "file",
				"control_type" => "fileManager",
				"title" => "Файл класса",
				"absolutePath" => "false",
				"must_set" => "false"),
		"name" => "class_file",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["WebTemplate"] = array(
		"title",
		"parent",
		"template_file",
		"css_file",
		"handler_file",
		"class_file",
		"persistedFields"		
);

$models["WebTemplate"] = array(
		"file" => __FILE__,
		"metaTitle" => "Раздел Web-сайта",
		"collection" => "models",
		"title" => "title",
		"parent" => "parent",
		"template_file" => "template_file",
		"css_file" => "css_file",
		"handler_file" => "handler_file",
		"class_file" => "class_file",
		"persistedFields" => "persistedFields"
);
