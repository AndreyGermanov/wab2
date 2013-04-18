<?php
$fields["fileParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceFiles",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Файлы",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceFiles_DocFlowApplication_Docs_"),
		"name" => "fileParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["fileDescription"] = array("base" => "textField",
									"params" => array("title" => "Описание", "width" => "100%", "height" => "100%", "control_type" => "tinyMCE"),
									"name" => "fileDescription",
									"collection" => "fields",
									"file" => __FILE__		
);

$fields["filePath"] = array(
							"base" => "fileField", 
							"params" => array("title" => "Путь к файлу"),
							"name" => "filePath",
							"collection" => "fields",
							"file" => __FILE__
);

$fields["fileStatus"] = array("type" => "integer",
						   "params" => array("title" => "Состояние",
						   					  "type" => "list,0~1|Существует~Не найден")
		
		);

$fields["inode"] = array(
		"base" => "stringField",
		"params" => array("title" => "Номер inode"),
		"name" => "inode",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["md5sum"] = array(
		"base" => "stringField",
		"params" => array("title" => "Контрольная сумма"),
		"name" => "md5sum",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["md5sumDate"] = array(
		"base" => "dateField",
		"params" => array("title" => "Дата контрольной суммы"),
		"name" => "md5sumDate",
		"collection" => "fields",
		"file" => __FILE__
);

$groups["ReferenceFiles"] = array("title" =>"Файлы",
		"name" => "ReferenceFiles",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("fileParent","fileDescription","filePath","inode","md5sum","md5sumDate","fileStatus")
);

$models["ReferenceFiles"] = array("metaTitle" => "Справочник файлов",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceFiles",
								   "isGroup" => "isGroup",
								   "parent" => "fileParent",
								   "path" => "filePath",			
								   "title" => "title",	
								   "description" => "fileDescription",
							       "inode" => "inode",
								   "md5sum" => "md5sum",
							       "md5sumDate" => "md5sumDate",
								   "status" => "fileStatus"		
);
?>