var DhcpSubnetContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var host = "DhcpHost_"+this.opener_item.getAttribute("target_object").replace("DhcpSubnet_","")+"_";
                var whost = host.replace(/_/g,"");
                var params = new Array;
                getWindowManager().show_window("Window_"+whost,host,params,this.opener_object,this.opener_item.id);
                break;
            case "change":
                var subnet = this.opener_item.getAttribute("target_object");
                var wsubnet = this.opener_item.getAttribute("target_object").replace(/_/g,"");
                var params = new Array;
                getWindowManager().show_window("Window_"+wsubnet,subnet,params,this.opener_object,this.opener_item.id);
                break;
            case "remove":

                var subnet = this.opener_item.getAttribute("target_object").split("_").pop();
                if (confirm("Вы действительно хотите удалить подсеть "+subnet+" и все хосты, которые внутри нее заведены?"))
                {
					var args = new Object;
					args["subnet"] = subnet;
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:"DhcpSubnets_"+module_id,hook: '3',arguments: Object.toJSON(args)},
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
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("DhcpSubnet_"+module_id+"_"+subnet)),true);
                            }
                        }

                    });
                }
                break;
        }
        topWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});