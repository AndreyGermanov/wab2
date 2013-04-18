var WebSiteRootContextMenu = Class.create(WABEntityRootContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        var tree = $O(this.opener_item.getAttribute("object"),"");
        var target_obj = this.opener_item.getAttribute("target_object");
        var siteId = target_obj.split("_").pop();
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
            	var item="";
                if (this.module_id=="") {
                    item = tree.defaultClassName+"_"+tree.module_id+"_"+siteId+"_";
                } else
                    item = tree.defaultClassName+"_"+siteId+"_";
                getWindowManager().show_window("Window_Window"+item.replace(/_/g,''),item,null,null,null);
                break;
            case "change":
                var item = this.opener_item.getAttribute("target_object");
                var params = new Array;
                getWindowManager().show_window("Window_Window"+item.replace(/_/g,''),item,params,this.opener_object,this.opener_item.id);
                break;        
            case "remove":
                var item = this.opener_item.getAttribute("target_object");
                if (confirm("Вы действительно хотите удалить Web-сайт и все его содержимое?"))
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
                                menu.raiseEvent("ENTITY_DELETED",$Arr("action=delete,object_id="+tree.object_id),true);
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