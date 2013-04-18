//include scripts/handlers/docflow/core/Reference.js
var tbl = $O("BloodAnalyzeTypesTable_"+entity.module_id+"_"+entity.object_id.replace(/_/g,""));
tbl.build(true);
tbl.sort();