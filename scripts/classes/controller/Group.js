var Group = Class.create(Mailbox, {
    OK_onClick: function(event) {

        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true") {
            getWindowManager().remove_window(this.win.object_id);
            return 0;
        }
        // Проверяем корректность ввода данных
        var data = this.getValues();
        var users = $O(this.users_table).getChecked();
        
        if (trim(data["name"])=="") {
            this.reportMessage('Укажите системное имя!',"error",true);
            return 0;
        }
        
        // Формируем параметры, передаваемые серверу для записи
        var args = new Object;
        args = data.toObject();
        args["group_users_string"] = users;

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
            parameters: {ajax: true, object_id: "Group_"+this.module_id+"_"+data["old_name"],hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response.length!="" && response!=parseInt(response))
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
                    var old_id = "Group_"+mbox.module_id+"_"+getClientId(data["old_name"]);
                    var new_id = "Group_"+mbox.module_id+"_"+getClientId(data["name"]);
                    var old_target_id = "Group_"+mbox.module_id+"_"+data["old_name"];
                    var new_target_id = "Group_"+mbox.module_id+"_"+data["name"];
                    var new_owner_id = getClientId("Groups_"+mbox.module_id);

                    var params = new Array;
                    if (mbox.opener_item.parentNode !=null)
                        params["object_id"] = old_id;
                    params["new_object_id"] = new_id;
                    params["target_id"] = new_target_id;
                    params["text"] = data["name"];
                    params["old_text"] = data["old_name"];
                    params["parent"] = new_owner_id;
                    var icon = mbox.skinPath+"images/Tree/group.png";
                    params["image"] = icon;

                    if (data["old_name"]=="")
                        params["action"] = "add";
                    else
                        params["action"] = "change";
                    mbox.raiseEvent("NODE_CHANGED",params,true);
                    if (new_target_id!=old_target_id) {
                        mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_target_id),getClientId(new_target_id));
                    }
                    mbox.win.node.setAttribute('changed',"false");
                    wm.remove_window(mbox.win.object_id);
                }
            }
        });
    },
    
    onRemoveWindow: function (topWindow) {
    	delete topWindow.objects.objects[this.tabset_id];
    	delete topWindow.objects.objects[this.users_table];
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide5.4.5","HTMLBook_"+this.module_id+"_controller_5.4.5",params,this.opener_item.getAttribute("object"),this.opener_item.id);
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