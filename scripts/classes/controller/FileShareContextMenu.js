var FileShareContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text","")) {
            case "change":
                var share = this.opener_item.getAttribute("target_object");
                var wshare = this.opener_item.getAttribute("target_object").replace(/_/g,"");
                var params = new Array;
                getWindowManager().show_window("Window_"+wshare,share,params,this.opener_object,this.opener_item.id);
                break;
            case "remove":
                var share = this.opener_item.getAttribute("target_object").split("_").pop();
                if (share==0) {
                    this.reportMessage("Нельзя удалить корневую папку !","error",true);
                    topWindow.removeContextMenu();
                    event = event || window.event;
                    event.cancelBubble = true;
                    return 0;
                }
                if (confirm("Вы действительно хотите удалить эту общую папку ?")) {
                    var initstring="$object->removeShare('"+share+"');";
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:"FileServer_"+module_id+"_Shares",init_string:initstring},
                        onSuccess:function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response.length>1)
                            {
                                var response = response.evalJSON();
                                if (response["error"]!=null)
                                    menu.reportMessage(response["error"],"error",true);
                            }
                            else
                            {
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("FileShare_"+module_id+"_"+share)),true);
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