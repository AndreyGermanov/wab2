var RegistryReports = Class.create(Entity, {
	
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.node.id+"_registry") {
			if (params["value"]!="") {
				var args = new Object;
				args["template"] = "templates/docflow/core/RegistryReportWindow.html";
				this.insertEntity(params["value"]+"_"+this.module_id+"_reg",this.node.id+"_content",args);
			}
		}	
	}
});