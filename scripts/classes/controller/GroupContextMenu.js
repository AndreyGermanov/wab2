var GroupContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text","")) {
            case "change":
                var group = this.opener_item.getAttribute("target_object");
                var wgroup = this.opener_item.getAttribute("target_object").replace(/_/g,"");
                var params = new Array;
                getWindowManager().show_window("Window_"+wgroup,group,params,this.opener_object,this.opener_item.id);
                break;
            case "remove":
                var group = this.opener_item.getAttribute("target_object").split("_").pop();
                if (confirm("Вы действительно хотите удалить эту группу ?")) {                    
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:"Group_"+module_id+"_"+group,hook:'4'},
                        onSuccess:function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response.length>1) {
                                response = response.evalJSON();
                                if (response["error"]!=null)
                                    menu.reportMessage(response["error"],"error",true);
                            }
                            else {
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("Group_"+module_id+"_"+group)),true);
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