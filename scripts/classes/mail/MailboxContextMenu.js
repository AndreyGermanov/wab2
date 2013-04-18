var MailboxContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var mboxid = this.opener_item.getAttribute("target_object");
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "change":              
                var params = new Array;                
                getWindowManager().show_window("Window_"+mboxid.replace(/_/g,""),mboxid,params,this.opener_object.object_id,this.opener_item.id);
                break;
            case "add_remote_mailbox":
                var owner = this.opener_item.getAttribute("target_object").replace("Mailbox_","").replace(module_id+"_","");
                owner = owner.split("_");
                var owner_end = owner.pop();
                owner = owner.join("_")+"@"+owner_end;
                var params = new Object;
                params["owner"] = owner;
                params["hook"] = "setParams";
                getWindowManager().show_window("Window_RemoteMailbox"+module_id.replace(/_/g,""),"RemoteMailbox_"+module_id+"_",params,this.opener_object,this.opener_item.id);
                break;
            case "remove":
                var domain = mboxid.split("_").pop();
                var name = mboxid.replace("_"+domain,"").replace("Mailbox_","").replace(module_id+"_","");
                if (confirm("Вы действительно хотите удалить почтовый ящик "+name+"@"+domain+" вместе со всей почтой ?"))
                {
                	var params = new Object;
                	params["name"] = name;
                	params["domain"] = domain;
                    var menu = this;
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true,object_id: "Mailboxes_"+module_id, hook: '3', arguments: Object.toJSON(params)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response.length>1)
                            {
                                response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    menu.reportMessage(response_object["error"],"error",true);
                                else
                                    menu.reportMessage(response,"error",true);
                            }
                            else
                            {
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("Mailbox_"+module_id+"_"+name+"_"+domain)),true);
                            }
                        }
                    });
                }
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});