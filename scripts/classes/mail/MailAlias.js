var MailAlias = Class.create(Mailbox, {
    OK_onClick: function(event) {
            var is_changed = this.win.node.getAttribute('changed');
            if (is_changed == "true") {
                var data = this.getValues();
                if (data["name"]=="" || data["name"]=="Новый") {
                    this.reportMessage("Укажите имя почтового ящика !","error",true);
                    return 0;
                }
                
                var args = data.toObject();
                
                var old_name = data['old_name'];
                var old_domain = data['old_domain'];
                var objectid = "MailAlias_"+this.module_id+"_"+old_name+"_"+old_domain;
                var mbox = this;
                var loading_img = document.createElement("img");                
                loading_img.src = this.skinPath+"images/Tree/loading2.gif";
                loading_img.style.zIndex = "100";
                loading_img.style.position = "absolute";
                loading_img.style.top=(window.innerHeight/2-33);
                loading_img.style.left=(window.innerWidth/2-33);        
                this.node.appendChild(loading_img);
                var params = new Array;
                new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true, object_id: objectid,
                                     hook: '3',arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText).replace("\n","");
                            mbox.node.removeChild(loading_img);
                            if (response.length>1) {
                                response = response.evalJSON();
                                if (response["error"]!=null)
                                    mbox.reportMessage(response["error"],"error",true);
                            } else {
                                if (data['name']!=data['old_name'] ||
                                data['domain']!=data['old_domain']) {
                                    if (mbox.opener_item!=null) {
                                        var object_type = mbox.opener_object.node.id.split("_").shift();
                                        if (object_type == "Tree" || object_type=="CollectorMXTree") {
                                            if (data["old_name"]=="") {
                                                if (mbox.opener_item.parentNode !=null)
                                                    params["object_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"];
                                                params["new_object_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"];
                                                params["target_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"];
                                                params["text"] = data["name"]+"@"+data["domain"];
                                                params["old_text"] = data["old_name"]+"@"+data["old_domain"];
                                                params["parent"] = mbox.module_id+"_"+data["domain"]+"_domain";
                                                var icon = mbox.skinPath+"images/Tree/maillist.gif";
                                                params["image"] = icon;
                                                params["action"] = "add";
                                                mbox.raiseEvent("NODE_CHANGED",params,true);
                                                if (mbox.opener_item.parentNode !=null)
                                                    params["object_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"]+"_Addresses";
                                                params["new_object_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"]+"_Addresses";
                                                params["target_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"]+"_Addresses";
                                                params["text"] = "Адресаты";
                                                params["old_text"] = "";
                                                params["parent"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"];
                                                var icon = mbox.skinPath+"images/Tree/mail_addresses.gif";
                                                params["image"] = icon;
                                                params["action"] = "add";
                                                mbox.raiseEvent("NODE_CHANGED",params,true);
                                                if (mbox.opener_item.parentNode !=null)
                                                    params["object_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"]+"_RemoteMailboxes";
                                                params["new_object_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"]+"_RemoteMailboxes";
                                                params["target_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"]+"_RemoteMailboxes";
                                                params["text"] = "Ящики в Интернете";
                                                params["old_text"] = "";
                                                params["parent"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"];
                                                icon = mbox.skinPath+"images/Tree/remote_mailboxes.gif";
                                                params["image"] = icon;
                                                params["action"] = "add";
                                                mbox.raiseEvent("NODE_CHANGED",params,true);
                                            } else {
                                                params = new Array;
                                                if (mbox.opener_item.parentNode !=null)
                                                    params["object_id"] = "MailAlias_"+mbox.module_id+"_"+data["old_name"]+"_"+data["old_domain"];
                                                params["new_object_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"];
                                                params["target_id"] = "MailAlias_"+mbox.module_id+"_"+data["name"]+"_"+data["domain"];
                                                params["text"] = data["name"]+"@"+data["domain"];
                                                params["old_text"] = data["old_name"]+"@"+data["old_domain"];
                                                params["parent"] = mbox.module_id+"_"+data["domain"]+"_domain";
                                                var icon = mbox.skinPath+"images/Tree/maillist.gif";
                                                params["image"] = icon;
                                                params["action"] = "change";
                                                mbox.raiseEvent("NODE_CHANGED",params,true);
                                            }
                                        }
                                    }
                                }
                                if (mbox.win!="") {
                                    mbox.win.node.setAttribute("changed","false");
                                    mbox.win.opener_item = mbox.opener_item.id;
                                    if (params["object_id"]!=params["new_object_id"]) {
                                        mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(params["object_id"]),getClientId(params["new_object_id"]));
                                    }
                                    getWindowManager().remove_window(mbox.win.id);
                                }
                            }
                        }
                    });
            }
            else {
                if (this.win!="")
                    getWindowManager().remove_window(this.win.id);
            }
    },

    editAddress_onChange: function(event) {
        this.win.node.setAttribute('changed',"false");
        this.node.setAttribute('changed',"false");
        event = event || window.event;
        event.cancelBubble = true;
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    },

    AddressOK_onClick: function(event) {        
        var is_changed = this.win.node.getAttribute('changed');
        var object = this;
        var data = this.getValues();
        var mbox = this;
        if (data["address"]==data["old_address"] && data["owner"] == data["old_owner"]) {
            this.win.node.setAttribute('changed','false');
            is_changed = false;
        }
        if (is_changed =="true") {
            var owner_parts = data["owner"].split("@");
            var old_owner_parts = data["old_owner"].split("@");
            data["name"] = owner_parts[0]; data["domain"] = owner_parts[1];
            data["old_name"] = old_owner_parts[0]; data["old_domain"] = old_owner_parts[1];
            if (data["name"]=="" || data["name"]=="Новый") {
                this.reportMessage("Укажите имя списка рассылки, в который входит этот адрес !","error",true);
                return 0;
            }

            if (data["address"]=="") {
                this.reportMessage("Укажите адрес !","error",true);
                return 0;
            }
            var args = data.toObject();
            if (data["old_name"]!=data["name"] || data["old_domain"]!=data["domain"]) {
                params[params.length] = "$Objects->get('MailAlias_"+this.module_id+"_"+data["old_name"]+"_"+data["old_domain"]+"')->remove('"+data["address"]+"')";
            }
            var objectid="MailAlias_"+this.module_id+"_"+data["name"]+"_"+data["domain"];
            var loading_img = document.createElement("img");                
            loading_img.src = this.skinPath+"images/Tree/loading2.gif";
            loading_img.style.zIndex = "100";
            loading_img.style.position = "absolute";
            loading_img.style.top=(window.innerHeight/2-33);
            loading_img.style.left=(window.innerWidth/2-33);        
            this.node.appendChild(loading_img);
            new Ajax.Request("index.php", {
                    method: "post",
                    parameters: {ajax: true, object_id: objectid,
                                 hook: '4', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = trim(transport.responseText.replace("\n",""));
                        mbox.node.removeChild(loading_img);
                        if (response.length>1) {
                            var response_object = response.evalJSON();
                            if (response_object["error"]!=null)
                                mbox.reportMessage(response_object["error"],"error",true);
                            else
                                mbox.reportMessage(response,"error",true);                            
                        } else {
                            var old_id = getClientId("MailAlias_"+mbox.module_id+"_"+data['old_name']+"_"+data['old_domain']+"_Addresses_"+data["old_address"]);
                            var new_id = getClientId("MailAlias_"+mbox.module_id+"_"+data['name']+"_"+data['domain']+"_Addresses_"+data["address"]);
                            var new_target_id = "MailAlias_"+mbox.module_id+"_"+data['name']+"_"+data['domain']+"_Addresses_"+data["address"];
                            var owner_id = getClientId("MailAlias_"+mbox.module_id+"_"+data['name']+"_"+data['domain']+"_Addresses");
                            var params = new Array;
                            if (mbox.opener_item.parentNode !=null)
                                params["object_id"] = old_id;
                            params["new_object_id"] = new_id;
                            params["target_id"] = new_target_id;
                            params["text"] = data["address"];
                            params["old_text"] = data["old_address"];
                            params["parent"] = owner_id;
                            var icon = mbox.skinPath+"images/Tree/mailbox_alias.gif";
                            params["image"] = icon;
                            if (data["old_address"]=="")
                                params["action"] = "add";
                            else
                                params["action"] = "change";
                            is_changed = object.win.node.setAttribute('changed','false');
                            mbox.raiseEvent("NODE_CHANGED",params,true);
                            if ($I(mbox.node.id+"_editAddress").checked == true) {
                                getWindowManager().show_window("Window_Address"+mbox.module_id.replace(/_/g,"")+data["address"].replace(/_/g,""),"Address_"+mbox.module_id+"_"+data["address"],null,'MailApplication',"Tree_Mail_tree_AddressBook");
                            }
                            mbox.win.opener_item = mbox.opener_item.id;
                            if (new_id!=old_id) {
                                mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_id),getClientId(new_id));
                            }              
                            getWindowManager().remove_window(mbox.win.id,mbox.instance_id);                            
                        }
                    }
            });
        }
        else {           
                if ($(this.node.id+"_editAddress").checked == true) {                    
                    getWindowManager().show_window("Window_Address"+mbox.module_id.replace(/_/g,"")+data["address"].replace(/_/g,""),"Address_"+mbox.module_id+"_"+data["address"],null,'MailApplication',"Tree_Mail_tree_AddressBook");
                }
            getWindowManager().remove_window(this.win.id,this.instance_id);
        }
    },

    spamboxBtn_onClick: function(event) {
        if (this.selectWindow==null) {
            var leftPosition = (screen.availWidth-250)/2;
            var topPosition = (screen.availHeight-300)/2;
            var target_item = this.node.id+"_spambox";
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
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector8","HTMLBook_"+this.module_id+"_collector_8",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }
});