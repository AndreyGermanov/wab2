var DocumentContract = Class.create(Document, {
		
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.node.id+"_contragent") {
			if (params["value"]!=params["old_value"]) {
				var obj = $O(this.node.id+"_contragentAccount","");
				var fieldDefaults = new Object;
				fieldDefaults["contragent"] = params["value"];
				obj.fieldDefaults = fieldDefaults;
				obj.condition = "@contragent.@name="+params["value"].split("_").pop();
				obj.node.setAttribute("condition",obj.condition);
				obj.calcProperties = new Object;
				obj.calcProperties["contragent"] = params["value"];
				obj.calc();
			}
		}
		if (params["object_id"]==this.node.id+"_firm") {
			if (params["value"]!=params["old_value"]) {
				var obj = $O(this.node.id+"_firmAccount","");
				var fieldDefaults = new Object;
				fieldDefaults["contragent"] = params["value"];
				obj.fieldDefaults = fieldDefaults;
				obj.condition = "@contragent.@name="+params["value"].split("_").pop();
				obj.node.setAttribute("condition",obj.condition);
				obj.calcProperties = new Object;
				obj.calcProperties["contragent"] = params["value"];
				obj.calc();
			}
		}
		if (params["object_id"] == this.node.id+"_contractTemplate") {
			var args = new Object;
			args["contractTemplateId"] = params["value"];
			obj = this;
	        new Ajax.Request("index.php", {
	            method: "post",
	            parameters: {ajax: true, object_id: obj.object_id,
	                         hook: '4', arguments: Object.toJSON(args)},
	            onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response!="") {
                    	$O(obj.node.id+"_contract","").setValue(response);
                    }
	            }
	        });
		}
		if (params["object_id"]==this.node.id+"_summa") {
			if (params["value"]!=params["old_value"]) {
				var summa = parseFloat(params["value"]);
				var stNDS = parseFloat($O(this.node.id+"_stavkaNDS").getValue());
				if (!isNaN(summa) && !isNaN(stNDS) && stNDS!=0) {
					var summaNDS = parseFloat(summa/(100+stNDS)*stNDS);
					$O(this.node.id+"_NDS").setValue(summaNDS.toFixed(2),true);
				}
				$O(this.node.id+"_summa").setValue(summa.toFixed(2),true);
			}
		}		
		if (params["object_id"]==this.node.id+"_stavkaNDS") {
			if (params["value"]!=params["old_value"]) {
				var stNDS = parseFloat(params["value"]);
				var summa = parseFloat($O(this.node.id+"_summa").getValue());
				if (!isNaN(summa) && !isNaN(stNDS) && stNDS!=0) {
					var summaNDS = parseFloat(summa/(100+stNDS)*stNDS);
					$O(this.node.id+"_NDS").setValue(summaNDS.toFixed(2),true);
				}
				$O(this.node.id+"_summa").setValue(summa.toFixed(2),true);
			}
		}		
	}
});