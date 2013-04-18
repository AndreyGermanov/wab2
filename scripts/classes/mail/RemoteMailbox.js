var RemoteMailbox = Class.create(Mailbox, {

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

        if (!$(getClientId("RemoteMailbox_"+this.module_id+"_"+data["old_name"]+"_active")).checked)
            data["active"] = "skip";
        else
            data["active"] = "poll";
        
        if (data["name"]=="") {
            this.reportMessage('Укажите название почтового ящика!',"error",true);
            return 0;
        }
        if (data["owner"]=="") {
            this.reportMessage('Укажите почтовый ящик владельца!',"error",true);
            return 0;
        }
        if (data["server"]=="") {
            this.reportMessage('Укажите имя серврера!',"erro",true);
            return 0;
        }
        if (data["user"]=="") {
            this.reportMessage("Укажите имя пользователя!","error",true);
            return 0;
        }
        if (data["password"]=="") {
            this.reportMessage("Укажите пароль!","error",true);
            return 0;
        }
        if (data["port"]=="") {
            this.reportMessage("Укажите порт!","error",true);
            return 0;
        }
        // Формируем параметры, передаваемые серверу для записи
        var args = data.toObject();
        
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
            parameters: {ajax: true, object_id: "RemoteMailbox_"+this.module_id+"_"+data["old_name"],hook: '3', arguments: Object.toJSON(args)},
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
                    var edit_address = $(mbox.node.id+"_editAddress");
                    var old_id = "RemoteMailbox_"+mbox.module_id+"_"+getClientId(data["old_name"]);
                    var new_id = "RemoteMailbox_"+mbox.module_id+"_"+getClientId(data["name"]);
                    var new_target_id = "RemoteMailbox_"+mbox.module_id+"_"+data["name"];
                    var owner_id = getClientId("Mailbox_"+mbox.module_id+"_"+data["owner"].replace("@","_"));

                    if ($I(mbox.opener_object.node.id+"_tree_"+owner_id)==0) {   
                        owner_id = getClientId("MailAlias_"+mbox.module_id+"_"+data["owner"].replace("@","_")+"_RemoteMailboxes");
                    }
                    
                    var params = new Array;
                    if (mbox.opener_item.parentNode !=null)
                        params["object_id"] = old_id;
                    params["new_object_id"] = new_id;
                    params["target_id"] = new_target_id;
                    params["text"] = data["name"];
                    params["old_text"] = data["old_name"];
                    params["parent"] = owner_id;
                    var icon = mbox.skinPath+"images/Tree/RemoteMailbox.gif";
                    params["image"] = icon;
                    if (data["old_name"]=="")
                        params["action"] = "add";
                    else
                        params["action"] = "change";
                    if (edit_address.checked == true) {
                        var parms = new Array;
                        var op_item = "Tree_"+mbox.module_id+"_Mail_tree_Address_"+mbox.module_id+"_"+getClientId(data["old_name"]);
                        if ($I(op_item)==0)
                            op_item = "Tree_"+mbox.module_id+"_Mail_tree_AddressBook_"+mbox.module_id;
                        getWindowManager().show_window("Window_Address"+mbox.module_id.replace(/_/g,"")+data["name"],"Address_"+mbox.module_id+"_"+data["name"],parms,"Tree_mail_tree",op_item,null);
                    }
                    mbox.raiseEvent("NODE_CHANGED",params,true);
                    mbox.win.node.setAttribute('changed',"false");                    
                    mbox.win.opener_item = mbox.opener_item.id;
                    if (new_id!=old_id) {
                        mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_id),getClientId(new_id));
                    }
                    wm.remove_window(mbox.win.id);
                }
            }
        });
    },

    ownerBtn_onClick: function(event) {
        if (this.selectWindow==null) {
            var leftPosition = (screen.availWidth-250)/2;
            var topPosition = (screen.availHeight-300)/2;
            var target_item = this.node.id+"_owner";
            var params = new Object;
            params["target_item"] = target_item;
            params["site"] = this.site;
            params["show_mailboxes"] = "true";
            var args = "";
            this.selectWindow = window.showModalDialog("index.php?object_id=SelectMailAddressTree_"+this.module_id+"_Tree1&hook=3&arguments="+Object.toJSON(params),args,"dialogWidth:250px; dialogHeight:300px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
        }
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector7","HTMLBook_"+this.module_id+"_collector_7",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }    
});