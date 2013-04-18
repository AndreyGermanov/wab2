//include scripts/handlers/docflow/core/Document.js
entity.tbl = $O("DocumentBloodAnalyzeTable_"+entity.module_id+"_"+entity.object_id.replace(/_/g,""));
if (entity.tbl!=null) {
    entity.tbl.build(true);
    entity.checkTable();
}
entity.helpTopic = '{helpTopic}';