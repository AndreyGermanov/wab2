var RegistrySettingsDialog = Class.create(Entity, {
	
	fillDialog: function() {
		var printFieldList = $I(this.node.id+"_printFields_value");
		printFieldList.options[printFieldList.selectedIndex] = null;
		var allPrintFieldList = $I(this.node.id+"_allPrintFields_value");
		allPrintFieldList.options[allPrintFieldList.selectedIndex] = null;
		var sortFieldList = $I(this.node.id+"_sortFields_value");
		sortFieldList.options[sortFieldList.selectedIndex] = null;
		var allSortFieldList = $I(this.node.id+"_allSortFields_value");
		allSortFieldList.options[allSortFieldList.selectedIndex] = null;
		var groupFieldList = $I(this.node.id+"_groupFields_value");
		groupFieldList.options[groupFieldList.selectedIndex] = null;
		var allGroupFieldList = $I(this.node.id+"_allGroupFields_value");
		allGroupFieldList.options[allGroupFieldList.selectedIndex] = null;
		var totalFieldList = $I(this.node.id+"_totalFields_value");
		totalFieldList.options[totalFieldList.selectedIndex] = null;
		var allTotalFieldList = $I(this.node.id+"_allTotalFields_value");
		allTotalFieldList.options[allTotalFieldList.selectedIndex] = null;
		
		var opt = null;
		var o=null;
		for (o in this.printProfile["printFields"]) {
			if (typeof this.printProfile["printFields"][o] != "function") {
				if (this.allFields[o]!=null) {
					opt = new Option(this.allFields[o],o);
					printFieldList.add(opt,null);
				}
			}
		}
		for (o in this.printProfile["sortFields"]) {
			if (typeof this.printProfile["sortFields"][o] != "function") {
				if (this.allFields[o]!=null) {
					opt = new Option(this.allFields[o],o);
					sortFieldList.add(opt,null);
				}
			}
		}		
		for (o in this.printProfile["groups"]) {
			if (typeof this.printProfile["groups"][o] != "function") {
				if (this.allFields[o]!=null) {
					opt = new Option(this.allFields[o],o);
					groupFieldList.add(opt,null);
				}
			}
		}
		for (o in this.printProfile["totals"]["totals"]) {
			if (typeof this.printProfile["totals"]["totals"][o] != "function") {
				if (this.allFields[o]!=null) {
					opt = new Option(this.allFields[o],this.printProfile["totals"]["totals"][o]);
					totalFieldList.add(opt,null);
				}
			}
		}				
		for (o in this.allFields) {
			if (typeof this.allFields[o] != 'function') {
				if (this.printProfile["printFields"]==null || this.printProfile["printFields"][o] == null) {
					opt = new Option(this.allFields[o],o);
					allPrintFieldList.add(opt,null);
				}
				if (this.printProfile["sortFields"]==null || this.printProfile["sortFields"][o] == null) {
					opt = new Option(this.allFields[o],o);
					allSortFieldList.add(opt,null);
				}
				if (this.printProfile["groups"]==null || this.printProfile["groups"][o] == null) {
					opt = new Option(this.allFields[o],o);
					allGroupFieldList.add(opt,null);
				}
				if (this.printProfile["totals"]["totals"]==null || this.printProfile["totals"]["totals"][o] == null) {
					opt = new Option(this.allFields[o],o);
					allTotalFieldList.add(opt,null);
				}
			}
		}
        $O(this.node.id+"_totalFontFace").setValue(this.printProfile["totals"]["fontFace"]);
        $O(this.node.id+"_totalFontSize").setValue(this.printProfile["totals"]["fontSize"]);
        $O(this.node.id+"_totalFontWeight").setValue(this.printProfile["totals"]["fontWeight"]);
        $O(this.node.id+"_totalFontColor").setValue(this.printProfile["totals"]["fontColor"]);
        $O(this.node.id+"_totalBgColor").setValue(this.printProfile["totals"]["bgColor"]);
	},
	
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.node.id+"_printFields") {
			if (params["old_value"]!=null && this.printProfile["printFields"][params["old_value"]]!=null)
				this.printProfile["printFields"][params["old_value"]]["size"] = $O(this.node.id+"_printFieldSize").getValue();
			if (params["value"]!=null && this.printProfile["printFields"][params["value"]]!=null)
				$O(this.node.id+"_printFieldSize").setValue(this.printProfile["printFields"][params["value"]]["size"]);			
		}
		if (params["object_id"]==this.node.id+"_sortFields") {
			if (params["old_value"]!=null && this.printProfile["sortFields"][params["old_value"]]!=null)
				this.printProfile["sortFields"][params["old_value"]] = $O(this.node.id+"_sortOrder").getValue();
			if (params["value"]!=null && this.printProfile["sortFields"][params["value"]]!=null)
				$O(this.node.id+"_sortOrder").setValue(this.printProfile["sortFields"][params["value"]]);			
		}
		if (params["object_id"]==this.node.id+"_groupFields") {
			if (params["old_value"]!=null && this.printProfile["groups"][params["old_value"]]!=null) {
				this.printProfile["groups"][params["old_value"]] = new Array;
				this.printProfile["groups"][params["old_value"]]["fontFace"] = $O(this.node.id+"_fontFace").getValue();
				this.printProfile["groups"][params["old_value"]]["fontSize"] = $O(this.node.id+"_fontSize").getValue();
				this.printProfile["groups"][params["old_value"]]["fontWeight"] = $O(this.node.id+"_fontWeight").getValue();
				this.printProfile["groups"][params["old_value"]]["fontColor"] = $O(this.node.id+"_fontColor").getValue();
				this.printProfile["groups"][params["old_value"]]["bgColor"] = $O(this.node.id+"_bgColor").getValue();
			}
			if (params["value"]!=null && this.printProfile["groups"][params["value"]]!=null) {
				$O(this.node.id+"_fontFace").setValue(this.printProfile["groups"][params["value"]]["fontFace"]);
				$O(this.node.id+"_fontSize").setValue(this.printProfile["groups"][params["value"]]["fontSize"]);
				$O(this.node.id+"_fontWeight").setValue(this.printProfile["groups"][params["value"]]["fontWeight"]);
				$O(this.node.id+"_fontColor").setValue(this.printProfile["groups"][params["value"]]["fontColor"]);
				$O(this.node.id+"_bgColor").setValue(this.printProfile["groups"][params["value"]]["bgColor"]);
			}
		}
		if (params["object_id"]==this.node.id+"_totalFontFace")
			this.printProfile["totals"]["fontFace"] = params["value"];
		if (params["object_id"]==this.node.id+"_totalFontSize")
			this.printProfile["totals"]["fontSize"] = params["value"];
		if (params["object_id"]==this.node.id+"_totalFontWeight")
			this.printProfile["totals"]["fontWeight"] = params["value"];
		if (params["object_id"]==this.node.id+"_totalFontColor")
			this.printProfile["totals"]["fontColor"] = params["value"];
		if (params["object_id"]==this.node.id+"_totalBgColor")
			this.printProfile["totals"]["bgColor"] = params["value"];
		
		var printFieldList = $I(this.node.id+"_printFields_value");
		var sortFieldList = $I(this.node.id+"_sortFields_value");
		var groupFieldList = $I(this.node.id+"_groupFields_value");
		
		if (printFieldList.options[printFieldList.selectedIndex]!=null) {
			this.printProfile["printFields"][printFieldList.options[printFieldList.selectedIndex]["value"]]["size"] = $O(this.node.id+"_printFieldSize").getValue();
		}

		if (sortFieldList.options[sortFieldList.selectedIndex]!=null) {
			this.printProfile["sortFields"][sortFieldList.options[sortFieldList.selectedIndex]["value"]] = $O(this.node.id+"_sortOrder").getValue();
		}
		
		if (groupFieldList.options[groupFieldList.selectedIndex]!=null) {
			if (params["object_id"]==this.node.id+"_fontFace")
				this.printProfile["groups"][groupFieldList.options[groupFieldList.selectedIndex]["value"]]["fontFace"] = $O(this.node.id+"_fontFace").getValue();
			if (params["object_id"]==this.node.id+"_fontSize")
				this.printProfile["groups"][groupFieldList.options[groupFieldList.selectedIndex]["value"]]["fontSize"] = $O(this.node.id+"_fontSize").getValue();
			if (params["object_id"]==this.node.id+"_fontWeight")
				this.printProfile["groups"][groupFieldList.options[groupFieldList.selectedIndex]["value"]]["fontWeight"] = $O(this.node.id+"_fontWeight").getValue();
			if (params["object_id"]==this.node.id+"_fontColor")
				this.printProfile["groups"][groupFieldList.options[groupFieldList.selectedIndex]["value"]]["fontColor"] = $O(this.node.id+"_fontColor").getValue();
			if (params["object_id"]==this.node.id+"_bgColor")
				this.printProfile["groups"][groupFieldList.options[groupFieldList.selectedIndex]["value"]]["bgColor"] = $O(this.node.id+"_bgColor").getValue();
		}		
		
		if ($O(params["object_id"],"").parent_object_id == this.object_id) {
			var arr = params["object_id"].split("_");
			var ending = arr.pop();
			var obj_name = arr.join("_");
			if (ending=="select") {
				var val = $O(obj_name+"_select","").getValue();
				var item = $O(obj_name+"_condition");
				var item1 = $O(obj_name+"_condition_2");
				if (val==" IN " || val==" NOT IN ") {
					item1.node.style.display = "none";
					if (item.type!="array") {
						item.type = "array";
						item.node.setAttribute("type","array");
						item.node.setAttribute("itemPrototype",item.node.getAttribute("properties"));						
						item.node.setAttribute("properties","");
						item.node.setAttribute("value",item.value);
						item.node.innerHTML = "";
						item.build();
					}
				} else {					
					if (val==" BETWEEN ") {
						item1.node.style.display = "";
					} else {
						item1.node.style.display = "none";
					}
					if (item.type=="array") {
						item.type = item.node.getAttribute("initialType");
						item.node.setAttribute("type",item.type);
						item.node.setAttribute("properties",item.node.getAttribute("itemPrototype"));
						item.node.setAttribute("value",item.value.split("~").shift());
						item.node.innerHTML = "";
						item.build();
					}
				}
			}
		}		
	},
	
	printFieldsMoveUp_onClick: function(event) {
        move_select_item($I(this.node.id+"_printFields_value").selectedIndex,$I(this.node.id+"_printFields_value"),"up");
    },

    printFieldsMoveDown_onClick: function(event) {
        move_select_item($I(this.node.id+"_printFields_value").selectedIndex,$I(this.node.id+"_printFields_value"),"down");
    },	
    
    printFieldsMoveLeft_onClick: function(event) {    	
    	var value = $O(this.node.id+"_allPrintFields","").getValue();
    	if (this.printProfile["printFields"]==null)
    		this.printProfile["printFields"] = new Object;
    	if (this.printProfile["printFields"][value]==null) {
    		this.printProfile["printFields"][value] = new Object;
    		this.printProfile["printFields"][value]["size"] = "10%";
    	}    	
        move_select_item_to_list($I(this.node.id+"_allPrintFields_value").selectedIndex,$I(this.node.id+"_allPrintFields_value"),$I(this.node.id+"_printFields_value"));
        $O(this.node.id+"_printFieldSize").setValue("10%");
    },

    printFieldsMoveRight_onClick: function(event) {
    	var value = $O(this.node.id+"_printFields","").getValue();
    	if (this.printProfile["printFields"][value]!=null)
    		this.printProfile["printFields"][value] = null;
        move_select_item_to_list($I(this.node.id+"_printFields_value").selectedIndex,$I(this.node.id+"_printFields_value"),$I(this.node.id+"_allPrintFields_value"));
        $O(this.node.id+"_printFieldSize").setValue("");
    },
        
	sortFieldsMoveUp_onClick: function(event) {
        move_select_item($I(this.node.id+"_sortFields_value").selectedIndex,$I(this.node.id+"_sortFields_value"),"up");
    },

    sortFieldsMoveDown_onClick: function(event) {
        move_select_item($I(this.node.id+"_sortFields_value").selectedIndex,$I(this.node.id+"_sortFields_value"),"down");
    },	
    
    sortFieldsMoveLeft_onClick: function(event) {    	
    	var value = $O(this.node.id+"_allSortFields","").getValue();
    	if (this.printProfile["sortFields"]==null)
    		this.printProfile["sortFields"] = new Object;
    	if (this.printProfile["sortFields"][value]==null) {
    		this.printProfile["sortFields"][value] = "ASC";
    	}    	
        move_select_item_to_list($I(this.node.id+"_allSortFields_value").selectedIndex,$I(this.node.id+"_allSortFields_value"),$I(this.node.id+"_sortFields_value"));
        $O(this.node.id+"_sortOrder").setValue("ASC");
    },

    sortFieldsMoveRight_onClick: function(event) {
    	var value = $O(this.node.id+"_sortFields","").getValue();
    	if (this.printProfile["sortFields"][value]!=null)
    		this.printProfile["sortFields"][value] = null;
        move_select_item_to_list($I(this.node.id+"_sortFields_value").selectedIndex,$I(this.node.id+"_sortFields_value"),$I(this.node.id+"_allSortFields_value"));
        $O(this.node.id+"_sortOrder").setValue("");
    },
    
	groupFieldsMoveUp_onClick: function(event) {
        move_select_item($I(this.node.id+"_groupFields_value").selectedIndex,$I(this.node.id+"_groupFields_value"),"up");
    },

    groupFieldsMoveDown_onClick: function(event) {
        move_select_item($I(this.node.id+"_groupFields_value").selectedIndex,$I(this.node.id+"_groupFields_value"),"down");
    },	
    
    groupFieldsMoveLeft_onClick: function(event) {    	
    	var value = $O(this.node.id+"_allGroupFields","").getValue();
    	if (this.printProfile["groups"]==null)
    		this.printProfile["groups"] = new Object;
    	if (this.printProfile["groups"][value]==null) {
    		this.printProfile["groups"][value] = new Object;
    		this.printProfile["groups"][value]["fontFace"] = "Arial";
    		this.printProfile["groups"][value]["fontSize"] = "15";
    		this.printProfile["groups"][value]["fontWeight"] = "bold";
    		this.printProfile["groups"][value]["fontColor"] = "#000000";
    		this.printProfile["groups"][value]["bgColor"] = "#CCCCCC";
    	}    	
        move_select_item_to_list($I(this.node.id+"_allGroupFields_value").selectedIndex,$I(this.node.id+"_allGroupFields_value"),$I(this.node.id+"_groupFields_value"));
        $O(this.node.id+"_fontFace").setValue("Arial");
        $O(this.node.id+"_fontSize").setValue("15");
        $O(this.node.id+"_fontWeight").setValue("bold");
        $O(this.node.id+"_fontColor").setValue("#000000");
        $O(this.node.id+"_bgColor").setValue("#CCCCCC");
    },

    groupFieldsMoveRight_onClick: function(event) {
    	var value = $O(this.node.id+"_groupFields","").getValue();
    	if (this.printProfile["groups"][value]!=null)
    		this.printProfile["groups"][value] = null;
        move_select_item_to_list($I(this.node.id+"_groupFields_value").selectedIndex,$I(this.node.id+"_groupFields_value"),$I(this.node.id+"_allGroupFields_value"));
        $O(this.node.id+"_fontFace").setValue("");
        $O(this.node.id+"_fontSize").setValue("");
        $O(this.node.id+"_fontWeight").setValue("");
        $O(this.node.id+"_fontColor").setValue("");
        $O(this.node.id+"_bgColor").setValue("");
    },
    
    totalFieldsMoveLeft_onClick: function(event) {    	
    	var value = $O(this.node.id+"_allTotalFields","").getValue();
    	if (this.printProfile["totals"]==null)
    		this.printProfile["totals"] = new Object;
    	if (this.printProfile["totals"]["totals"]==null)
    		this.printProfile["totals"]["totals"] = new Object;
    	if (this.printProfile["totals"]["totals"][value]==null) {
    		this.printProfile["totals"]["totals"][value] = value;
    	}    	
        move_select_item_to_list($I(this.node.id+"_allTotalFields_value").selectedIndex,$I(this.node.id+"_allTotalFields_value"),$I(this.node.id+"_totalFields_value"));
    },

    totalFieldsMoveRight_onClick: function(event) {
    	var value = $O(this.node.id+"_totalFields","").getValue();
    	if (this.printProfile["totals"]["totals"][value]!=null)
    		this.printProfile["totals"]["totals"][value] = null;
        move_select_item_to_list($I(this.node.id+"_totalFields_value").selectedIndex,$I(this.node.id+"_totalFields_value"),$I(this.node.id+"_allTotalFields_value"));
    },
    
    OK_onClick: function(event) {
		var printFieldList = $I(this.node.id+"_printFields_value");
		var sortFieldList = $I(this.node.id+"_sortFields_value");
		var groupFieldList = $I(this.node.id+"_groupFields_value");
		var totalFieldList = $I(this.node.id+"_totalFields_value");
		var printFields = new Object;
		var sortFields = new Object;
		var groupFields = new Object;
		var totalFields = new Object;
		var conditions = new Object;
		var i=0;
		for (i=0;i<printFieldList.options.length;i++) {
			printFields[printFieldList.options[i].value] = this.printProfile["printFields"][printFieldList.options[i].value]; 
		}
		for (i=0;i<sortFieldList.options.length;i++) {
			sortFields[sortFieldList.options[i].value] = this.printProfile["sortFields"][sortFieldList.options[i].value]; 
		}
		for (i=0;i<groupFieldList.options.length;i++) {
			groupFields[groupFieldList.options[i].value] = this.printProfile["groups"][groupFieldList.options[i].value]; 
		}
		for (i=0;i<totalFieldList.options.length;i++) {
			totalFields[totalFieldList.options[i].value] = this.printProfile["totals"]["totals"][totalFieldList.options[i].value]; 
		}
    	var data = this.getValues();
    	var tmp = new Array;
    	var ending = "";
    	var fieldName = "";
    	var condValue = "";
    	var o=null;
    	for (o in data) {
    		if (typeof data[o] != "function") {
    			tmp = o.split("_");
    			ending = tmp.pop();
    			if (ending=="check" && data[o]!=0) {
    				fieldName = tmp.pop();
    				conditions[fieldName] = new Object;
    				conditions[fieldName]["type"] = data[fieldName+"_select"];
					condValue =  data[fieldName+"_condition"];
    				if (conditions[fieldName]["type"]==" IN " || conditions[fieldName]["type"] == " NOT IN ") {
    					tmp = condValue.split("~");
    					conditions[fieldName]["value"] = new Object;
    					for (i=0;i<tmp.length;i++) {
    						conditions[fieldName]["value"][i] = tmp[i];    						
    					}
    				} else if (conditions[fieldName]["type"]==" BETWEEN ") {
    					conditions[fieldName]["value1"] = condValue;
    					conditions[fieldName]["value2"] = data[fieldName+"_condition_2"];    					
    				} else if (conditions[fieldName]["type"]==" LIKE " || conditions[fieldName]["type"]==" NOT LIKE ") {
						conditions[fieldName]["value"] = "%"+condValue+"%";    						    					
    				} else {
    					conditions[fieldName]["value"] = condValue;
    				}
    			}
    		}
    	}
    	this.printProfile["printFields"] = printFields;
    	this.printProfile["sortFields"]  = sortFields;
    	this.printProfile["groups"]  = groupFields;
    	this.printProfile["totals"]["totals"]  = totalFields;    	
    	this.printProfile["conditions"]  = conditions;
    	this.opener_object.printProfile = this.printProfile;
    	this.opener_object.refreshButton_onClick();    	
    	getWindowManager().remove_window(this.win.id);
    }            
});