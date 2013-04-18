var ApacheUser = Class.create(Mailbox, {

        OK_onClick: function(event) {

        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true")
        {
            getWindowManager().remove_window(this.win.id);
            return 0;
        }

        // Проверяем корректность ввода данных
        var data = this.getValues();
        var params = new Array;
        if (data["name"]=="") {
        	params["object_id"] = "InfoPanelMessages";
        	params["text"] = "Укажите имя пользователя!";
        	params["activate"] = "1";
        	params["type"] = "error";
        	this.raiseEvent("SEND_MESSAGE",params);
            return 0;
        }
        
        if (data["password"]=="" && data["authType"]!="ldap") {
            this.reportMessage('Укажите пароль !',"error",true);
            return 0;
        }

        if (data["password"]!=data["password1"]) {
            this.reportMessage("Введенные пароли не совпадают!","error",true)
            return 0;
        }

        // Формируем параметры, передаваемые серверу для записи
        var args = new Object;
        args["appconfig"] = this.appconfig.getValues().toObject();
        args["appconfig"]["name"] = args["appconfig"]["md_name"];
        args["appconfig"]["collection"] = "appconfig";
        args["modules"] = new Object;
        var modulesArr = this.modulesTable.getSingleValue();
        for (o in modulesArr) {
        	args["modules"][o] = o;
        }
        
        var mdata = "";
        for (o in this.modules) {
        	if (typeof this.modules[o] != "function") {        			
            	mdata = this.modules[o].getValues().toObject();
            	if (args["modules"][mdata["md_name"]]!=null)
        			args["modules"][mdata["md_name"]] = mdata;
        	}
        };
        for (o in data) {
        	if (typeof data[o] != "function")
        		args[o] = data[o];
        };
        args["roles"] = this.rolesTable.getSingleValue();
        var mbox = this;
        var wm = getWindowManager();
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        
        this.node.appendChild(loading_img);
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "ApacheUser_"+mbox.module_id+"_"+data["old_name"],hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response.length>1)
                {
                    response = response.evalJSON(true);
                    if (response["error"]!=null)
                    {
                        mbox.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        mbox.reportMessage(response,"error",true);
                }
                else {
                    var old_id = "ApacheUser_"+mbox.module_id+"_"+getClientId(data["old_name"]);
                    var new_id = "ApacheUser_"+mbox.module_id+"_"+getClientId(data["name"]);
                    var new_target_id = "ApacheUser_"+mbox.module_id+"_"+data["name"];
                    var owner_id = getClientId("SystemSettingsUsers_"+mbox.module_id);
                    var params = new Array;
                    if (mbox.opener_item!=null && mbox.opener_item!=0 && mbox.opener_item.parentNode !=null)
                        params["object_id"] = old_id;
                    params["new_object_id"] = new_id;
                    params["target_id"] = new_target_id;
                    params["text"] = data["name"];
                    params["old_text"] = data["old_name"];
                    params["parent"] = owner_id;
                    var icon = mbox.skinPath+"images/Tree/user.gif";
                    params["image"] = icon;
                    if (data["old_name"]=="")
                        params["action"] = "add";
                    else
                        params["action"] = "change";
                    mbox.raiseEvent("NODE_CHANGED",params,true);
                    params["object_id"] = old_id;
                    mbox.raiseEvent("ENTITY_CHANGED",params,true);  
                    mbox.win.node.setAttribute('changed',"false");
                    if (mbox.opener_item!=null && mbox.opener_item!=0) {
	                    mbox.win.opener_item = mbox.opener_item.id;
	                    if (new_id!=old_id) {
	                    	mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_id),getClientId(new_id));
	                    }
                    }
                    wm.remove_window(mbox.win.id);
                }
            }
        })
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide4.2.1","HTMLBook_"+this.module_id+"_controller_4.2.1",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    },
    
    CONTROL_VALUE_CHANGED_processEvent: function(params) {
    	if (params["object_id"]==this.node.id+"_wabBackgroundColor") {
    		$I(this.node.id+"_wabBackgroundTest","").setAttribute("style","background-color:#"+params["value"]);
    	}
    },
    
    refreshDuration_onClick: function(event) {
    	$O(this.node.id+"_activeDuration",'').calc({user: this.name, returnType: "string"});
    	$O(this.node.id+"_isUserActive",'').calc({user: this.name});
    },
    
    disconnectUser_onClick: function(event) {
    	var obj = this;
    	var args = new Object;
    	if (this.banned) {
    		args["switch"] = "unban";
    	}
    	else {
    		args["switch"] = "ban";
    	}
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "ApacheUser_"+obj.module_id+"_"+obj.name,hook: '4', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = transport.responseText;            	
            	if (response=="") {
            		$O(obj.node.id+"_activeDuration",'').calc({user: obj.name, returnType: "string"});
            		$O(obj.node.id+"_isUserActive",'').calc({user: obj.name});
            		if (args["switch"]=="ban") {
            			$I(obj.node.id+"_disconnectUser").value = "  Включить  ";
            			obj.banned = true;
            		} else {
            			$I(obj.node.id+"_disconnectUser").value = "  Отключить  ";
            			obj.banned = false;            			
            		}
            		var params = new Array;
                    params["object_id"] =  obj.object_id;
                    params["new_object_id"] = obj.object_id;
                    params["target_id"] = obj.object_id;
                    params["text"] = obj.name
                    params["old_text"] = obj.name;
                    var owner_id = getClientId("SystemSettingsUsers_"+obj.module_id);
                    params["parent"] = owner_id
                    var icon = obj.skinPath+"images/Tree/user.gif";
                    params["image"] = icon;
                    params["action"] = "change";
                    obj.raiseEvent("ENTITY_CHANGED",params,true);  
                    obj.win.node.setAttribute('changed',"false");
                    $I(obj.win.node.id+"_headertext").innerHTML = $I(obj.win.node.id+"_headertext").innerHTML.replace("*","");
                    
            	} else
            		obj.reportMessage(response,"error",true);
            }
        });    	
    }
});