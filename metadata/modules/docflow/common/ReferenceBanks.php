<?php

$fields["BIK"] = array("base" => "stringField",
						"params" => array("title" => "БИК", "regs" => '^0[0-9]{0,8}$'),
						"file" => __FILE__,
						"collection" => "fields",
						"name" => "BIK"
);

$fields["KS"] = array("base" => "stringField",
						"params" => array("title" => "Корр. счет","regs" => '^[1-9][0-9]{0,19}$'),
						"file" => __FILE__,
						"collection" => "fields",
						"name" => "KS"
);

$fields["bankParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceBanks",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name",
				"show_float_div" => "true",
				"classTitle" => "Банки",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"additionalFields" => "isGroup",
				"sortOrder" => "title ASC",
				"hierarchy" => "true",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"childCondition" => "@isGroup=1",
				"parentEntity" => "ReferenceBanks_DocFlowApplication_Docs_"),
		"name" => "bankParent",
		"collection" => "fields",
		"file" => __FILE__
);


$groups["ReferenceBanks"] = array("title" =>"Банки",
		"name" => "ReferenceBanks",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("BIK","KS")
);

$models["ReferenceBanks"] = array("metaTitle" => "Банки",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceBanks",
								   "title" => "title",
								   "BIK" => "BIK",
								   "KS" => "KS",
								   "parent" => "bankParent",
								   "isGroup" => "isGroup"
);

?>