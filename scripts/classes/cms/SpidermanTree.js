var SpidermanTree = Class.create(Tree, {

    onExpandClick: function(event)
    {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        var elem_end = elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');
        elem_arr = elem_id.split("_");
        var elem_start = elem_arr.shift();
        elem_end = elem_arr.pop();
        var tree = this;
        if (elem_id == "Sites")
        {
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
                        parameters: {ajax: true, object_id: 'SpidermanTree_Mail',hook: '3'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace(" ","").replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_id == "Templates")
        {
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
                        parameters: {ajax: true, object_id: 'SpidermanTree_'+this.module_id+'_Sites',hook: '4'},
                        onSuccess: function(transport) {
                            response = transport.responseText;
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_start == "ItemTemplate")
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                $I(root_elem.concat("_content")).innerHTML = '';
                var args = new Object;
                args["parent_id"] = elem_arr[2];
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'SpidermanTree_'+this.module_id+'_Sites',hook: '4', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = transport.responseText;//.toString().replace(" ","").replace("\n","");
                           
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_start == "WebSite")
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                $I(root_elem.concat("_content")).innerHTML = '';
                var args = new Object;
                args["site"] = elem_end;
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'SpidermanTree_'+this.module_id+'_Sites', hook: '5', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = transport.responseText;
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (webitem_classes.indexOf(","+elem_start+",") != -1)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                $I(root_elem.concat("_content")).innerHTML = '';
                var parent_item = elem_arr.pop();
                var item = elem_arr.pop();
                var site = elem_arr.pop();
                var args = new Object;
                args["site"] = site;
                args["item"] = item;
                args["parent_item"] = parent_item;
                args["elem_end"] = elem_end;
                new Ajax.Request("index.php", {
                    method: "post",
                    parameters: {ajax: true, object_id: 'SpidermanTree_'+this.module_id+'_Sites',hook: '6', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        tree.fillTree(response);
                        $(root_elem).setAttribute("loaded","true");
                        elem.setAttribute("disable","false");
                        $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                    }
                });
            }
        }

        if (elem_start == "SystemSettingsUsers")
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                var elem_arr = elem.id.split('_');
                elem_arr.pop();
                var root_elem = elem_arr.join('_');
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                $I(root_elem.concat("_content")).innerHTML = '';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'SpidermanTree_'+this.module_id+"_Sites", hook: '7'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace(" ","").replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        this.toggleTreeNode(elem_id);
    },

    onObjectClick: function(event) {
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        var elem_start = elem_id.split('_').shift();
        var elem_end = elem_id.split("_").pop();
        var window_elem_id = "Window_"+elem_id.replace(/_/g,"");
        if (elem_end == "HIDED")
            return 0;
        if (elem_start == "WebSite")
        {            
            getWindowManager().show_window(window_elem_id,elem_id,null,this.module_id,elem.id);
        }
        if (elem_start == "ItemTemplate")
        {
            getWindowManager().show_window(window_elem_id,elem_id,null,this.module_id,elem.id);
        }
        if (webitem_classes.indexOf(","+elem_start+",") != -1)
        {
            var elem_arr = elem_id.split("_");
            var params = new Array;
            params[0] = "$object->init_string='$object->openAs=\""+elem_arr.pop()+"\";$object->setTemplate()';";
            var elemid = elem_arr.join("_");
            window_elem_id = "Window_"+elemid.replace(/_/g,"");
            getWindowManager().show_window(window_elem_id,elemid,params,this.module_id,elem.id);
        }
        if (elem_start == "ApacheUser")
        {
            getWindowManager().show_window(window_elem_id,elem_id,null,'MailApplication_'+this.module_id,elem.id);
        }
        if (elem_start == "ReferenceUsers")
        {            
        	var args = new Object;
        	args["hook"] = "3";
        	args["object_text"] = "Пользователи";
            getWindowManager().show_window(window_elem_id,elem_id,args,this.module_id,elem.id,null,true);
        }
        if (elem_start == "DeleteMarkedObjectsWindow")
        {            
        	var args = new Object;
        	args["hook"] = "3";
        	args["object_text"] = "Удаление помеченных объектов";
            getWindowManager().show_window(window_elem_id,elem_id,args,this.module_id,elem.id,null,true);
        }

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
        var elem_start = elem_id.split('_').shift();
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        if (elem_start == "WebSite")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode != elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }
        if (elem_start == "ItemTemplate")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode != elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }
        if (elem_start == "ReferenceUsers")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode != elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }        
        if (elem_start == "DeleteMarkedObjectsWindow")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode != elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }        
        if (webitem_classes.indexOf(","+elem_start+",") != -1)
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode != elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }
        if (elem_start == "ApacheUser")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode != elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
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
        var elem_start = elem_id.split('_').shift();
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        if (elem_start == "WebSite")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
        if (elem_start == "ItemTemplate")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
        if (elem_start == "ReferenceUsers")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
        if (elem_start == "DeleteMarkedObjectsWindow")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
        if (webitem_classes.indexOf(","+elem_start+",") != -1)
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
        if (elem_start == "ApacheUser")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
    },

    onContextMenu: function(event) {
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        var elem_id_start = elem_id.split("_").shift();
        event = event || window.event;
        event.cancelBubble = true;
        if (elem_id_start=="WebSite")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("WebSiteContextMenu_site",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (elem_id_start=="Sites")
        {
            if (this.addWebSites=='1') {
                var objectid = elem.getAttribute("object");
                var instanceid = elem.getAttribute("instance");
                $O(objectid,instanceid).show_context_menu("WebSitesContextMenu_site",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
            }
        }
        if (elem_id_start=="Templates")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("ItemTemplatesContextMenu_template",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (elem_id_start=="ItemTemplate")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("ItemTemplateContextMenu_template",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (webitem_classes.indexOf(","+elem_id_start+",") != -1)
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("WebItemContextMenu_template",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (elem_id_start=="SystemSettingsUsers")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("ApacheUsersContextMenu_user",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (elem_id_start=="ApacheUser")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("ApacheUserContextMenu_mailbox",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }

         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    }
});