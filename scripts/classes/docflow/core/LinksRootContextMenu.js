var LinksRootContextMenu = Class.create(ContextMenu,{

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
                var obj = this.opener_object.parent_object_id;
                params["tableObject"] = this.opener_object.parent_object_id;
                params["treeClassName"] = "EntityTree";                	
                params["adapterId"] = "DocFlowDataAdapter_"+module_id+"_1";
                params['present'] = "Выбор";
                params["classTitle"] = "";
                params["editorType"] = "WABWindow";
                params["selectGroup"] = "0";
                params["entityId"] = "";
                params["parent_object_id"] = this.opener_item.getAttribute("object");
        		params["condition"] = "@parent IS NOT EXISTS";
        		var elem_id = "EntitySelectWindow_"+module_id+"_"+obj.split("_").pop();
        		var window_elem_id = "Window_"+elem_id.replace(/\_/g,"");
                getWindowManager().show_window(window_elem_id,elem_id,params,module_id,this.node.id,null,true);                                                    
                break;
            case "refresh":
                var args = new Object;
                args["topObject"] = this.opener_object.topObject;                
                obj = this.opener_object;
                new Ajax.Request("index.php", {
                    method:"post",
                    parameters: {ajax: true, object_id: obj.object_id, hook: '3', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {                            
                        var response = trim(transport.responseText);
                        obj.deleteAllNodes();
                        obj.fillTree(response);
                    }
                });
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});