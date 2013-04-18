var LinksContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var params = new Object;
                params["hook"] = "setParams";
                params["className"] = "";
                params["defaultClassName"] = "";
                params["tableClassName"] = ""; 
                var obj = this.opener_item.getAttribute("target_object");
                params["tableObject"] = this.opener_item.getAttribute("target_object");
                params["treeClassName"] = "EntityTree";                	
                params["adapterId"] = "DocFlowDataAdapter_"+module_id+"_1";
                params['present'] = "Выбор";
                params["classTitle"] = "";
                params["editorType"] = "WABWindow";
                params["selectGroup"] = "0";
                params["entityId"] = "";
                params["parent_object_id"] = this.opener_object.object_id;
        		params["condition"] = "@parent IS NOT EXISTS";
        		var elem_id = "EntitySelectWindow_"+module_id+"_"+obj.split("_").pop();
        		var window_elem_id = "Window_"+elem_id.replace(/\_/g,"");
                getWindowManager().show_window(window_elem_id,elem_id,params,module_id,this.node.id,null,true);                                                    
                break;
            case "remove":
                var link = this.opener_item.getAttribute("target_object");
                var parent_node = this.opener_item;
                var parent = "";
                while (1==1) {
                	parent_node = parent_node.parentNode;
                	if (parent_node==null)
                		break;
                	if (parent_node.getAttribute("target_object")=="null" || parent_node.getAttribute("target_object") == "") {
                		parent = this.opener_object.topObject;
                		break;
                	}
                	if (parent_node.getAttribute("target_object")!=link) {
                		parent = parent_node.getAttribute("target_object");
                		break;
                	}
                }
                if (confirm("Вы действительно хотите удалить эту ссылку ?")) {
                	var args = new Object;
                	args["entities"] = new Object;
                	args["entities"] = link;
            		args["mark"] = parent;
                    var obj = this; 
                    new Ajax.Request("index.php", {
                        method:"post",
                        parameters: {ajax: true, object_id: obj.opener_object.module_id, hook: '5', arguments: Object.toJSON(args)},
                        onSuccess: function(transport)
                        {                            
                            var response = trim(transport.responseText);
                            if (response!="") {
                                var rsp = response.evalJSON();
                                if (rsp) {
                                    var removed_objects = rsp["removed_objects"];
                                    if (removed_objects!="") {
                                        obj.raiseEvent("ENTITY_DELETED",$Arr("object_id="+removed_objects+",action=delete"),true);
                                        obj.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId(link),true));
                                    }
                                 } else
                                     obj.reportMessage(response,"error",true);
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