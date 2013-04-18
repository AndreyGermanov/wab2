var TagsTable = Class.create(DataTable, {
    getSingleValue: function() {
    	var arr = new Array;
    	var rows = this.rows;
    	var c=null;
    	var c1=null;
    	for (c in rows) {
    		if (c==0)
    			continue;
    		if (typeof(rows[c])=="function")
    			continue;
    		var str = new Array;
    		for (c1 in rows[c]["cells"]) {
    			if (typeof(rows[c1])=="function")
    				continue;
    			str[str.length] = rows[c]["cells"][c1]["value"];    			
    		}
    		arr[arr.length] = str.join("~");
    	}    	
    	return arr.join("|");
    },
    
	CONTROL_VALUE_CHANGED_processEvent: function($super,params) {
		if ($O(params["object_id"],"").parent_object_id == this.object_id) {
			var column = $O(params["object_id"],"").node.parentNode.getAttribute("column");
			if (column!=null) {
				var row = $O(params["object_id"],"").node.parentNode.getAttribute("row");
				var item = this.getItem(row,1);				
				val = params["value"];
				var objectClass = this.entityObject.split("_").shift();
				var resultObject = item.object_id.split("_");
				resultObject.pop();
				resultObject = resultObject.join("_");
				var args = new Object;
				args["tag"] = val;
				if (column=="name") {
			        new Ajax.Request("index.php", {
			            method:"post",
			            parameters: {ajax: true, object_id: this.object_id,hook: '3', arguments: Object.toJSON(args)},
			            onSuccess: function(transport)
			            {
			            	var response = transport.responseText;
			                var result  = response.evalJSON(true);
			                if (result["properties"]!=null) {
			    				item.node.setAttribute("properties",result["properties"]);
			    				item.node.setAttribute("type",result["type"]);
			    				if (item.getValue()=="" && result["value"]!="") {
			    					item.node.setAttribute("value",result["value"]);
				    				item.setValue(result["value"]);
			    				}
			    				item.node.innerHTML = "";
			    				if (result["type"]!="list" && result["type"]!="listedit" && result["type"]!="boolean")
			    					item.node.setAttribute("selectOptions","resultObject="+resultObject+"~entityClass="+objectClass+"~searchField="+val+"~displayField="+val+"~resultFields="+val+"|value");
			    				item.build(true);
			                } else 
			                	item.node.setAttribute("selectOptions","resultObject="+resultObject+"~entityClass="+objectClass+"~searchField="+val+"~displayField="+val+"~resultFields="+val+"|value");
			            }
				});
			}
		}
		}
		if (params["object_id"]==this.object_id+"_tagGroup") {
			var obj = this;
        	var args = new Object;
        	args["tagGroup"] = params["value"];
        	args["entityObject"] = this.entityObject;
	        new Ajax.Request("index.php", {
	            method:"post",
	            parameters: {ajax: true, object_id: this.object_id,hook: '4', arguments: Object.toJSON(args)},
	            onSuccess: function(transport) {
	            	var response = transport.responseText;
	            	obj.deleteRows();
	            	eval(response);
	            	obj.build();
	            }
	        });
		}
	},
	
    addButton_onClick: function($super,event) {
    	var maxRow = this.getMaxRow();
    	this.emptyrow['cells'][0]['control_properties'] = "deactivated=true,input_class=input1,selectClass=SelectTagFloatMenu,selectOptions=entityClasszozo"+this.entityObject.split("_").shift()+"~resultFieldszozoname~resultObjectzozo"+this.object_id+"_"+maxRow+",showOnKeyPress=true,hideSelectButton=true";
    	$super(event);
    }
});