var DocumentTask = Class.create(Document, {

	TAB_CHANGED_processEvent: function($super,params) {
		if (params["tabset_id"]==this.tabsetName) {
			if (params["tab"]=="taskChanges") {
				if (!this.taskChangesTabLoaded) {
					var tbl = $O(this.taskChangesTableId,'');
					tbl.fieldAccess = this.taskChangesTableFieldAccess;
					tbl.fieldDefaults = this.taskChangesTableFieldDefaults;
					tbl.rebuild();
					this.taskChangesTabLoaded = true;
				}
			}
		}
		$super(params);
	}		
});