var WebTemplateTreeContextMenu = Class.create(WABEntityContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text","")) {
            case "add":
                var item = this.opener_item.getAttribute("target_object");                
                var item_array = item.split("_");
                item_array = item.split("_");
                item_array.pop();
                var new_item = item_array.join("_")+"_";                
                var params = new Object;
                params["item"] = item;
                params["hook"] = "3";
                getWindowManager().show_window("Window_Window"+new_item.replace(/_/g,''),new_item,params,this.opener_object,this.opener_item.getAttribute("target_object"));
                break;
            case "add_by_template":
                var item = this.opener_item.getAttribute("target_object");                
                var item_array = item.split("_");
                item_array = item.split("_");
                item_array.pop();
                var new_item = item_array.join("_")+"_";                
                var params = new Object;
                params["item"] = item;
                params["hook"] = "copyFrom";
                getWindowManager().show_window("Window_Window"+new_item.replace(/_/g,''),new_item,params,this.opener_object,this.opener_item.id);
                break;
            case "change":
                var item = this.opener_item.getAttribute("target_object");
                var params = new Array;
                getWindowManager().show_window("Window_Window"+item.replace(/_/g,''),item,params,this.opener_object,this.opener_item.id);
                break;
            case "remove":
                var item = this.opener_item.getAttribute("target_object");
                if (confirm("Вы действительно хотите удалить шаблон и все его содержимое?"))
                {
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:item,hook: "remove"},
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
                                menu.raiseEvent("ENTITY_DELETED",$Arr("action=delete,object_id="+getClientId(item)),true);
                            }
                        }
                    });
                }
                break;
        }
        topWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
        return false;
    }
});