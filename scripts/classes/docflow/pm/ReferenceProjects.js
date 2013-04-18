var ReferenceProjects = Class.create(Reference, {
	TAB_CHANGED_processEvent: function($super,params) {
		if (params["tabset_id"]==this.tabsetName) {
			if (params["tab"]=="projectChanges") {
				if (!this.projectChangesTabLoaded) {
					var tbl = $O(this.projectChangesTableId,'');
					tbl.fieldAccess = this.projectChangesTableFieldAccess;
					tbl.fieldDefaults = this.projectChangesTableFieldDefaults;
					tbl.rebuild();
					this.projectChangesTabLoaded = true;
				}
			}
		}
		$super(params);
	}		
});