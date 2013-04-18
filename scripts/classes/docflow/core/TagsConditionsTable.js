var TagsConditionsTable = Class.create(DataTable, {
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
    			str[str.length] = rows[c]["cells"][c1]["value"].replace(/\r\n/g,"").replace(/\n/g,"");    			
    		}
    		arr[arr.length] = str.join("~");
    	}    		    
    	return arr.join("|");
    },
    
	CONTROL_VALUE_CHANGED_processEvent: function($super,params) {
		if ($O(params["object_id"],"").parent_object_id == this.object_id) {
			var column = $O(params["object_id"],"").node.parentNode.getAttribute("column");
			var row = $O(params["object_id"],"").node.parentNode.getAttribute("row");
			var val = params["value"];
			if (column=="tagName") {
				var item = this.getItem(row,2);				
				if (item.node.getAttribute("type")=="string") {
					var objectClass = this.entityObject.split("_").shift();
					var resultObject = item.object_id.split("_");
					resultObject.pop();
					resultObject = resultObject.join("_");
					item.node.setAttribute("selectOptions","resultObject="+resultObject+"~entityClass="+objectClass+"~searchField="+val+"~displayField="+val+"~resultFields="+val+"|tagValue");
				}
			}
			if (column=="tagCondition") {				
				var item = this.getItem(row,2);
				if (val=="inList" || val=="notInList") {
					if (item.type!="array") {
						item.type = "array";
						item.module_id = this.module_id;
						item.node.setAttribute("type","array");						
						item.node.setAttribute("properties","width=100%");
						item.node.setAttribute("itemPrototype","width=100%,type=string");
						item.node.setAttribute("value",item.getValue());
						item.node.innerHTML = "";
						item.build();
					}
				} else {
					if (item.type=="array") {
						item.type = "string";
						item.module_id = this.module_id;
						item.node.setAttribute("type","string");
						item.node.setAttribute("value",item.value.split("~").shift());
						item.node.innerHTML = "";
						item.build();
					}
				}
			}
		}
	}        
});