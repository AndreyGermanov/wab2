var ListOptionsWindow = Class.create(Entity, {
	
	selectedFieldsMoveUp_onClick: function(event) {
        move_select_item($I(this.node.id+"_selectedFields").selectedIndex,$I(this.node.id+"_selectedFields"),"up");
    },

    selectedFieldsMoveDown_onClick: function(event) {
        move_select_item($I(this.node.id+"_selectedFields").selectedIndex,$I(this.node.id+"_selectedFields"),"down");
    },

    selectedFieldsMoveLeft_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_allFields").selectedIndex,$I(this.node.id+"_allFields"),$I(this.node.id+"_selectedFields"));
    },

    selectedFieldsMoveRight_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_selectedFields").selectedIndex,$I(this.node.id+"_selectedFields"),$I(this.node.id+"_allFields"));
    },
    
	printFieldsMoveUp_onClick: function(event) {
        move_select_item($I(this.node.id+"_printFields").selectedIndex,$I(this.node.id+"_printFields"),"up");
    },

    printFieldsMoveDown_onClick: function(event) {
        move_select_item($I(this.node.id+"_printFields").selectedIndex,$I(this.node.id+"_printFields"),"down");
    },

    printFieldsMoveLeft_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_allPrintFields").selectedIndex,$I(this.node.id+"_allPrintFields"),$I(this.node.id+"_printFields"));
    },

    printFieldsMoveRight_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_printFields").selectedIndex,$I(this.node.id+"_printFields"),$I(this.node.id+"_allPrintFields"));
    },
    
    OK_onClick: function(event) {
		var data = this.getValues();
		var parentList = this.opener_object;
		var printArray = new Array;
		var selectedArray = new Array;
		var selSelect = $I(this.node.id+"_selectedFields");
		var printSelect = $I(this.node.id+"_printFields");
		parentList.sortField = data["sortField"]+" "+data["sortDirection"];
		parentList.sortOrder = data["sortField"]+" "+data["sortDirection"];
		parentList.itemsPerPage = data["itemsPerPage"];
		var i=0;
		for (i=0;i<selSelect.options.length;i++) {
			selectedArray[selectedArray.length] = selSelect.options[i].value+" "+selSelect.options[i].text;
		}
		for (i=0;i<printSelect.options.length;i++) {
			printArray[printArray.length] = printSelect.options[i].value+" "+printSelect.options[i].text;
		}
		parentList.fieldList = selectedArray.join(",");
		parentList.printFieldList = printArray.join("~");
		var arr = new Array;
		var arr1 = new Array;
		var values_array = new Array;
		var ending = "";
		var obj = "";
		var condArray = new Array;
		var condType = "";
		var field = "";
		var i=0;
		var value = "";
		var o=null;
		for (o in data) {
			arr = o.split("_");
			ending = arr.pop();
			obj = this.node.id+"_"+arr.join("_");
			if (ending=="check") {
				if ($O(obj+"_check","").getValue()=="1") {
					field = "@"+$I(obj+"_check").getAttribute("fieldName").replace(/\./g,".@");
					if ($O(obj+"_select","").getValue()=="eq")
						condType = "=";
					if ($O(obj+"_select","").getValue()=="neq")
						condType = "!=";
					if ($O(obj+"_select","").getValue()=="le")
						condType = "<";
					if ($O(obj+"_select","").getValue()=="ge")
						condType = ">";
					if ($O(obj+"_select","").getValue()=="leeq")
						condType = "<=";
					if ($O(obj+"_select","").getValue()=="geeq")
						condType = ">=";
					if ($O(obj+"_select","").getValue()=="has")
						condType = " LIKE ";
					if ($O(obj+"_select","").getValue()=="notHas")
						condType = " NOT LIKE ";
					if ($O(obj+"_select","").getValue()=="inList")
						condType = " IN ";
					if ($O(obj+"_select","").getValue()=="notInList")
						condType = " NOT IN ";
					value = "'"+$O(obj+"_condition","").getValue()+"'";
					if (condType==" LIKE " || condType==" NOT LIKE ") {
						value = "'%"+$O(obj+"_condition","").getValue()+"%'";
					}
					if (condType==" IN " || condType==" NOT IN ") {
						arr1 = $O(obj+"_condition","").getValue().split("~");
						for (i=0;i<arr1.length;i++) {
							values_array[values_array.length] = "'"+arr1[i]+"'";
						}
						value = "("+values_array.join(",")+")";
					}
					condArray[condArray.length] = field+condType+value;
				}
			}
		}
		parentList.condition = condArray.join(" AND ");
		var tagConditions = this.tagsConditionsTable.getSingleValue().split("|");
		var condArray = new Array; 
		var tagParts = "";
		if (tagConditions.length>0) {
			for (o in tagConditions) {
				if (typeof tagConditions[o] != "function" && tagConditions[o]!="") {
					tagParts = tagConditions[o].split("~");
					var field = "@"+tagParts[0].replace(/\./g,".@");
					var cond = tagParts[1];
					var condType = "="; 
					if (cond=="eq")
						condType = "=";
					if (cond=="neq")
						condType = "!=";
					if (cond=="le")
						condType = "<";
					if (cond=="ge")
						condType = ">";
					if (cond=="leeq")
						condType = "<=";
					if (cond=="geeq")
						condType = ">=";
					if (cond=="has")
						condType = " LIKE ";
					if (cond=="notHas")
						condType = " NOT LIKE ";
					if (cond=="inList")
						condType = " IN ";
					if (cond=="notInList")
						condType = " NOT IN ";
					value = "'"+tagParts[2]+"'";
					if (condType==" LIKE " || condType==" NOT LIKE ") {
						value = "'%"+tagParts[2]+"%'";
					}
					if (condType==" IN " || condType==" NOT IN ") {
						arr1 = tagParts[2].split("~");
						for (i=0;i<arr1.length;i++) {
							values_array[values_array.length] = "'"+arr1[i]+"'";
						}
						value = "("+values_array.join(",")+")";
					}
					condArray[condArray.length] = field+condType+value;
				}
			}
		}
		parentList.tagsCondition = "";
		if (condArray.length>0)
			parentList.tagsCondition = condArray.join(" AND ");
		parentList.prevListProfile = null;
		parentList.showQRCode = data["showQRCode"];
		parentList.sort(true);
		getWindowManager().remove_window(this.win.id);
	},
	
	CONTROL_VALUE_CHANGED_processEvent: function($super,params) {
		if ($O(params["object_id"],"").parent_object_id == this.object_id) {
			var arr = params["object_id"].split("_");
			var ending = arr.pop();
			var obj_name = arr.join("_");
			if (ending=="select") {
				var val = $O(obj_name+"_select","").getValue();
				var item = $O(obj_name+"_condition");
				if (val=="inList" || val=="notInList") {
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
	
	profileEditBtn_onClick: function(event) {
		var wm = getWindowManager();
		var role = $O(this.object_id+"_currentRole","").getValue();
		var elem_id = this.opener_object.profileClass+"_"+this.module_id+"_"+role+"_"+this.opener_object.object_id.replace("DocFlowApplicationDocsList","").split("_").pop();
		var window_elem_id = "Window_"+elem_id.replace(/_/g,"");
		var params = new Array;
		wm.show_window(window_elem_id,elem_id,params,this.object_id,this.node.id,null,false);		
	}	
});