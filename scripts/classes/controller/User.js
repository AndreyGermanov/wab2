var User = Class.create(Mailbox, {
    OK_onClick: function(event) {

        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true")
        {
            getWindowManager().remove_window(this.win.object_id);
            return 0;
        }
        // Проверяем корректность ввода данных
        var data = this.getValues();
        var groups = new Array;

        if ($O(this.groups_table)!=null)
            groups = $O(this.groups_table).getChecked();
        
        if (trim(data["name"])=="") {
            this.reportMessage('Укажите системное имя!',"error",true);
            return 0;
        }
        
        if (trim(data["name"])!=trim(data["old_name"]) && trim(data["old_name"])=="guest") {
            this.reportMessage("Системного пользователя guest запрещено переименовывать !","error",true);
            return 0;
        }
        if (trim(data["gecos"])=="") {
            this.reportMessage('Укажите полное имя!',"error",true);
            return 0;
        }

        if (trim(data["password1"]) != trim(data["password2"])) {
            this.reportMessage('Пароли не совпадают !',"error",true);
            return 0;
        }

        if (trim(data["home_dir"])=="" && data["old_name"] != "") {
            this.reportMessage('Укажите домашнюю папку!',"error",true);
            return 0;
        }

        if (trim(data["shell"])=="") {
            this.reportMessage('Укажите командную оболочку!',"error",true);
            return 0;
        }
       
        // Формируем параметры, передаваемые серверу для записи
        var args = data.toObject();
        if (data["password1"]!="")
        	args["password"] = trim(data["password1"]);
        if (typeof(groups)!="undefined")
        	args["user_groups_string"] = groups;
        
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
            parameters: {ajax: true, object_id: "User_"+this.module_id+"_"+data["old_name"],hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response!="" && response!=parseInt(response))
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
                    var old_id = "User_"+mbox.module_id+"_"+getClientId(data["old_name"]);
                    var new_id = "User_"+mbox.module_id+"_"+getClientId(data["name"]);
                    var old_target_id = "User_"+mbox.module_id+"_"+data["old_name"];
                    var new_target_id = "User_"+mbox.module_id+"_"+data["name"];
                    var new_owner_id = getClientId("Users_"+mbox.module_id);
                    var params = new Array;
                    if (mbox.opener_item.parentNode !=null)
                        params["object_id"] = old_id;
                    params["new_object_id"] = new_id;
                    params["target_id"] = new_target_id;
                    params["text"] = data["name"];
                    params["old_text"] = data["old_name"];
                    params["parent"] = new_owner_id;
                    var icon = mbox.skinPath+"images/Tree/user.png";
                    params["image"] = icon;
                    if (data["old_name"]=="")
                        params["action"] = "add";
                    else
                        params["action"] = "change";
                    mbox.win.node.setAttribute('changed',"false");
                    if (new_target_id!=old_target_id) {
                        mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_target_id),getClientId(new_target_id));
                    }
                    mbox.raiseEvent("NODE_CHANGED",params,true);
                    if (mbox.mailIntegration!="") {
                        var old_id = "Mailbox_"+mbox.mailModuleId+"_"+getClientId(data['old_name'])+"_"+mbox.domain;
                        var new_id = "Mailbox_"+mbox.mailModuleId+"_"+getClientId(data['name'])+"_"+getClientId(mbox.domain);
                        var new_target_id = "Mailbox_"+mbox.mailModuleId+"_"+data['name']+"_"+mbox.domain;
                        var owner_id = mbox.mailModuleId+"_"+mbox.domain+"_domain";
                        var params = new Array;
                        params["object_id"] = old_id;
                        params["new_object_id"] = new_id;
                        params["target_id"] = new_target_id;
                        params["text"] = data["name"]+"@"+mbox.domain;
                        params["old_text"] = data["old_name"]+"@"+mbox.domain;
                        params["parent"] = getClientId(owner_id);
                        params["action"] = 'change';
                        var icon = mbox.skinPath+"images/Window/mail.gif";
                        params["image"] = icon;
                        if (mbox.new_mailbox)
                            params["action"] = "add";
                        else
                            params["action"] = "change";
                        mbox.raiseEvent("NODE_CHANGED",params,true);                                                
                    }
                    if (data["authType"]=="ldap" && (data["old_name"]!=data["name"] || (data["wabUser"]==1 && data["oldWabUser"]==0))) {
                        var old_id = "ApacheUser_"+mbox.module_id+"_"+getClientId(data["old_name"]);
                        var new_id = "ApacheUser_"+mbox.module_id+"_"+getClientId(data["name"]);
                        var new_target_id = "ApacheUser_"+mbox.module_id+"_"+data["name"];
                        var new_owner_id = getClientId("SystemSettingsUsers_"+mbox.module_id);
                        var params = new Array;
                        if (mbox.opener_item.parentNode !=null)
                            params["object_id"] = old_id;
                        params["new_object_id"] = new_id;
                        params["target_id"] = new_target_id;
                        params["text"] = data["name"];
                        params["old_text"] = data["old_name"];
                        params["parent"] = new_owner_id;
                        var icon = mbox.skinPath+"images/Tree/user.gif";
                        params["image"] = icon;
                        if (data["old_name"]=="" || (data["wabUser"]==1 && data["oldWabUser"]==0))
                            params["action"] = "add";
                        else
                            params["action"] = "change";
                        mbox.raiseEvent("NODE_CHANGED",params,true);                    	                    	
                    }
                    if (data["authType"]=="ldap" && (data["wabUser"]==0 && data["oldWabUser"]==1))
                    	mbox.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("ApacheUser_"+mbox.module_id+"_"+data["name"])),true);                    	
                    mbox.win.node.setAttribute('changed',"false");
                    wm.remove_window(mbox.win.object_id);
                }
            }
        });
    },
    
    onRemoveWindow: function (topWindow) {
            delete topWindow.objects.objects[this.tabset_id];
            delete topWindow.objects.objects[this.groups_table];
            delete topWindow.objects.objects[this.access_rules_table];
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide5.4","HTMLBook_"+this.module_id+"_controller_5.4",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    },

    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event == "TAB_CHANGED") {
            if (params["tabset_id"]==this.tabset_id) {
                if (params["tab"] == "groups" && this.groupsTabLoaded!=true) {
                    $I(this.node.id+"_groupsFrame").src = this.groupsFrameSrc;
                    this.groupsTabLoaded = true;
                }
                if (params["tab"] == "access_rules" && this.rightsTabLoaded!=true) {
                    $I(this.node.id+"_rightsFrame").src = this.rightsFrameSrc;
                    this.rightsTabLoaded = true;
                }
            }
        }
    },
    
    fileManagerButton_onClick: function(event) {
        var params = new Object;
        params["useCase"] = "sharesEditor";
        params["hook"] = "3";
        getWindowManager().show_window("Window_FileManagerShares","FileManager_"+this.module_id+"_Shares",params,this.object_id,eventTarget(event).id);        
    },
    
	referenceButton_onClick: function(event) {
		var but = eventTarget(event);
		var elemid = but.getAttribute("fileid");
		var windowid = "Window_"+elemid.replace(/_/g,'');
		var args = new Object;		
		var data = this.getValues();
		args["title"]=data["name"];
		args["objectId"] = this.object_id;
		args["hook"] = "setParams";
		wm.show_window(windowid,elemid,args,this.object_id,but.id,null);
	}     
});