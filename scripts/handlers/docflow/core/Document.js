//include scripts/handlers/core/WABEntity.js
obj = entity;
entity.user = '{user}';
entity.appUser = '{appUser}';
entity.objectid='{objectid}';
entity.printFormsCount = {printFormsCount};
if (entity.printFormsCount>0)
	entity.firstPrintForm = '{firstPrintForm}';
if ($O('DocFlowDocumentTable_'+entity.module_id+"_"+entity.objectid,'')!=null)
    $O('DocFlowDocumentTable_'+entity.module_id+"_"+entity.objectid,'').build();
entity.filesTableId = '{filesTableId}';
entity.filesTabLoaded = false;
entity.notesTableId = '{notesTableId}';
entity.notesTabLoaded = false;
entity.linksTreeId = '{linksTreeId}';
entity.linksTreeLoaded = false;
entity.registerConfirmation = '{registerConfirmation}';
if ($I(entity.node.id+"_tagsTable")!=0 && typeof {tagsTableId}tbl != "undefined")
	$I(entity.node.id+"_tagsTable").value = {tagsTableId}tbl.getSingleValue();