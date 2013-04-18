var DocumentOrder = Class.create(Document, {

	TAB_CHANGED_processEvent: function($super,params) {
		if (params["tabset_id"]==this.tabsetName) {
			if (params["tab"]=="orderChanges") {
				if (!this.orderChangesTabLoaded) {
					var tbl = $O(this.orderChangesTableId,'');
					tbl.fieldAccess = this.orderChangesTableFieldAccess;
					tbl.fieldDefaults = this.orderChangesTableFieldDefaults;
					tbl.rebuild();
					this.orderChangesTabLoaded = true;
				}
			}
		}
		$super(params);
	}		
});