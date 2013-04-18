var RemoteMailboxContextMenu = Class.create(ContextMenu,{
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
            case "remove":
                var name = mboxid.replace("RemoteMailbox_","").replace(module_id+"_","");
                if (confirm("Вы действительно хотите удалить почтовый ящик Интернет "+name+" ?"))
                {
                	var args = new Object;
                	args["name"] = name;
                    var menu = this;
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true,object_id: "RemoteMailboxes_"+module_id, hook: '3', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response!="")
                            {   
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    menu.reportMessage(response_object["error"],"error",true);
                            }
                            else
                            {
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("RemoteMailbox_"+module_id+"_"+name)),true);
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