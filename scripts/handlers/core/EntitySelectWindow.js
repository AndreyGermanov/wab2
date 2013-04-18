//include /scripts/handlers/core/WABEntity.js
entity.editorType = '{editorType}';
entity.condition = '{condition}';
entity.parentEntity = '{parentEntity}';
entity.tableObject = '{tableObject}';
entity.tbl = $O('{tableClassName}_{objectid}','');
if (entity.fieldAccess!='') {
	entity.tbl.fieldAccess = entity.fieldAccess;
}
if (entity.fieldDefaults!='') {
	entity.tbl.fieldDefaults = entity.fieldDefaults;
}
entity.tbl.build();
entity.tbl.selectCurrentEntity();