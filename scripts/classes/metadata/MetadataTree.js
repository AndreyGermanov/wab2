var MetadataTree = Class.create(Tree, {
    
    fillTree: function($super,items) {
        $super(items);
        if ($I(this.node.id+"_tree_"+getClientId(this.selectedItem)+"_image")!=0) {
            $I(this.node.id+"_tree_"+getClientId(this.selectedItem)+"_image").setAttribute("class","tree_item_selected");
            $I(this.node.id+"_tree_"+getClientId(this.selectedItem)+"_text").setAttribute("class","tree_item_selected");
        }                                
    },

    onExpandClick: function(event) {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        var elem_end = elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');
        elem_arr = elem_id.split("_");
        elem_end = elem_arr.pop();
        var elem_pre_end = elem_arr.pop();
        var tree = this;
        var args = tree.getValues('loaded');        
        args["parent"] = elem_end;         
        args["rnd"] = elem_pre_end;
        if ($I(root_elem).getAttribute("loaded")=="false")
        {
            elem.setAttribute("disable","true");
            elem_arr = elem.id.split('_');
            elem_arr.pop();
            root_elem = elem_arr.join('_');
            $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
            $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
            $I(root_elem.concat("_content")).innerHTML = '';
            new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: tree.object_id, hook: '3', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        tree.fillTree(response);
                        $(root_elem).setAttribute("loaded","true");
                        elem.setAttribute("disable","false");
                        $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                    }
                });
        }
        this.toggleTreeNode(elem_id);
    },
    
    rootExpandClick: function(event) {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var tree = this;
        var args = this.getValues('loaded');
        
        if ($I(root_elem).getAttribute("loaded")=="false")
        {
            elem.setAttribute("disable","true");
            elem_arr = elem.id.split('_');
            elem_arr.pop();
            root_elem = elem_arr.join('_');
            $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
            $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
            $I(root_elem.concat("_content")).innerHTML = '';
            new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: tree.object_id,hook:'3', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response!="")
                            tree.fillTree(response);
                        $(root_elem).setAttribute("loaded","true");
                        elem.setAttribute("disable","false");
                        $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                    }
                });
        }            
        this.toggleTreeNode(root_elem);
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
        if (elem_id==null || elem_id=="") {
            elem_id = "";
            return 0;
        }
        var elem_start = elem_id.split('_').shift();
        if (elem_start=="MetadataTree")
        	return 0;
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        var elems=elem.parentNode.getElementsByTagName("*");
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
        if (elem_id==null)
            elem_id = "";
        var elem_start = elem_id.split('_').shift();
        if (elem_start=="MetadataTree")
        	return 0;
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        var elems=elem.parentNode.getElementsByTagName("*");
        for (var el=0;el<elems.length;el++) {
            if (elems[el].parentNode !== elem.parentNode)
                continue;
            if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                elems[el].setAttribute("class","tree_item");
        }
    },

    onObjectClick: function(event) {
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        if (elem_id==null || elem_id=="") {
        	return 0;
            elem_id = "";
        }
        var elem_start = elem_id.split('_').shift();
        if (elem_start=="MetadataTree")
        	return 0;
        var elem_end = elem_id.split("_").pop();
        var window_elem_id = "Window_"+elem_id.replace(/_/g,"");
        if (elem_end == "HIDED")
            return 0;
        if (this.forSelect) {
        	if (elem_start=="MetadataGroup" || elem_start=="MetadataModelGroup" || elem_start=="MetadataCodesGroup") {
        		if (this.groupSelect) {
        			$O(this.opener_item.id,'').setValue(elem_end);
        			this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.opener_item.id+",value="+elem_end));
        			$O(this.opener_item.id,'').setFocus();
        			removeContextMenu();
        		}
        	}
        	if (elem_start=="MetadataObjectField" || elem_start=="MetadataObjectModel" || elem_start=="MetadataObjectCode" || elem_start=="MetadataPanel" || elem_start == "ModuleConfig" || elem_start == "MetadataInterface" || elem_start == "MetadataRole" || elem_start == "MetadataObjectTag" || elem_start == "LDAPAddressBook") {
        		if (this.itemSelect) {
        			$O(this.opener_item.id,'').setValue(elem_end);
        			this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.opener_item.id+",value="+elem_end));
        			$O(this.opener_item.id,'').setFocus();
                	removeContextMenu();
        		}
        	}
        	return 0;
        }
        var elem = eventTarget(event);
        elem_id = elem.getAttribute("target_object");
        var params = new Object;
		getWindowManager().show_window(window_elem_id,elem_id,params,this.module_id,elem.id);
    },
    
    onContextMenu: function(event) {
        var elem = eventTarget(event);
        event = event || window.event;
        event.cancelBubble = true;

        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");
        var args = new Object;
        args["target"] = elem.getAttribute("target_object");
        $O(objectid,instanceid).show_context_menu(this.contextMenuClass+"_elem",cursorPos(event).x-10,cursorPos(event).y-10,elem.id,args);

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
        var args = new Object;
        args["target"] = elem.getAttribute("target_object");
        args["metadataClass"] = this.metadataClass;
        args["metadataGroupClass"] = this.metadataGroupClass;
        if (!this.hide_root_context_menu)
            $O(objectid,instanceid).show_context_menu(this.rootContextMenuClass+"_elem",cursorPos(event).x-10,cursorPos(event).y-10,elem.id,args);

         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    }   
});