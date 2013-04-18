var LinksTree = Class.create(Tree, {
 
    onExpandClick: function(event) {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var target_elem = elem.getAttribute("target_object");
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');
        elem_arr = elem_id.split("_");
        elem_start = elem_arr.shift();
        elem_arr.pop();
        var tree = this;
        if ($I(root_elem).getAttribute("loaded") == null || $I(root_elem).getAttribute("loaded")=="false")
        {
            elem.setAttribute("disable","true");
            if ($I(root_elem.concat("_content"))!=null)
                $I(root_elem.concat("_content")).innerHTML = '';
            var args = new Object;
            args["topObject"] = this.topObject;
            args["parent"] = target_elem;
            $I(root_elem.concat("_content")).innerHTML = '';
            $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
            $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';            
            new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: 'LinksTree_'+tree.module_id+'_Links',hook: '3',arguments: Object.toJSON(args)},                                 
                    onSuccess: function(transport) {
                        elem.setAttribute("disable","false");
                        $(root_elem).setAttribute("loaded","true");
                        var response = transport.responseText.toString();                        
                        tree.fillTree(response);
                        tree.ok = true;
                        $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                    }
                });
        }
        this.toggleTreeNode(elem_id);
    },

    onObjectClick: function(event) {
        var elem_id = elem.getAttribute("target_object");
        window_elem_id = "Window_"+elem_id.replace(/_/g,"");
		getWindowManager().show_window(window_elem_id,elem_id,null,null,null);        
    },

    onMouseOver: function(event) {
        var elem = eventTarget(event);
        var par = elem.parentNode;
        var par_id = par.id.split("_");
        par_id = par_id.join("_");

        if ($I(par_id+"_text").getAttribute("class")=="tree_item_selected") {
            return 0;
        }
        var elem_id = elem.getAttribute("target_object");
        var elems = this.root_node.getElementsByClassName('tree_item_selected');
        for (var counter=0;counter<elems.length;counter++)
            if (elems[counter].getAttribute("class")!="tree_item_selected")
                elems[counter].setAttribute('class','tree_item');
        if (elem_id==null)
            return 0;
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        elems=elem.parentNode.getElementsByTagName("*");
        for (var el=0;el<elems.length;el++) {
            if (elems[el].parentNode != elem.parentNode)
                continue;
            if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                elems[el].setAttribute("class","tree_item_hover");
        }
    },

    onMouseOut: function(event) {
        var elem = eventTarget(event);
        var par = elem.parentNode;
        var par_id = par.id.split("_");
        par_id = par_id.join("_");
        if ($I(par_id+"_text").getAttribute("class")=="tree_item_selected") {
            return 0;
        }
        var elem_id = elem.getAttribute("target_object");
        var elems = this.root_node.getElementsByClassName('tree_item_selected');
        for (var counter=0;counter<elems.length;counter++) {
            if (elems[counter].getAttribute("class")!="tree_item_selected")
                elems[counter].setAttribute('class','tree_item');
        }
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        elems=elem.parentNode.getElementsByTagName("*");
        for (var el=0;el<elems.length;el++) {
            if (elems[el].parentNode !== elem.parentNode)
                continue;
            if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                elems[el].setAttribute("class","tree_item");
        }
    },

    onContextMenu: function(event) {
        var elem = eventTarget(event);
        event = event || window.event;
        event.cancelBubble = true;
        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");
        var params = new Object;
    	params["link_object"] = this.parent_object_id;
        if (elem.getAttribute("target_object")!=null) {
            $O(objectid,instanceid).show_context_menu("LinksContextMenu_dir",cursorPos(event).x-10,cursorPos(event).y-10,elem.id,params);
        }
        else {
            $O(objectid,instanceid).show_context_menu("LinksRootContextMenu_dir",cursorPos(event).x-10,cursorPos(event).y-10,elem.id,params);
        }
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    },

    rootContextMenu: function(event) {
        var elem = eventTarget(event);
        event = event || window.event;
        event.cancelBubble = true;
        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");
        var params = new Object;
        params["link_object"] = this.parent_object_id;        
        $O(objectid,instanceid).show_context_menu("LinksRootContextMenu_dir",cursorPos(event).x-10,cursorPos(event).y-10,elem.id,params);
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    },
    
    CONTROL_VALUE_CHANGED_processEvent: function(params) {
    	if (params["object_id"]==this.object_id) {
        	var args = new Object;
        	var links = new Object;
        	links[params["value"]] = params["value"];
        	args['links'] = links;        	
            var obj = this;      
            new Ajax.Request("index.php", {
                method:"post",
                parameters: {ajax: true, object_id: params["target_object"],hook: 'setLinks', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {                  
                	var response = transport.responseText;
                	obj.raiseEvent("ENTITY_CHANGED",$Arr("object_id="+params["value"]+",action=addlink"),true);
                	var parms = new Array;
                    var old_id = params["value"];
                    var new_id = params["value"];
                    var new_target_id = params["value"];
                    var new_owner_id = params["target_object"];
                    parms["object_id"] = old_id;
                    parms["new_object_id"] = new_id;
                    parms["target_id"] = new_target_id;
                    parms["text"] = params["valueTitle"];
                    parms["old_text"] = params["valueTitle"];
                    parms["title"] = params["valueTitle"];
                    parms["old_title"] = params["valueTitle"];
                    parms["parent"] = new_owner_id;
                    if (new_owner_id==obj.parent_object_id)
                    	parms["parent"] = "";
                    parms["image"] = response;
                    parms["action"] = "add";
                    obj.raiseEvent("NODE_CHANGED",parms,true);
                }
            });    		
    	}
    }    
});