var Reference = Class.create(Entity, {	
	TAB_CHANGED_processEvent: function($super,params) {
		if (params["tabset_id"]==this.tabsetName) {
			if (params["tab"]=="files") {
				if (!this.filesTabLoaded) {
					var tbl = $O(this.filesTableId,'');
					tbl.rebuild();
					this.filesTabLoaded = true;
				}
			}
			if (params["tab"]=="notes") {
				if (!this.notesTabLoaded) {
					var tbl = $O(this.notesTableId,'');
					tbl.rebuild();
					this.notesTabLoaded = true;
				}
			}
		}
	},
	
	Create_onClick: function(event) {
		$O(this.object_id,this.instance_id).show_context_menu("CreateObjectContextMenu_"+this.module_id+"_print",cursorPos(event).x-10,cursorPos(event).y-20*this.createObjectsCount,eventTarget(event).id,"$object->opener_object='"+this.object_id+"';");
	}		
});