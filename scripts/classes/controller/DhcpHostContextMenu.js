var DhcpHostContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "change":
                var host = this.opener_item.getAttribute("target_object");
                var whost = this.opener_item.getAttribute("target_object").replace(/_/g,"");
                var params = new Array;
                getWindowManager().show_window("Window_"+whost,host,params,this.opener_object,this.opener_item.id);
                break;
            case "remove":
                var el = this.opener_item.getAttribute("target_object").split("_");
                var host = el.pop();
                var subnet = el.pop();
                if (confirm("Вы действительно хотите удалить хост "+host+" ?"))
                {
                    var args = new Object;
                    args['host'] = host;
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:"DhcpSubnet_"+module_id+"_"+subnet,hook:'3', arguments: Object.toJSON(args)},
                        onSuccess:function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response.length>1)
                            {
                                response = response.evalJSON();
                                if (response["error"]!=null)
                                    menu.reportMessage(response["error"],"error",true);
                            }
                            else
                            {
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("DhcpHost_"+module_id+"_"+subnet+"_"+host)),true);
                            }
                        }
                    });
                }
                break;
                case "control":
                    this.opener_object.show_context_menu("ControlContextMenu_"+this.opener_item.id,cursorPos(event).x-10,cursorPos(event).y-10,this.opener_item.id);
                break;
        }
        topWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});