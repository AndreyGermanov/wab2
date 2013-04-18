//include scripts/handlers/docflow/core/Document.js
entity.firmAccountFieldAccess = '{firmAccountFieldAccess}';
entity.firmAccountFieldAccess = entity.firmAccountFieldAccess.evalJSON();
entity.firmAccountFieldDefaults = '{firmAccountFieldDefaults}';
entity.firmAccountFieldDefaults = entity.firmAccountFieldDefaults.evalJSON();
entity.contragentAccountFieldAccess = '{contragentAccountFieldAccess}';
entity.contragentAccountFieldAccess = entity.contragentAccountFieldAccess.evalJSON();
entity.contragentAccountFieldDefaults = '{contragentAccountFieldDefaults}';
entity.contragentAccountFieldDefaults = entity.contragentAccountFieldDefaults.evalJSON();
entity.barCodeClasses["ReferenceProducts"] = "ReferenceProducts";
entity.tbl = $O('{documentTableId}');
if (typeof(entity.tbl) != "undefined" && entity.tbl!=null) {
	entity.tbl.build();
}
entity.entityImages = '{entityImages}';
entity.entityImages = entity.entityImages.evalJSON();