var MailAliasContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var mboxid = this.opener_item.getAttribute("target_object");
        var mbox_arr = mboxid.split("_");
        mbox_arr.shift();
        var module_id = mbox_arr.shift()+"_"+mbox_arr.shift();
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "change":
                var params = new Array;
                getWindowManager().show_window("Window_"+mboxid.replace(/_/g,""),mboxid,params,this.opener_object.object_id,this.opener_item.id);
                break;
            case "remove":
                var name = mboxid.replace("MailAlias_","").replace(module_id+"_","");
                var name_parts = name.split("_");
                if (confirm("Вы действительно хотите удалить список рассылки "+name+" ?"))
                {
                	var args = new Object;
                	args["name"]   = name_parts[0];
                	args["domain"] = name_parts[1];
                    var menu = this;
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true,object_id: "MailAliases_"+this.opener_object.module_id, hook: '3', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response.length>1)
                            {                                
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    menu.reportMessage(response_object["error"],"error",true);
                            }
                            else
                            {
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("MailAlias_"+module_id+"_"+name_parts[0]+"_"+name_parts[1])),true);
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