<?php
$fields["productParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceProducts",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Номенклатура",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceProducts_DocFlowApplication_Docs_"),
		"name" => "productParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["productKind"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceProductKinds",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Виды номенклатуры",
				"editorType" => "WABWindow",
				"title" => "Вид номенклатуры",
				"fieldList" => "title",
				"sortOrder" => "title ASC",
				"list" => "true",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceProductKinds_DocFlowApplication_Docs_"),
		"name" => "productKind",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["productDimension"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceDimensions",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Единицы измерения",
				"editorType" => "WABWindow",
				"title" => "Единица измерения",
				"fieldList" => "title",
				"sortOrder" => "title ASC",
				"list" => "true",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"parentEntity" => "ReferenceDimensions_DocFlowApplication_Docs_"),
		"name" => "productDimension",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["productTitle"] = array("base" => "textField",
		"params" => array("title" => "Наименование"),
		"file" => __FILE__,
		"name" => "productTitle",
		"collection" => "fields"
);

$fields["productDescription"] = array("base" => "textField",
		"params" => array("title" => "Описание", "height" => "100%", "control_type" => "tinyMCE"),
		"file" => __FILE__,
		"name" => "productDescription",
		"collection" => "fields"
);

$fields["productNDS"] = array("base" => "integerField",
						"params" => array("title" => "НДС", "regs" => "^[1-9][0-9]{0,1}$"),
						"name" => "NDS",
						"collection" => "fields",
						"file" => __FILE__
);

$fields["productCost"] = array("base" => "decimalField",
		"params" => array("title" => "Цена"),
		"name" => "productCost",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["productCode"] = array("base" => "stringField",
		"params" => array("title" => "Код"),
		"name" => "productCode",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["productDImensions"] = array("base" => "arrayField",
		"name" => "productDimensions",
		"collection" => "fields",
		"file" => __FILE__,
		"params" =>
		array("title" => "Таблица коэффициентов пересчета единицы измерения")
);

$groups["ReferenceProducts"] = array("title" =>"Номенклатура",
		"name" => "ReferenceProducts",
		"file" => __FILE__,
		"collection" => "groups",
		"groups" => array("ReferenceProductsMain","ReferenceProductsDescription")
);

$groups["ReferenceProductsMain"] = array("title" =>"Номенклатура (основное)",
		"name" => "ReferenceProducts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("productCode","productTitle","productCost","productNDS","ProductKind","productDimension","productDescription","productParent")
);

$groups["ReferenceProductsDescription"] = array("title" =>"Номенклатура (описание)",
		"name" => "ReferenceProducts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => ("productDescription")
);

$groups["ReferenceProductsDimensions"] = array("title" =>"Номенклатура (единицы измерения)",
		"name" => "ReferenceProductsDimensions",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => ("productDimensions")
);

$models["ReferenceProducts"] = array("metaTitle" => "Номенклатура",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceProducts",
								   "code" => "productCode",
								   "title" => "productTitle",
								   "description" => "productDescription",
								   "isGroup" => "isGroup",
								   "kind" => "productKind",
								   "cost" => "productCost",
								   "dimension" => "productDimension",
								   "NDS" => "productNDS",
								   "photo" => "userPhoto",
								   "parent" => "productParent",
								   "table" => "productDimensions",
								   "groups" => array("ReferenceProductsMain","ReferenceProductsDescription")
);
?>