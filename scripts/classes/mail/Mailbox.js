var Mailbox = Class.create(Entity, {

    OK_onClick: function(event) {        
            if (blur_error!="")
                return 0;
            var is_changed = this.win.node.getAttribute('changed');
            if (is_changed =="true")
            {
                var data = this.getValues();

                if (data["name"]=="" || data["name"]=="Новый")
                {
                    this.reportMessage("Укажите имя почтового ящика !","error",true);
                    return 0;
                }
                
                if (data["password"] != data["password1"])
                {
                    this.reportMessage('Пароли не совпадают !',"error",true);
                    return 0;
                }
                
                if (data["autoreply_enabled"]!=0 && data["autoreply_enabled"]!=false && data["autoreply_enabled"]!=null)
                    data["autoreply_enabled"] = "true";
                else
                    data["autoreply_enabled"] = "false";
                data["autoreply_text"] = data["autoreply_text"].replace(/\'/g,"#|#X#-");
                
                var args = data.toObject();
                                
                var old_name = data['old_name'];
                var old_domain = data['old_domain'];
                var objectid = "Mailbox_"+this.module_id+"_"+old_name+"_"+old_domain;
                var mbox = this;
                var loading_img = document.createElement("img");                
                loading_img.src = this.skinPath+"images/Tree/loading2.gif";
                loading_img.style.zIndex = "100";
                loading_img.style.position = "absolute";
                loading_img.style.top=(window.innerHeight/2-33);
                loading_img.style.left=(window.innerWidth/2-33);        
                this.node.appendChild(loading_img);
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: objectid,
                                     hook: '3', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText).replace("\n","");
                            mbox.node.removeChild(loading_img);
                            if (response.length>1)
                            {
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    mbox.reportMessage(response_object["error"],"error",true);
                                else
                                    mbox.reportMessage(response,"error",true);
                            }
                            else
                            {
                                var old_id = "Mailbox_"+mbox.module_id+"_"+getClientId(data['old_name'])+"_"+getClientId(data['old_domain']);
                                var new_id = "Mailbox_"+mbox.module_id+"_"+getClientId(data['name'])+"_"+getClientId(data['domain']);
                                var new_target_id = "Mailbox_"+mbox.module_id+"_"+data['name']+"_"+data['domain'];
                                var owner_id = mbox.module_id+"_"+data['domain']+"_domain";
                                var params = new Array;
                                //if (mbox.opener_item.parentNode !=null)
                                    params["object_id"] = old_id;
                                params["new_object_id"] = new_id;
                                params["target_id"] = new_target_id;
                                params["text"] = data["name"]+"@"+data["domain"];
                                params["old_text"] = data["old_name"]+"@"+data["old_domain"];
                                params["parent"] = getClientId(owner_id);
                                params["action"] = 'change';
                                var icon = mbox.skinPath+"images/Window/mail.gif";
                                params["image"] = icon;

                                if (data['name']!=data['old_name'] ||
                                data['domain']!=data['old_domain'])
                                {                                    
                                    if (mbox.opener_item!=null)
                                    {   
                                        var object_type = mbox.opener_object.node.id.split("_").shift();
                                        if (object_type == "Tree" || object_type=="CollectorMXTree")
                                        {
                                            if (data["old_name"]=="")
                                                params["action"] = "add";
                                            else
                                                params["action"] = "change";
                                        }
                                    }
                                }
                                if (mbox.win!="")
                                {
                                    var parms = new Array;
                                    if ($(mbox.node.id+"_editAddress").checked == true) {
                                        var op_item = "Tree_"+mbox.module_id+"_Mail_tree_Address_"+mbox.module_id+"_"+getClientId(data["old_name"]+"@"+getClientId(data["domain"]));
                                        if ($I(op_item)==0)
                                            op_item = "Tree_"+mbox.module_id+"_Mail_tree_AddressBook_"+mbox.module_id;
                                        
                                        getWindowManager().show_window("Window_Address"+mbox.module_id.replace(/_/g,"")+data["name"]+"@"+data["domain"],"Address_"+mbox.module_id+"_"+data["name"]+"@"+data["domain"],parms,"Tree_mail_tree",op_item,null);
                                    }
                                    mbox.win.node.setAttribute("changed","false");
                                    mbox.win.opener_item = mbox.opener_item.id;
                                    if (new_id!=old_id) {
                                        mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_id),getClientId(new_id));
                                    }
                                    getWindowManager().remove_window(mbox.win.id);
                                    mbox.raiseEvent("NODE_CHANGED",params,true);
                                }
                            }
                        }
                    });
            }
            else
            {
                if (this.win!="")
                    getWindowManager().remove_window(this.win.id);
            }
    },

    returnAddressBtn_onClick: function(event) {

        if (this.selectWindow==null) {
            var leftPosition = (screen.availWidth-250)/2;
            var topPosition = (screen.availHeight-300)/2;
            var target_item = this.node.id+"_returnAddress";
            var params = new Object;
            params["show_mailboxes"] = "true";
            params["show_remote_mailboxes"] = "true";
            params["show_addressbook"] = "true";
            params["target_item"] = target_item; 
            params["site"] = this.site; 
            var args = "";
            this.selectWindow = window.showModalDialog("index.php?object_id=SelectMailAddressTree_"+this.module_id+"_Tree1&hook=3&arguments="+Object.toJSON(params),args,"dialogWidth:250px; dialogHeight:300px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
        }
    },

    spamboxBtn_onClick: function(event) {
        if (this.selectWindow==null) {
            var leftPosition = (screen.availWidth-250)/2;
            var topPosition = (screen.availHeight-300)/2;
            var target_item = this.node.id+"_spambox";
            var params = new Object;
            params["show_mailboxes"] = "true";
            params["target_item"] = target_item; 
            params["site"] = this.site; 
            var args = "";
            this.selectWindow = window.showModalDialog("index.php?object_id=SelectMailAddressTree_"+this.module_id+"_Tree1&hook=3&arguments="+Object.toJSON(params),args,"dialogWidth:250px; dialogHeight:300px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
        }
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector6","HTMLBook_"+this.module_id+"_collector_6",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }    
});