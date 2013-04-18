var AddressContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var mboxid = this.opener_item.getAttribute("target_object");
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "change":
                var params = new Array;
                getWindowManager().show_window("Window_"+mboxid.replace(/_/g,""),mboxid,params,this.opener_object.object_id,this.opener_item.id);
                break;
            case "remove":
                var name = mboxid.replace("Address_","");
                name = name.split("_");
                var module_id = name.shift()+"_"+name.shift();
                name = name.join("_");
                if (confirm("Вы действительно хотите удалить адрес "+name+" ?"))
                {
					var args = new Object;
					args["address"] = name;                    
                    var menu = this;
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true,object_id: "AddressBook_"+module_id, hook: '4', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText).replace("\n","");
                            if (response.length>1)
                            {                                
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    menu.reportError(response_object["error"],"error",true);
                            }
                            else
                            {
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("Address_"+module_id+"_"+name)),true);
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