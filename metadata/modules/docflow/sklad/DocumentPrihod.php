<?php

$fields["referenceProduct"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceProducts",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Товары",
				"editorType" => "WABWindow",
				"title" => "Товар",
				"fieldList" => "code Код~title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceProducts_DocFlowApplication_Docs_"),
		"name" => "referenceProduct",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["referencePlace"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferencePlaces",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Места",
				"editorType" => "WABWindow",
				"title" => "Место",
				"fieldList" => "code Код~title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferencePlaces_DocFlowApplication_Docs_"),
		"name" => "referencePlace",
		"collection" => "fields",
		"file" => __FILE__
);

$models["RegistryProducts"] = array("metaTitle" => "Остатки и движения товаров на складах",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryProducts",
												   "product" => "referenceProduct",
												   "department" => "placeDepartment",
												   "place" => "referencePlace",
												   "partya" => "entityObject",
												   "count" => "decimalField",
												   "summa" => "decimalField"
);
?>