//include scripts/handlers/docflow/core/Reference.js
if ($I(entity.node.id+"_tagsTable")!=0 && typeof {tagsTableId}tbl != "undefined")
	$I(entity.node.id+"_tagsTable").value = {tagsTableId}tbl.getSingleValue();