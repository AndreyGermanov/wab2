var DocumentQuickSale = Class.create(Document, {
	
   dispatchEvent: function($super,event,params) {        
        $super(event,params);        
        if (event == "DATATABLE_VALUE_CHANGED") {
            if (params["parent"]==this.object_id && params["object_id"]=="DocumentQuickSaleTable_"+this.module_id+"_"+this.name) {
                if (params["object_id"] == "DocumentQuickSaleTable_"+this.module_id+"_"+this.name) {
                    $I(this.node.id+"_documentTable").value = params["value"].replace(/xox/g,"=").replace(/yoy/g,",");
                }
            }
        }                
    },
	
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.object_id+"_prihodSumma") {
			this.tbl.calcTable();
		}
		if (params["object_id"]==this.object_id+"_contragent") {
			var args = new Array;
			args["contragent"] = params["value"];			
			$O(this.object_id+"_allDiscountSumma","").calc(args);
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
    
    Register_onClick: function($super,event) {
    	$super(event);
    	if ($I(this.node.id+"_registered").value==1) {
    		$I(this.node.id+"_Print").style.display = "";
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
		        				obj.tbl.rows[i]["cells"][2]["value"] = parseFloat(obj.tbl.rows[i]["cells"][2]["value"])+1;
		        				var table_row_num = obj.tbl.rows[i]["cells"][2]["node"].getAttribute("row");
		        				$O(obj.tbl.node.id+"_"+table_row_num+"_count").setValue(obj.tbl.rows[i]["cells"][2]["value"]);
		        				break;
		        			}
		        		}
		        		if (!found) {
		        			var row = obj.tbl.emptyrow;
		        			row["cells"][0]["value"] = product["title"];
		        			row["cells"][1]["value"] = parseFloat(product["cost"]).toFixed(2);
		        			row["cells"][2]["value"] = 1;
	        				row["cells"][3]["value"] = parseFloat(product["cost"]).toFixed(2);
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