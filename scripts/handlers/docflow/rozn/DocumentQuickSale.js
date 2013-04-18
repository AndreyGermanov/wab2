//include scripts/handlers/docflow/core/Document.js
entity.barCodeClasses["ReferenceProducts"] = "ReferenceProducts";
entity.tbl = $O('{documentTableId}');
if (typeof(entity.tbl) != "undefined" && entity.tbl!=null) {
	entity.tbl.build();
	//if ('{name}'!="")		
		entity.tbl.calcTable();
	if ('{contragent}'!="")
		$O(entity.object_id+"_allDiscountSumma","").calc();
}
entity.entityImages = '{entityImages}';
entity.entityImages = entity.entityImages.evalJSON();