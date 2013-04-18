var GlobalSearchTable = Class.create(EntityDataTable, {
        
    refreshButton_onClick: function(event) {
        this.rebuild();
    },
    
    findButton_onClick: function(event) {
    	this.searchText = $I(this.node.id+"_findField").value;
    	this.onLoad();
    	this.rebuild();
    },

	onLoad: function() {
		this.params = new Object;
		this.params["searchText"] = this.searchText;
		this.params["classesList"] = this.classesList.join(",");
		this.params["fieldsList"] = this.fieldsList.join(",");
	},
	
    optionsButton_onClick: function(event) {   
        if (current_context_menu!=null)
            removeContextMenu(event,null,true);
        params = new Object;
        params["hook"] = "setParams";
        if (this.classesList!="")
        	params["classesList"] = this.classesList.join(",");
        if (this.fieldsList!="")
        	params["fieldsList"] = this.fieldsList.join(",");
        getWindowManager().show_window("Window_GlobalSearchSettings","GlobalSearchSettings_"+this.module_id+"_global",params,this.object_id,this.node.id,null,true);
    },
	
	saveSettingsButton_onClick: function(event) {
		var obj = this;     		
		this.sortOrder = this.sortOrder.replace(/null/g,'');
		var params = new Object;
		params["classesList"] = this.classesList.join(",");
		params["fieldsList"] = this.fieldsList.join(",");
		new Ajax.Request("index.php", {
			method:"post",
			parameters: {ajax: true, object_id: obj.object_id,hook: '5', arguments: Object.toJSON(params)},
			onSuccess: function(transport)
			{                            
			}
		});		
	}
});