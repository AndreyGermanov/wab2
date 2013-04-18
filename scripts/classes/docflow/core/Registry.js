var Registry = Class.create(Entity, {
	
	setPrintProfile: function(reportName,profileName) {
		var args = new Object;
		var obj = this;
		args["profile"] = profileName;
		args["report"] = reportName;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: this.object_id,hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
            	var response = transport.responseText;
            	if (response!="") {
            		obj.printProfile = response.evalJSON(true);
            		obj.currentPrintProfile = profileName;
            		obj.refreshButton_onClick();
            	}
            }
        });		
	},
	
	refreshButton_onClick: function(event) {
		var args = new Object;
		var obj = this;
		var data = this.getValues();
		args["currentReport"] = this.currentReport;
		args["printProfile"] = this.printProfile;
		args["periodStart"] = data["periodStart"];
		args["periodEnd"] = data["periodEnd"];
	    new Ajax.Request("index.php", {
	        method:"post",
	        parameters: {ajax: true, object_id: this.object_id,hook: '6', arguments: Object.toJSON(args)},
	        onSuccess: function(transport)
	        {
	        	$I(obj.object_id+"_innerFrame").src = "?object_id="+obj.object_id+"2&hook=show&arguments="+obj.argums;
	        }
	    });		
	},

	setPrintProfilesList: function(reportName) {
		var args = new Object;
		var obj = this;
		args["report"] = reportName;
	    new Ajax.Request("index.php", {
	        method:"post",
	        parameters: {ajax: true, object_id: this.object_id,hook: '4', arguments: Object.toJSON(args)},
	        onSuccess: function(transport)
	        {
	        	var response = transport.responseText;
	        	var el = $O(obj.object_id+"_currentPrintProfile","");
	        	el.type = "list,"+response+"|"+response;
	        	el.node.setAttribute("type",el.type);
	        	el.node.setAttribute("value",'Основной');
	        	el.value = 'Основной';
	        	el.build();
	        	obj.setPrintProfile(reportName,'Основной');	        	
	        }
	    });		
	},
	
	optionsButton_onClick: function(event) {
		var args = new Object;
		var obj = this;
		args["report"] = this.currentReport;
	    new Ajax.Request("index.php", {
	        method:"post",
	        parameters: {ajax: true, object_id: this.object_id,hook: '8', arguments: Object.toJSON(args)},
	        onSuccess: function(transport)
	        {
	        	var settingsClass = transport.responseText;
	        	var args = new Object;
	        	args["printProfile"] = obj.printProfile;
	        	args["hook"] = "setParams";
	        	var regClass = this.object_id.split("_").shift();
	        	var objName = settingsClass+"_"+obj.module_id+"_"+regClass;	        	
	        	getWindowManager().show_window("Window_"+objName.replace(/_/g,""),objName,args,obj.object_id,obj.object_id,null,true);
	        }
	    });		
	},
	
    printButton_onClick: function(event) {
		$I(this.node.id+"_innerFrame").contentWindow.print();
	},

	saveButton_onClick: function(event) {
		if (this.role["canSaveListSettings"]=="false") {
			this.reportMessage("Не достаточно прав доступа !","error",true);
			return 0;
		}
		var obj = this;     		
		var params = new Object;
		params["fieldList"] = this.fieldList;
		params["printProfile"] = this.printProfile;
		params["report"] = this.currentReport;
		params["profile"] = this.currentPrintProfile;
		new Ajax.Request("index.php", {
			method:"post",
			parameters: {ajax: true, object_id: obj.object_id,hook: '9', arguments: Object.toJSON(params)},
			onSuccess: function(transport)
			{                            
			}
		});
	},
	
	newProfileButton_onClick: function(event) {
		var value = prompt("Введите название набора настроек");
		if (value!=0 && value!="") {
			var list = $I(this.node.id+"_currentPrintProfile_value");
			var o=null;
			for (o in list.options) {
				if (list.options[o].value==value) {
					alert("Набор настроек с таким наименованием уже существует");
					return 0;
				}					
			}
			var opt = new Option(value,value,true,true);
			list.add(opt);
			$O(this.node.id+"_currentPrintProfile").setValue(value,true);
			this.currentPrintProfile = value;			
		}
	},	
	
	deleteProfileButton_onClick: function(event) {
		if (!confirm("Вы действительно хотите удалить этот набор настроек ?"))
			return 0;
		var list = $I(this.node.id+"_currentPrintProfile_value");
		var selectedItem = list.options.item(list.selectedIndex);
		if (selectedItem.value=="Основной") {
			this.reportMessage("Удалить основной набор настроек невозможно !","error",true);
			return 0;
		}
		var params = new Object;
		params["profile"] = selectedItem.value;
		params["report"] = this.currentReport;
		var obj = this;
		new Ajax.Request("index.php", {
			method:"post",
			parameters: {ajax: true, object_id: obj.object_id,hook: '10', arguments: Object.toJSON(params)},
			onSuccess: function(transport)
			{                            
				list.options.remove(list.selectedIndex);
				list.selectedIndex = 0;
				$O(obj.node.id+"_currentPrintProfile").setValue("Основной");
			}
		});		
	},	
		
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.object_id+"_currentReport") {
			var report = $O(this.object_id+"_currentReport","").getValue();
			this.currentReport = report;
			this.setPrintProfilesList(report);
		}
		if (params["object_id"]==this.object_id+"_currentPrintProfile") {
			var profile = $O(this.object_id+"_currentPrintProfile","").getValue();
			this.setPrintProfile(this.currentReport,profile);
		}		
	},
	
    button_onMouseDown: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_clicked."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseUp: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseOver: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_hover."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseOut: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        eventTarget(event).src = src;
    },	
    
    afterInsert: function() {
    	var obj = $I(this.node.id+"_bottomRow");
    	if (obj!=0)
    		obj.style.display = 'none';
    	obj = $I(this.node.id+"_mainRow");
    	obj.style.height = '100%';
    	obj = $I(this.node.id+"_mainColumn");
    	obj.style.height = '100%';
    	obj = $I(this.node.id+"_innerFrame");
    	obj.style.height = '100%';
    }
});