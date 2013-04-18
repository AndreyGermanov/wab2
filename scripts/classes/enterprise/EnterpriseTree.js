var EnterpriseTree = Class.create(Tree, {

    onExpandClick: function(event)
    {
        var elem = eventTarget(event);        
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');        
        var tree = this;
        if (elem_id == "SystemSettingsUsers_"+this.module_id)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                $I(root_elem.concat("_content")).innerHTML = '';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'CollectorMXTree_'+this.module_id+'_mail',
                                     hook: '3'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace(" ","").replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
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
        if (elem_end == "HIDED")
            return 0;
        var window_elem_id = "Window_"+elem_id.replace(/_/g,"");
        if (elem_start == "LocalSettings")
        {
            var arr=location.href.split("/");
            arr.pop();
            arr=arr.join("/").split(":");
            if (arr.length>2)
                arr.pop();           
            window.open(arr.join(":")+":10000");
        }        
        if (elem_start == "VirtualBox")
        {
            var arr=location.href.split("/");
            arr.pop();
            window.open(arr.join("/")+"/vbox");
        }        
        if (elem_start == "ApacheUser")
        {
            getWindowManager().show_window(window_elem_id,elem_id,null,'MailApplication_'+this.module_id,elem.id);
            elem.setAttribute('class',"tree_item_selected");
        }
        if (elem_start == "ControlPanel")
        {
            getWindowManager().show_window("Window_ControlPanelPropertiesProps","ControlPanelProperties_Props",null,'EnterpriseApplication_'+this.module_id,elem.id);
            elem.setAttribute('class',"tree_item_selected");
        }
        
        if (elem_start == "HTMLBook")
        {
            var params = new Array;
            getWindowManager().show_window(window_elem_id,elem_id,params,this.module_id,elem.id);
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
        var elems = this.root_node.getElementsByClassName('tree_item_selected');
        for (var counter=0;counter<elems.length;counter++)
            if (elems[counter].getAttribute("class")!="tree_item_selected")
                elems[counter].setAttribute('class','tree_item');
        var elem_start = elem_id.split('_').shift();
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        window_elem_id = "Window_"+elem_id.replace(/_/g,"");
		var elem_starts = new Array("LocalSettings","VirtualBox","ApacheUser","ControlPanel","HTMLBook");
		for (var i=0;i<elem_starts.length;i++) {
		  if (elem_start == elem_starts[i])
		  {
		      elems=elem.parentNode.getElementsByTagName("*");
		      for (var el=0;el<elems.length;el++) {
			  if (elems[el].parentNode !== elem.parentNode)
			      continue;
			  if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
			      elems[el].setAttribute("class","tree_item_hover");
		      }
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
        var elems = this.root_node.getElementsByClassName('tree_item_selected');
        for (var counter=0;counter<elems.length;counter++) {
            if (elems[counter].getAttribute("class")!="tree_item_selected")
                elems[counter].setAttribute('class','tree_item');
        }
        var elem_start = elem_id.split('_').shift();
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
		var elem_starts = new Array("LocalSettings","VirtualBox","ApacheUser","ControlPanel","HTMLBook");
		for (var i=0;i<elem_starts.length;i++) {
		  if (elem_start == elem_starts[i])
		  {
	            elems=elem.parentNode.getElementsByTagName("*");
	            for (var el=0;el<elems.length;el++) {
	                if (elems[el].parentNode !== elem.parentNode)
	                    continue;
	                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
	                    elems[el].setAttribute("class","tree_item");
	            }
		  }
		}
    },

    onContextMenu: function(event) {       
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        var elem_id_start = elem_id.split("_").shift();        
        event = event || window.event;
        event.cancelBubble = true;
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
        if (elem_id_start=="ControlPanel")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("ControlPanelContextMenu_mailbox",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (event.preventDefault)
           event.preventDefault();
        else
           event.returnValue= false;
        return false;
    }
});