//include scripts/handlers/core/WABEntity.js
obj = entity;
entity.objectid='{objectid}';
if ($O('DocFlowReferenceTable_'+entity.module_id+"_"+entity.objectid,'')!=null)
    $O('DocFlowReferenceTable_'+entity.module_id+"_"+entity.objectid,'').build();
entity.filesTableId = '{filesTableId}';
entity.filesTabLoaded = false;
entity.notesTableId = '{notesTableId}';
entity.notesTabLoaded = false;
entity.linksTreeId = '{linksTreeId}';
entity.linksTreeLoaded = false;
if ($I(entity.node.id+"_tagsTable")!=0 && typeof {tagsTableId}tbl != "undefined")
	$I(entity.node.id+"_tagsTable").value = {tagsTableId}tbl.getSingleValue();