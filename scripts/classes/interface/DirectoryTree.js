var DirectoryTree = Class.create(Tree, {
 
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
        elem_arr.shift();
        elem_arr.pop();
        tree = this;
        if ($I(root_elem).getAttribute("loaded") == null || $I(root_elem).getAttribute("loaded")=="false")
        {
            elem.setAttribute("disable","true");
            if ($I(root_elem.concat("_content"))!=null)
                $I(root_elem.concat("_content")).innerHTML = '';
            var args = new Object;
            args["show_files"] = this.show_files;
            args["dir"] = target_elem;
            new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: 'DirectoryTree_'+tree.module_id+'_Dir',hook: '3',arguments: Object.toJSON(args)},                                 
                    onSuccess: function(transport) {
                        var response = transport.responseText.toString();                        
                        tree.fillTree(response);
                        $(root_elem).setAttribute("loaded","true");
                        tree.ok = true;
                        elem.setAttribute("disable","false");
                    }
                });
        }
        this.toggleTreeNode(elem_id);
    },

    onObjectClick: function(event) {
        var ev = new Array;
        var id = eventTarget(event).id;
        var id_arr = id.split("_");
        id_arr.pop();
        id = id_arr.join("_")+"_image";
        var file = $I(id).src.split("/").pop();
        if (this.selectFile=="true" && file!="file.png")
            return 0;
        ev["target"] = window.opener.document.getElementById(this.target_item);
        var obj = window.opener.document.getElementById(this.target_item);
        if (this.absolute_path)
            obj.value = eventTarget(event).getAttribute("target_object").replace(/\/\//g,'/');
        else
            obj.value = eventTarget(event).getAttribute("target_object").replace(this.root_dir+'/','');
        var object = obj.getAttribute("object");
        var instance = obj.getAttribute("instance");        
        var obje = window.opener.globalTopWindow.$O(object,instance);
        window.opener.globalTopWindow.$O(object,instance).onChange(ev);
        obje.raiseEvent("CONTROL_VALUE_CHANGED",$Arr('object_id='+obje.object_id+",parent_object_id="+obje.parent_object_id+",value="+obj.value));
        window.close();
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
        var elems = this.root_node.getElementsByClassName('tree_item_selected');
        for (var counter=0;counter<elems.length;counter++) {
            if (elems[counter].getAttribute("class")!="tree_item_selected")
                elems[counter].setAttribute('class','tree_item');
        }
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

    onContextMenu: function(event) {
        var elem = eventTarget(event);
        event = event || window.event;
        event.cancelBubble = true;
        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");
        if (elem.getAttribute("target_object")!=null)
            $O(objectid,instanceid).show_context_menu("DirectoryTreeContextMenu_dir",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        else
            $O(objectid,instanceid).show_context_menu("DirectoryTreeRootContextMenu_dir",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
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
        if (elem.getAttribute("target_object")!=null && elem.getAttribute("target_object")!="")
            $O(objectid,instanceid).show_context_menu("DirectoryTreeContextMenu_dir",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        else
            $O(objectid,instanceid).show_context_menu("DirectoryTreeRootContextMenu_dir",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    }
});