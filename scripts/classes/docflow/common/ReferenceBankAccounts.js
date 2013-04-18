var ReferenceBankAccounts = Class.create(Reference, {
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"] == this.node.id+"_bank") {
			var arr = params["value"].split("_");
			var className = arr.shift();
			var entityId = arr.pop();
			var objectid = className+"_"+this.module_id+"_"+entityId;
			var args = new Object;
			args["fields"] = new Object;
			args["fields"][0] = "BIK";
			args["fields"][1] = "KS";
			var obj = this;
			new Ajax.Request("index.php",{
				method: 'post',
				parameters: {
					ajax: true,
					object_id: objectid,
					hook: "getFields",
					arguments: Object.toJSON(args)
				},
				onSuccess: function(transport) {
					var response = trim(transport.responseText);					
					if (response[0]=="{") {
						var values = response.evalJSON();
						obj.setValuesFromArray(values);
					}
				}
			});
		}
	}
});