<?php
$fields["contractParent"] = array("type" => "entity",
 												"params" => array("type" => "entity",
										 						  "className" => "DocumentContract",
																  "tableClassName" => "DocFlowDocumentTable",
																  "additionalFields" => "name,isGroup",
																  "show_float_div" => "true",
																  "classTitle" => "Договор",
																  "editorType" => "WABWindow",
																  "title" => "Родитель",
																  "fieldList" => "title Наименование",
																  "sortOrder" => "title ASC",
																  "width" => "100%",
																  "adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
																  "condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
																  "parentEntity" => "DocumentContract_DocFlowApplication_Docs_"),
																  "name" => "contractParent",
																  "collection" => "fields",
																  "file" => __FILE__
);

$fields["contractTemplate"] = array("type" => "entity",
		"params" => array("type" => "entity",
							"className" => "ReferenceContractTemplates",
							"tableClassName" => "DocFlowReferenceTable",
							"additionalFields" => "name,isGroup",
							"show_float_div" => "true",
							"classTitle" => "Шаблон договора",
							"editorType" => "WABWindow",
							"title" => "Шаблон договора",
							"fieldList" => "title Наименование",
							"sortOrder" => "title ASC",
							"width" => "100%",
							"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
							"condition" => "@parent IS NOT EXISTS",
							"parentEntity" => "ReferenceContractTemplate_DocFlowApplication_Docs_"),
		"name" => "contractTemplate",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["summa"] = array ("name" => "summa", "collection" => "fields", "base" => "decimalField",
		                   "params" => array ("title" => "Сумма"));

$fields["NDS"] = array ("name" => "NDS", "collection" => "fields", "base" => "decimalField",
						 "params" => array ("title" => "НДС"));

$fields["stavkaNDS"] = array ("name" => "stavkaNDS", "collection" => "fields", "base" => "decimalField",
							   "params" => array ("title" => "Ставка НДС"));

$models["DocumentContract"] = array("metaTitle" => "Договоры",
								    "file" => __FILE__,
								    "collection" => "models",
								    "name" => "DocumentContract",
								    "title" => "title",
								    "isGroup" => "isGroup",
								    "parent" => "contractParent",
								    "contragent" => "referenceContragent",
								    "contragentAccount" => "bankAccount",
								    "firm" => "firm",
								    "firmAccount" => "bankAccount",
								    "contract" => "contractText",
								    "contractTemplate" => "contractTemplate",
								    "manager" => "referenceUser",
								    "summa" => "summa",
								    "stavkaNDS" => "stavkaNDS",
								    "NDS" => "NDS"
);
?>