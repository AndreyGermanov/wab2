var ReferenceDepartments = Class.create(Reference, {
	TAB_CHANGED_processEvent: function($super,params) {
		if (params["tabset_id"]==this.tabsetName) {
			if (params["tab"]=="places") {
				if (!this.placesTabLoaded) {
					var tbl = $O(this.placesTableId,'');
					tbl.fieldAccess = this.placesTableFieldAccess;
					tbl.fieldDefaults = this.placesTableFieldDefaults;
					tbl.entityImages = this.entityImages;
					tbl.rebuild();
					this.placesTabLoaded = true;
				}
			}
		}
		$super(params);
	}
});