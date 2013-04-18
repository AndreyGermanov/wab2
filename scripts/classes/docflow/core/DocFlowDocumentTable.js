var DocFlowDocumentTable = Class.create(EntityDataTable, {

	getClassName:function() {
		return "DocFlowDocumentTable";
	},
		
	setDeletionMark:function(mark) {
        var delete_checks = this.getColValues(this.rows,1,true);
        var ids = this.getColValues(this.rows,0,true);
        var deleted_entities = new Array;
        var vl=null;
        var ent=null;
        for (vl in delete_checks) {
            if (delete_checks[vl]==1) {
                deleted_entities[deleted_entities.length] = ids[vl];
            }
        }
        if (deleted_entities.length==0) {
            if (this.currentControl!=null & this.currentControl!=0)
                ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
            if (ent!=null && ent!=0)
                deleted_entities[0] = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0).getValue();
        }
        var proc = '3';
        if (deleted_entities.length>0)  {
        	var args = new Object;
        	args["entities"] = deleted_entities.join(",");
    		args["mark"] = mark;
        	if (mark!=0 && mark!=1)
        		proc='5';
            var obj = this; 
            new Ajax.Request("index.php", {
                method:"post",
                parameters: {ajax: true, object_id: obj.module_id, hook: proc, arguments: Object.toJSON(args)},
                onSuccess: function(transport)
                {                            
                    var response = trim(transport.responseText);
                    if (response!="") {
                        var rsp = response.evalJSON();
                        if (rsp) {
                            var removed_objects = rsp["removed_objects"];
                            if (removed_objects!="") {
                                obj.raiseEvent("ENTITY_DELETED",$Arr("object_id="+removed_objects+",action=delete,mark="+mark),true);
                                if (proc=='5') {
	                                var rem_objects = removed_objects.split(",");
	                                for (var i=0;i<rem_objects.length;i++)
	                                	obj.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+rem_objects[i],true));
                                }
                            }
                         }
                    }
                }
            });
        }
	},
	
	setRegisterMark:function(mark) {
        var delete_checks = this.getColValues(this.rows,1,true);
        var ids = this.getColValues(this.rows,0,true);
        var deleted_entities = new Array;
        var vl=null;
        var ent=null;
        for (vl in delete_checks) {
            if (delete_checks[vl]==1) {
                deleted_entities[deleted_entities.length] = ids[vl];
            }
        }
        if (deleted_entities.length==0) {
            if (this.currentControl!=null & this.currentControl!=0)
                ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
            if (ent!=null && ent!=0)
                deleted_entities[0] = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0).getValue();
        }
        if (deleted_entities.length>0)  {
        	var args = new Object;
        	args['entities'] = deleted_entities.join(",");
        	args['mark'] = mark;
            var obj = this;      
            new Ajax.Request("index.php", {
                method:"post",
                parameters: {ajax: true, object_id: obj.module_id,hook: '4', arguments: Object.toJSON(args)},
                onSuccess: function(transport)
                {                            
                    var response = trim(transport.responseText);
                    if (response!="") {
                        var rsp = response.evalJSON();
                        if (rsp) {
                            var removed_objects = rsp["removed_objects"];
                            if (removed_objects!="") {
                                obj.raiseEvent("ENTITY_REGISTERED",$Arr("object_id="+removed_objects+",action=delete,mark="+mark),true);
                            }
                         } else
                             alert(response);
                    }
                }
            });
        }
	},
	
	deleteButton_onClick: function(event) {
		event.cancelBubble = true;
        if (this.objRole["canDelete"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }		
		this.setDeletionMark(1);
	},
	
	createbyButton_onClick: function(event) {
		event.cancelBubble = true;
		var ent=null;
        if (this.currentControl!=null & this.currentControl!=0)
            ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
        if (ent!=null && ent!=0) {
        	ent = ent.getValue();
        	var params = new Object;
        	params["opener_object"] = ent;
        	$O(this.object_id,this.instance_id).show_context_menu("CreateObjectContextMenu_"+this.module_id+"_print",cursorPos(event).x,cursorPos(event).y,eventTarget(event).id,params);
        };
	},
	
	barCodeButton_onClick: function(event) {
		event.cancelBubble = true;
		var ent=null;
        if (this.currentControl!=null & this.currentControl!=0)
            ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
        if (ent!=null && ent!=0) {
        	ent = ent.getValue();
        	window.open("utils/qr/qr.php?text=https://"+this.serverName+"/bc.php?i="+ent.split("_").pop());
        }
	},	

	registryButton_onClick: function(event) {
		event.cancelBubble = true;
		var ent=null;
        if (this.objRole["canViewMovements"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }		
        if (this.currentControl!=null & this.currentControl!=0)
            ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
        if (ent!=null && ent!=0) {
        	ent = ent.getValue();
        	var args = new Object;
        	args["document"] = ent.replace(this.module_id+"_","");
        	args["hook"] = "setParams";
            var pos = getElementPosition(eventTarget(event).id);
    		args["left"] = pos.left-5;
            args["top"] = pos.top + pos.height-5;		 
    		getWindowManager().show_window("Window_RegistryMovementsWindow"+this.module_id.replace(/_/g,"")+ent.split("_").pop().replace(this.module_id.replace(/_/g,""),""),"RegistryMovementsWindow_"+this.module_id+"_"+ent.split("_").pop().replace(this.module_id.replace(/_/g,""),""),args,this.object_id,this.node.id,null,true);        	
        }
	},	
	
	unlinkButton_onClick: function(event) {
		event.cancelBubble = true;
        if (this.objRole["canUnlink"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }		
        if (confirm("Вы действительно хотите удалить выбранные ссылки ?"))
        	this.setDeletionMark(this.topLinkObject);
	},

	linkButton_onClick: function(event) {
		event.cancelBubble = true;
        if (this.objRole["canLink"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }
        var params = new Object;
        params["hook"] = "setParams";
        params["className"] = this.className;
        params["defaultClassName"] = this.defaultClassName;
        params["tableClassName"] = this.getClassName();
        params["hierarchy"] = this.hierarchy;
        params["treeClassName"] = "EntityTree";
        if (this.additionalFields!=null)
        	params["additionalFields"] = this.additionalFields;
        	
        params["sortOrder"] = this.sortOrder;
        params["adapterId"] = this.adapterId;
        if (this.fieldList!="")
        	params["fieldList"] = this.fieldList.replace(/,/g,'~');
        if (this.parentEntity!="")
        	params['entityParentId'] = this.parentEntity;
        params['present'] = this.classTitle.replace(/#/g,'xyxyx'+":Выбор");
        params["classTitle"] = this.classTitle;
        params["editorType"] = "WABWindow";
        params["selectGroup"] = this.selectGroup;
        params["entityId"] = "";
        params["parent_object_id"] = this.object_id;
		params["condition"] = "@parent IS NOT EXISTS";
		var elem_id = "EntitySelectWindow_"+this.module_id+"_"+this.name+"newLink";
		var window_elem_id = "Window_"+elem_id.replace(/\_/g,"");
        getWindowManager().show_window(window_elem_id,elem_id,params,obj.module_id,this.node.id,null,true);                                                    
	},
	
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.object_id) {
        	var args = new Object;
        	var links = new Object;
        	links[params["value"]] = params["value"];
        	args['links'] = links;        	
            var obj = this;      
            new Ajax.Request("index.php", {
                method:"post",
                parameters: {ajax: true, object_id: obj.topLinkObject,hook: 'setLinks', arguments: Object.toJSON(args)},
                onSuccess: function(transport)
                {                            
                	obj.raiseEvent("ENTITY_CHANGED",$Arr("object_id="+params["value"]+",action=addlink"),true);
                }
            });			
		}
		if (params["object_id"]==this.object_id+"_profilesList") {
			this.prevListProfile = this.defaultListProfile;
			this.defaultListProfile = params["value"];
			this.sort(true);
		}
	},		
	
	undeleteButton_onClick: function(event) {
		event.cancelBubble = true;
        if (this.objRole["canUndelete"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }
		this.setDeletionMark(0);
	},

	registerButton_onClick: function(event) {
		event.cancelBubble = true;
		this.setRegisterMark(1);
	},

	unregisterButton_onClick: function(event) {
		event.cancelBubble = true;
		this.setRegisterMark(0);
	},
	
	deleteFilterButton_onClick: function(event) {
		event.cancelBubble = true;
		if (this.objRole["canUnfilter"]=="false") {
			this.reportMessage("Не достаточно прав доступа !","error",true);
			return 0;
		}
		if (this.parentEntity!=null && this.parentEntity!="" && this.parentEntity!="{parentEntity}" && this.parentEntity.split("_").pop()!="")			
			this.condition = "@parent.@name="+this.parentEntity.split("_").pop();
		else if (this.showHierarchy)
			this.condition = "@parent IS NOT EXISTS";
		else
			this.condition = "";
		this.tagsCondition = "";
		this.sort();
	},
	
	filterButton_onClick: function(event) {
		event.cancelBubble = true;
		if (this.objRole["canFilter"]=="false") {
			this.reportMessage("Не достаточно прав доступа !","error",true);
			return 0;
		}		
		if (this.currentControl!=null) {
			var value = this.currentControl.getValue();
			var col = this.currentControl.node.parentNode.getAttribute("column");
			this.condition = "@"+col.replace(/\./g,".@")+"='"+value+"'";
			this.sort();
		}
	},

	optionsButton_onClick: function(event) {
		event.cancelBubble = true;
		if (this.objRole["canSetProperties"]=="false") {
			this.reportMessage("Не достаточно прав доступа !","error",true);
			return 0;
		}		
		this.sortOrder = this.sortOrder.replace(/null/g,'');
		var args = new Object;
		args["fieldList"] = this.fieldList;
		args["allFieldList"] = this.allFieldList;
		args["printFieldList"] = this.printFieldList;
		args["showQRCode"] = this.showQRCode;
		args["conditionFields"] = this.conditionFields;
		args["condition"] = this.condition.replace(/'/g,'"').replace(/"/g,"xoxoxo").replace('#xo','xoxoxo');
		args["tagsCondition"] = this.tagsCondition.replace(/'/g,'"').replace(/"/g,"xoxoxo").replace('#xo','xoxoxo');
		args["itemsPerPage"] = this.itemsPerPage;
		args["item"] = this.className+"_"+this.module_id+"_List";	
		args["item2"] = args["item"];
		args["className"] = this.className;
		args["sortField"] = this.sortOrder;
		args["hook"] = "setParams";
		args["object_text"] = 'Свойства списка "'+this.classListTitle+'"';
        var pos = getElementPosition(eventTarget(event).id);
        args["left"] = pos.left-5;
        args["top"] = pos.top + pos.height-5;		 
		getWindowManager().show_window("Window_ListOptionsWindow"+this.module_id.replace(/_/g,"")+this.node.id.split("_").pop().replace(this.module_id.replace(/_/g,""),""),"ListOptionsWindow_"+this.module_id+"_"+this.node.id.split("_").pop().replace(this.module_id.replace(/_/g,""),""),args,this.object_id,this.node.id,null,true);
	},
	
	datesButton_onClick: function(event) {        
		event.cancelBubble = true;
		var params = new Object;
		params["periodStart"] = this.periodStart;
		params["periodEnd"] = this.periodEnd;
		params["className"] = this.className;
		params["hook"] = "setParams";
        var pos = getElementPosition(eventTarget(event).id);
		params["left"] = pos.left-5;
        params["top"] = pos.top + pos.height-5;		 
		getWindowManager().show_window("Window_SetPeriodWindow"+this.module_id.replace(/_/g,"")+this.node.id.split("_").pop().replace(this.module_id.replace(/_/g,""),""),"SetPeriodWindow_"+this.module_id+"_"+this.node.id.split("_").pop().replace(this.module_id.replace(/_/g,""),""),params,this.object_id,this.node.id,null,true);
	},
	
	printButton_onClick: function(event) {
		event.cancelBubble = true;
		if (this.objRole["canPrintList"]=="false") {
			this.reportMessage("Не достаточно прав доступа !","error",true);
			return 0;
		}
		this.sortOrder = this.sortOrder.replace(/null/g,'');
		var params = new Object;
		params["fieldList"] = this.fieldList;
		params["showQRCode"] = this.showQRCode;
		params["allFieldList"] = this.allFieldList;
		params["printFieldList"] = this.printFieldList;
		params["conditionFields"] = this.conditionFields;
		params["condition"] = this.condition.replace(/'/g,'"').replace(/"/g,"xoxoxo");
		if (this.additionalCondition!=null)
			params["additionalCondition"] = this.additionalCondition.replace(/'/g,'"').replace(/"/g,"xoxoxo");
		params["item"] = this.parent_object_id;
		params["sortField"] = this.sortOrder;
		params["className"] = this.className;
		params["sortOrder"] = this.sortOrder;		
        var pos = getElementPosition(eventTarget(event).id);
		params["left"] = pos.left-5;
        params["top"] = pos.top + pos.height-5;		 
		var obj = this;            
		new Ajax.Request("index.php", {
			method:"post",
			parameters: {ajax: true, object_id: obj.object_id,hook: '6', arguments: Object.toJSON(params)},
			onSuccess: function(transport) {                            
				var opObject = "PrintWindow_"+obj.module_id+"_"+obj.object_id+"_list";
				params = new Object;
				params["hook"] = "setParams";
				params["left"] = pos.left-5;
		        params["top"] = pos.top + pos.height-5;		 
				getWindowManager().show_window("Window_"+opObject.replace(/_/g,""),opObject,params,obj.object_id,obj.node.id,null,true);
			}
		});
	},
	
	findButton_onClick: function(event) {
		event.cancelBubble = true;
		var params = new Object;
		var obj = this;     
		if (obj.foundedRecords==null)
			obj.foundedRecords = new Array;
		if (obj.currentFindString==null)
			obj.currentFindString = $I(this.node.id+"_findField").value;
		if (obj.currentFindString != $I(this.node.id+"_findField").value) {
			obj.currentFindString = $I(this.node.id+"_findField").value;
			obj.foundedRecords = new Array;
		}
		this.sortOrder = this.sortOrder.replace(/null/g,'');
		if (this.periodStart!="" && this.periodStart!=null) {
			this.advancedCondition = "@docDate>='"+this.periodStart+"'";
		}
		if (this.periodEnd!="" && this.periodEnd!=null) {
			if (this.advancedCondition!=null && this.advancedCondition!="") {
				this.advancedCondition += " AND @docDate<='"+this.periodEnd+"'";
			} else
				this.advancedCondition += "@docDate<='"+this.periodEnd+"'";
		}
		if (this.condition!="" && this.advancedCondition!="")
			this.advancedCondition = " AND "+this.advancedCondition;
		var condition = this.condition+this.advancedCondition;
		params["fieldList"] = this.fieldList;
		params["allFieldList"] = this.allFieldList;
		params["printFieldList"] = this.printFieldList;
		params["periodStart"] = this.periodStart;
		params["conditionFields"] = this.conditionFields;
		params["condition"] = condition.replace(/'/g,'"').replace(/"/g,"xoxoxo");
		params["className"] = this.className;
		params["item"] = this.parent_object_id;
		params["sortField"] = this.sortOrder;
		params["sortOrder"] = this.sortOrder;
		params["searchString"] = this.currentFindString;
		params["exclude_entities"] = this.foundedRecords.join(",");
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        
        this.node.appendChild(loading_img);		
		new Ajax.Request("index.php", {
			method:"post",
			parameters: {ajax: true, object_id: obj.object_id,hook: '7', arguments: Object.toJSON(params)},
			onSuccess: function(transport)
			{                            
				var response = transport.responseText;
				if (response!="") {
					var arr = response.split("|");
					if (arr[0]!="") {
						obj.foundedRecords[obj.foundedRecords.length] = arr[0].split("_").pop();
						var entityNumber = arr[1];
						var page = "";
						obj.entityId = arr[0].replace(/\\n/g,"");
						if (parseInt(entityNumber/obj.itemsPerPage)!=entityNumber/obj.itemsPerPage)
							page = parseInt(entityNumber/obj.itemsPerPage)+1;
						if (obj.numPages>1 && $O(obj.pagePanelId,'')!=null && page!=obj.currentPage) {
							obj.currentPage = page;
							event = new Array;
							event.target = $I($O(obj.pagePanelId,'').node.id+"_p"+page);
							$O(obj.pagePanelId,'').changePage(event,obj.currentPage);
							obj.selectCurrentEntity(true);														
						} else {
							obj.selectCurrentEntity(true);
						};
					} else {
						obj.foundedRecords = new Array;
						obj.findButton_onClick(event);
						obj.selectCurrentEntity(true);
					};
				} else if (obj.foundedRecords.length>0) {
					obj.foundedRecords = new Array;
					obj.findButton_onClick(event);
					obj.selectCurrentEntity(true);
				}
                obj.node.removeChild(loading_img);
			}
		});
	},
	
	globalSearch_onClick: function(event) {
		event.cancelBubble = true;
        var elem_id = "GlobalSearchTable_"+this.module_id+"_tbl";
        window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
        var params = new Object;
        params["hook"] = "setParams";
        params["object_text"] = "Глобальный поиск";
        params["searchText"] = $I(this.node.id+"_findField").value;
        var pos = getElementPosition(eventTarget(event).id);
		params["left"] = pos.left-5;
        params["top"] = pos.top + pos.height-5;		 
        getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);		
	},
	
	saveSettingsButton_onClick: function(event) {
		event.cancelBubble = true;
		if (this.objRole["canSaveListSettings"]=="false") {
			this.reportMessage("Не достаточно прав доступа !","error",true);
			return 0;
		}
		var obj = this;     		
		this.sortOrder = this.sortOrder.replace(/null/g,'');
		var params = new Object;
		params["fieldList"] = this.fieldList;
		params["printFieldList"] = this.printFieldList;
		params["conditionFields"] = this.conditionFields;
		params["condition"] = this.condition.replace(/'/g,'"').replace(/"/g,"xoxoxo");
		params["tagsCondition"] = this.tagsCondition.replace(/'/g,'"').replace(/"/g,"xoxoxo");
		params["sortField"] = this.sortOrder;
		params["className"] = this.className;
		params["periodStart"] = this.periodStart;
		params["periodEnd"] = this.periodEnd;
		params["itemsPerPage"] = this.itemsPerPage;
		params["showQRCode"] = this.showQRCode;
		params["defaultListProfile"] = this.defaultListProfile;
		new Ajax.Request("index.php", {
			method:"post",
			parameters: {ajax: true, object_id: obj.object_id,hook: '8', arguments: Object.toJSON(params)},
			onSuccess: function(transport) {
				return 0;
			}
		});
	},
	
	hierarchyButtonOn_onClick: function(event) {
		event.cancelBubble = true;
		this.showHierarchy = 0;
		$I(this.node.id+"_hierarchyButtonOn").style.display = 'none';
		$I(this.node.id+"_hierarchyButtonOff").style.display = '';
		$O(this.node.id+"_entityTree","").setValue('');
		this.condition = this.condition.replace(/AND @isGroup IS NOT EXISTS/g,'');
		this.condition = this.condition.replace(/@isGroup IS NOT EXISTS/g,'');
		this.condition = this.condition.replace(/AND @parent IS NOT EXISTS/g,'');
		this.condition = this.condition.replace(/@parent IS NOT EXISTS/g,'');
		this.condition = this.condition.replace(/AND \@parent\.\@name\=\S+/g,'');
		this.condition = this.condition.replace(/AND \@parent\.\@name\=/g,'');
		this.condition = this.condition.replace(/\@parent\.\@name\=\S+/g,'');
		this.condition = this.condition.replace(/\@parent\.\@name\=/g,'');
		if (this.condition=='')
			this.condition = '@isGroup IS NOT EXISTS';
		else
			this.condition = '@isGroup IS NOT EXISTS AND '+this.condition;
		this.condition = this.condition.replace(/AND  AND/g,"AND");
		this.condition = this.condition.replace(/AND AND/g,"AND");
		this.rebuild();
	},

	hierarchyButtonOff_onClick: function(event) {
		event.cancelBubble = true;
		this.showHierarchy = 0;
		$I(this.node.id+"_hierarchyButtonOn").style.display = '';
		$I(this.node.id+"_hierarchyButtonOff").style.display = 'none';		
		if (this.currentControl != null && this.currentControl.node!=null) {
			var row = this.currentControl.node.parentNode.getAttribute("row");
			this.entityId = this.getItem(row,0).getValue();
			var obj = this;
			new Ajax.Request("index.php", {
				method:"post",
				parameters: {ajax: true, object_id: this.entityId,hook: 'getParent'},
				onSuccess: function(transport) {
					var response = transport.responseText;
					$O(obj.node.id+"_entityTree","").setValue(response);
					obj.condition = obj.condition.replace(/\@isGroup IS NOT EXISTS/g,'');
					obj.condition = obj.condition.replace(/AND @parent IS NOT EXISTS/g,'');
					obj.condition = obj.condition.replace(/@parent IS NOT EXISTS/g,'');
					obj.condition = obj.condition.replace(/AND \@parent\.\@name\=\S+/g,'');
					obj.condition = obj.condition.replace(/\@parent\.\@name\=\S+/g,'');
					var parent_condition = "";
					if (response!="") {
						var entity_name = response.split("_").pop();
						if (entity_name!="") {
							obj.parentEntity = response;
							parent_condition = "@parent.@name="+entity_name;
						} else
							parent_condition = "@parent IS NOT EXISTS";
					} else
						parent_condition = "@parent IS NOT EXISTS";
					if (obj.condition=="")
						obj.condition = parent_condition;
					else
						obj.condition = parent_condition+" AND "+obj.condition;
					obj.condition = obj.condition.replace(/AND  AND/g,"AND");
					obj.condition = obj.condition.replace(/AND AND/g,"AND");
					obj.rebuild();
				} 
			});
		} else {
			obj = this;
			obj.condition = obj.condition.replace(/\@isGroup IS NOT EXISTS/g,'');
			obj.condition = obj.condition.replace(/AND @parent IS NOT EXISTS/g,'');
			obj.condition = obj.condition.replace(/@parent IS NOT EXISTS/g,'');
			obj.condition = obj.condition.replace(/AND \@parent\.\@name\=\S+/g,'');
			obj.condition = obj.condition.replace(/\@parent\.\@name\=\S+/g,'');
			var parent_condition = "";
			if (obj.parentEntity!="") {
				var entity_name = obj.parentEntity.split("_").pop();
				if (entity_name!="")
					parent_condition = "@parent.@name="+entity_name;
				else
					parent_condition = "@parent IS NOT EXISTS";
			}
			else
				parent_condition = "@parent IS NOT EXISTS";
			if (obj.condition=="")
				obj.condition = parent_condition;
			else
				obj.condition = parent_condition+" AND "+obj.condition;
			obj.condition = obj.condition.replace(/AND  AND/g,"AND");
			obj.condition = obj.condition.replace(/AND AND/g,"AND");
			obj.rebuild();			
		}
	},
	
	rebuild: function($super,rebuild) {
		this.advancedCondition = "";
		if (this.periodStart!="" && this.periodStart!=null) {
			var pStart = new Date();
			pStart.setTime(this.periodStart);
			pStart.setHours(0);
			pStart.setMinutes(0);
			pStart.setSeconds(0);
			this.periodStart = pStart.getTime();
			this.advancedCondition = "@docDate>='"+this.periodStart+"'";
		}
		if (this.periodEnd!="" && this.periodEnd!=null) {
			var pEnd = new Date();
			pEnd.setTime(this.periodEnd);
			pEnd.setHours(23);
			pEnd.setMinutes(59);
			pEnd.setSeconds(59);
			this.periodEnd = pEnd.getTime();
			if (this.advancedCondition!=null && this.advancedCondition!="") {
				this.advancedCondition += " AND @docDate<='"+this.periodEnd+"'";
			} else
				this.advancedCondition += "@docDate<='"+this.periodEnd+"'";
		}
		if (this.condition!="" && this.advancedCondition!="")
			this.advancedCondition = " AND "+this.advancedCondition;
		$super(rebuild);
	},
	
	newProfileButton_onClick: function(event) {
		event.cancelBubble = true;
		var value = prompt("Введите название набора настроек");
		if (value!=0 && value!="") {
			var list = $I(this.node.id+"_profilesList_value");
			for (o in list.options) {
				if (list.options[o].value==value) {
					alert("Набор настроек с таким наименованием уже существует");
					return 0;
				}					
			}
			var opt = new Option(value,value,true,true);
			list.add(opt);
			$O(this.node.id+"_profilesList").setValue(value);
			this.prevListProfile = this.defaultListProfile;
			this.defaultListProfile = value;
		}
	},
	
	deleteProfileButton_onClick: function(event) {
		event.cancelBubble = true;
		if (!confirm("Вы действительно хотите удалить этот набор настроек ?"))
			return 0;
		var list = $I(this.node.id+"_profilesList_value");
		var selectedItem = list.options.item(list.selectedIndex);
		if (selectedItem.value=="Основной") {
			this.reportMessage("Удалить основной набор настроек невозможно !","error",true);
			return 0;
		}
		var params = new Object;
		params["profileToRemove"] = selectedItem.value;
		params["defaultListProfile"] = "Основной";
		var obj = this;
		new Ajax.Request("index.php", {
			method:"post",
			parameters: {ajax: true, object_id: obj.object_id,hook: '8', arguments: Object.toJSON(params)},
			onSuccess: function(transport) {                            
				list.options.remove(list.selectedIndex);
				list.selectedIndex = 0;
				$O(obj.node.id+"_profilesList").setValue("Основной");
				obj.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+obj.node.id+"_profilesList,value=Основной"));
			}
		});		
	},
	
	helpButton_onClick: function(event) {
		event.cancelBubble = true;
		var params = new Object;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide1","HTMLBook_"+this.module_id+"_"+this.helpGuideId,params,null,null);		
	},	
	
	onLoad: function() {
		if (this.objRole["canPrintList"]=="false")
			$I(this.node.id+"_printButton").style.display = 'none';
		if (this.objRole["canSetProperties"]=="false")
			$I(this.node.id+"_optionsButton").style.display = 'none';
		if (this.objRole["canFilter"]=="false")
			$I(this.node.id+"_filterButton").style.display = 'none';
		if (this.objRole["canUnfilter"]=="false")
			$I(this.node.id+"_deleteFilterButton").style.display = 'none';
		if (this.objRole["canSaveListSettings"]=="false") {
			$I(this.node.id+"_saveSettingsButton").style.display = 'none';
			$I(this.node.id+"_newProfileButton").style.display = 'none';			
			$I(this.node.id+"_deleteProfileButton").style.display = 'none';	
			if ($I(this.node.id+"_profilesList_value")!=0 && $I(this.node.id+"_profilesList_value").options.length==1) {
				$I(this.node.id+"_profilesList_value").style.display = 'none';
			}
		}
		if (this.objRole["canAdd"]=="false")
			$I(this.node.id+"_addButton").style.display = 'none';
		if (this.objRole["canGlobalSearch"]=="false")
			$I(this.node.id+"_globalSearch").style.display = 'none';
		if (this.objRole["canCreateBy"]=="false")
			$I(this.node.id+"_createbyButton").style.display = 'none';
		if (this.objRole["canAddCopy"]=="false")
			$I(this.node.id+"_copyButton").style.display = 'none';
		if (this.objRole["canEdit"]=="false")
			$I(this.node.id+"_insertButton").style.display = 'none';		
		if (this.objRole["canRegister"]=="false")
			$I(this.node.id+"_registerButton").style.display = 'none';		
		if (this.objRole["canUnregister"]=="false")
			$I(this.node.id+"_unregisterButton").style.display = 'none';		
		if (this.objRole["canViewMovements"]=="false")
			$I(this.node.id+"_registryButton").style.display = 'none';		
		if (this.objRole["canDelete"]=="false")
			$I(this.node.id+"_deleteButton").style.display = 'none';		
		if (this.objRole["canUndelete"]=="false")
			$I(this.node.id+"_undeleteButton").style.display = 'none';	
		if (this.helpButtonDisplay=="none")
			if ($I(this.node.id+"_helpButton")!=null && $I(this.node.id+"_helpButton").style!=null)
				$I(this.node.id+"_helpButton").style.display = 'none';	
		if (!this.hierarchy || this.objRole["canAddGroup"]=="false") {
			$I(this.node.id+"_addGroupButton").style.display = 'none';
			$I(this.node.id+"_groupList").style.display = 'none';
		}
		else {
			if (this.showHierarchy) {
				$I(this.node.id+"_hierarchyButtonOn").style.display = '';
				$I(this.node.id+"_hierarchyButtonOff").style.display = 'none';
			} else {
				$I(this.node.id+"_hierarchyButtonOn").style.display = 'none';
				$I(this.node.id+"_hierarchyButtonOff").style.display = '';			
			}			
		}
		if (this.topLinkObject=="" || this.topLinkObject==null || this.topLinkRole["canEditLinks"]=="false") {
			$I(this.node.id+"_linkButton").style.display = 'none';	
			$I(this.node.id+"_unlinkButton").style.display = 'none';
		}
	}
});