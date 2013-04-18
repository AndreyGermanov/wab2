var DocumentInvoice = Class.create(Document, {
	
	   dispatchEvent: function($super,event,params) {        
	        $super(event,params);        
	        if (event == "DATATABLE_VALUE_CHANGED") {
	            if (params["parent"]==this.object_id && params["object_id"]=="DocumentInvoiceTable_"+this.module_id+"_"+this.name) {
	                if (params["object_id"] == "DocumentInvoiceTable_"+this.module_id+"_"+this.name) {
	                    $I(this.node.id+"_documentTable").value = params["value"].replace(/xox/g,"=").replace(/yoy/g,",");
	                }
	            }
	        }                
	    },
	
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
		if (params["object_id"]==this.node.id+"_includeNDS") {
			var i=0;
			if (params["value"]=="1") {
				for (i=1;i<this.tbl.rows.length;i++) {
					var summa = parseFloat($O(this.tbl.object_id+"_"+i+"_summa").getValue());
					var stNDS = parseFloat($O(this.tbl.object_id+"_"+i+"_stNDS").getValue());
					var summaNDS = summa/(100+stNDS)*stNDS;
					var total = summa;
					$O(this.tbl.object_id+"_"+i+"_summaNDS").setValue(summaNDS.toFixed(2),true);					
					$O(this.tbl.object_id+"_"+i+"_total").setValue(total.toFixed(2),true);
					this.tbl.rows[i]["cells"][this.tbl.getColumnNumber("summaNDS")]["value"] = summaNDS.toFixed(2);
					this.tbl.rows[i]["cells"][this.tbl.getColumnNumber("total")]["value"] = total.toFixed(2);					
				}
				this.tbl.calcTable();
			} else {
				for (i=1;i<this.tbl.rows.length;i++) {
					var summa = parseFloat($O(this.tbl.object_id+"_"+i+"_summa").getValue());
					var stNDS = parseFloat($O(this.tbl.object_id+"_"+i+"_stNDS").getValue());
					var summaNDS = summa/100*stNDS;
					var total = summa+summaNDS;
					$O(this.tbl.object_id+"_"+i+"_summaNDS").setValue(summaNDS.toFixed(2),true);					
					$O(this.tbl.object_id+"_"+i+"_total").setValue(total.toFixed(2),true);		
					this.tbl.rows[i]["cells"][this.tbl.getColumnNumber("summaNDS")]["value"] = summaNDS.toFixed(2);
					this.tbl.rows[i]["cells"][this.tbl.getColumnNumber("total")]["value"] = total.toFixed(2);					
				}				
				this.tbl.calcTable();
			}
		}
		if (params["object_id"]==this.node.id+"_receiveBarCodes") {
			this.receiveBarCodes = params["value"];
			if (params["value"]=="1") {
				this.scanInterval = getTopWindow().setInterval('processUser()',5000);
			} else {
				getTopWindow().clearInterval(this.scanInterval);				
			}
		}
	},
	
	SCAN_CODE_processEvent:function(params) {
		if (this.receiveBarCodes=="1" && params["object_class"]=="ReferenceProducts") {
			var args = new Object;
			args["id"] = params["object_class"]+"_"+this.module_id+"_"+params["i"];
			var obj = this;
		    new Ajax.Request("index.php", {
		        method:"post",
		        parameters: {ajax: true, object_id: obj.object_id,hook: '10',arguments: Object.toJSON(args)},
		        onSuccess: function(transport) {
		        	var response = transport.responseText;
		        	var product = response.evalJSON();
		        	if (product["title"]!= null) {
		        		var found = false;
		        		for (var i=0;i<obj.tbl.rows.length;i++) {
		        			if (obj.tbl.rows[i]["cells"][0]["value"]==product["title"]) {
		        				found = true;
		        				obj.tbl.rows[i]["cells"][4]["value"] = parseFloat(obj.tbl.rows[i]["cells"][4]["value"])+1;
		        				var table_row_num = obj.tbl.rows[i]["cells"][4]["node"].getAttribute("row");
		        				$O(obj.tbl.node.id+"_"+table_row_num+"_count").setValue(obj.tbl.rows[i]["cells"][4]["value"]);
		        				break;
		        			}
		        		}
		        		if (!found) {
		        			var row = obj.tbl.emptyrow;
		        			row["cells"][0]["value"] = product["title"];
		        			row["cells"][1]["value"] = product["code"];
		        			row["cells"][2]["value"] = product["dimension"];
		        			row["cells"][3]["value"] = parseFloat(product["cost"]).toFixed(2);
		        			row["cells"][4]["value"] = 1;
		        			row["cells"][6]["value"] = parseFloat(product["NDS"]);
		        			var includeNDS = $O(obj.object_id+"_includeNDS").getValue();
		        			if (includeNDS=="1") {
		        				row["cells"][5]["value"] = parseFloat(product["cost"])*1;
		        				row["cells"][7]["value"] = parseFloat(row["cells"][5]["value"]/(parseFloat(product["NDS"])+100)*parseFloat(product["NDS"])).toFixed(2);
		        				row["cells"][8]["value"] = row["cells"][5]["value"].toFixed(2);
		        				row["cells"][5]["value"] = row["cells"][5]["value"].toFixed(2);
		        			} else {
		        				row["cells"][5]["value"] = parseFloat(product["cost"]*1);
		        				row["cells"][7]["value"] = parseFloat(row["cells"][5]["value"]/(100)*parseFloat(product["NDS"]));
		        				row["cells"][8]["value"] = parseFloat(row["cells"][5]["value"]+row["cells"][7]["value"]).toFixed(2);
		        				row["cells"][5]["value"] = row["cells"][5]["value"].toFixed(2);
		        				row["cells"][7]["value"] = row["cells"][7]["value"].toFixed(2);		        				
		        			}		       
		        			obj.tbl.appendRow(row);
		        			obj.tbl.calcTable();
		        		}
		        	}		        	
		        }
		    });
		}
	},
	
	onRemoveWindow: function(window) {
		if (this.scanInterval!=null)
			getTopWindow().clearInterval(this.scanInterval);
	}
});