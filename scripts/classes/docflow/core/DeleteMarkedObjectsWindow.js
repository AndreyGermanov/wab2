var DeleteMarkedObjectsWindow = Class.create(Entity, {

	CONTROL_HAS_FOCUSED_processEvent: function(params) {
		var elem = $I(params["object_id"]);
		var par = $O(params["object_id"],'').parent_object_id;
		if (par==this.deletedTbl.object_id) {			
			var ent = this.deletedTbl.getItem(elem.getAttribute("row"),0).getValue();
			if (ent!=null && ent!="" && ent!='header') {
				this.blockingTbl.collection = ent;
				this.blockingTbl.rebuild();
			}
		}
	}	
});