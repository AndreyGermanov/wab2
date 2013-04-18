var WABEntityContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var item = this.opener_item.getAttribute("target_object");                
                var item_array = item.split("_");
                item_array.shift();
                item_array = item.split("_");
                item_array.pop();
                var new_item = item_array.join("_")+"_";                
                var params = new Object;
                params["hook"] = "afterInit";
                params["item"] = item;
                getWindowManager().show_window("Window_Window"+new_item.replace(/_/g,''),new_item,params,this.opener_object,this.opener_item.getAttribute("target_object"));
                break;
            case "add_by_template":
                var item = this.opener_item.getAttribute("target_object");                
                var item_array = item.split("_");
                item_array.shift();
                var item_array = item.split("_");
                item_array.pop();
                var new_item = item_array.join("_")+"_";                
                var params = new Object;
                params["hook"] = "2";
                params["item"] = item;
                getWindowManager().show_window("Window_Window"+new_item.replace(/_/g,''),new_item,params,this.opener_object,this.opener_item.id);
                break;
            case "change":
                var item = this.opener_item.getAttribute("target_object");
                var params = new Array;
                getWindowManager().show_window("Window_Window"+item.replace(/_/g,''),item,params,this.opener_object,this.opener_item.id);
                break;
            case "up":
                var item = this.opener_item.parentNode;
                var current_target = item.getAttribute("target_object");
                var sible = null;
                if (item!=null)
                    sible = item.previousSibling;
                if (sible!=null) {
                    var sible_target = sible.getAttribute("target_object");
                    var args = new Object;
                    args["sible_target"] = sible_target;
                    var obj = this;
                    new Ajax.Request("index.php", {
                        method:"post",
                        parameters: {ajax: true, object_id: current_target,hook: "move", arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {                            
                            var response = trim(transport.responseText.replace("\n",""));
                            var params = new Array;
                            params['object_id'] = current_target;
                            params['parent'] = response;
                            params['old_name'] = current_target.split("_").pop();
                            params['old_parent'] = '';
                            params['name'] = current_target.split("_").pop();
                            params['image'] = $I(item.id+"_image").src;
                            params["action"] = "move";
                            params['sible_id'] = sible_target;
                            obj.raiseEvent("ENTITY_CHANGED",params,true);
                        }
                    });                   
                }
                break;
            case "down":
                var item = this.opener_item.parentNode;
                var current_target = item.getAttribute("target_object");
                var sible = null;
                if (item!=null)
                    sible = item.nextSibling;
                if (sible!=null) {
                    var sible_target = sible.getAttribute("target_object");
                    var args = new Object;
                    args["sible_target"] = sible_target;
                    var obj = this;
                    new Ajax.Request("index.php", {
                        method:"post",
                        parameters: {ajax: true, object_id: current_target,hook: "move", arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {                            
                            var response = trim(transport.responseText.replace("\n",""));
                            var params = new Array;
                            params['object_id'] = current_target;
                            params['parent'] = response;
                            params['old_name'] = current_target.split("_").pop();
                            params['old_parent'] = '';
                            params['name'] = current_target.split("_").pop();
                            params['image'] = $I(item.id+"_image").src;
                            params["action"] = "move";
                            if (sible.nextSibling!=null)
                                params['sible_id'] = sible.nextSibling.getAttribute("target_object");
                            else
                                params['sible_id'] = '';
                            obj.raiseEvent("ENTITY_CHANGED",params,true);
                        }
                    });                    
                }
                break;
            case "remove":
                var item = this.opener_item.getAttribute("target_object");
                if (confirm("Вы действительно хотите удалить раздел и все его содержимое?"))
                {
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:item,hook: 'remove'},
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
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
        return false;
    }
});