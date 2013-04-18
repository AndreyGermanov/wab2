var SelectEntityFloatMenu = Class.create(Entity, {	
	selectValue: function(event,vals) {
		var elem = null;
		if (event!=null)
			elem = eventTarget(event);
		var values = "";
		if (vals!=null)
			values = vals;
		else
			values = elem.getAttribute("selectedValues");
		if (values[0]=="{" || values[0]=="[") {
			values = values.evalJSON();
			var o=null;
			for (o in values) {
				if (typeof values[o] != "function") {
					if ($O(o,'')!=null)
						$O(o,'').setValue(values[o]);					
				}
			}
		}
		removeContextMenu();
	},
	
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.object_id && params["value"]!="") {
			var args = new Object;
	        args["item"] = params["value"];
	        args["resultObject"] = this.resultObject;
	        args["resultFields"] = this.resultFields;
	        var obj = this;
	        new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: this.object_id, hook: '5', arguments: Object.toJSON(args)},                             
                onSuccess: function(transport) {
                	obj.selectValue(null,transport.responseText);
                }
	        });
		}
	},
	
	advanced_onClick: function(event) {
		var elem_id = "";
        if ($O(this.parent_object_id,'').module_id=="")
            elem_id = "EntitySelectWindow_"+this.node.id.replace(/_/g,'');
        else
            elem_id = "EntitySelectWindow_"+$O(this.parent_object_id,'').module_id+"_"+this.node.id.replace($O(this.parent_object_id,'').module_id+"_","").replace(/_/g,'');
        var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
        var obj = this;
        var params = new Object;        
        params["hook"] = "setParams";
        params["className"] = this.entityClass;
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: this.entityClass+"_"+this.module_id+"_List",
                         hook: 'getTableClass'},
            onSuccess: function(transport) {                    	
            	obj.tableClassName = transport.responseText;
                params["defaultClassName"] = obj.entityClass;
                params["tableClassName"] = obj.tableClassName;
                params["hierarchy"] = "true";
                params["treeClassName"] = "EntityTree";
                params["condition"] = "";
                params["childCondition"] = "";
                params["sortOrder"] = "";
                params["adapterId"] = "DocFlowDataAdapter_"+obj.module_id+"_List";
                params["fieldList"] = "title";
                params['present'] = "Выбор объекта";
                params["classTitle"] = "Объекты";
                params["editorType"] = "";
                params["selectGroup"] = false;
                params["parent_object_id"] = obj.object_id;
                getWindowManager().show_window(window_elem_id,elem_id,params,obj.object_id,obj.node.id,null,true);                                            
            }
        });        
	}
});