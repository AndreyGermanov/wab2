var ControllerTree = Class.create(Tree, {
    onExpandClick: function(event) {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        var elem_end = elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');
        elem_arr = elem_id.split("_");
        var elem_start = elem_arr.shift();
        elem_end = elem.getAttribute("target_object").split("_").pop();
        var tree = this;
        if (elem_end == "Networks")
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';                
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+tree.module_id+'_Networks',
                                     hook: "3"},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_start == "DhcpSubnet") {
            if ($I(root_elem).getAttribute("loaded")=="false") {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                var args = new Object;
                args['subnet_name'] = elem_end;
                new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+tree.module_id+'_Networks',
                                     hook: '4',arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString();
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_start == "FileShares")
        {
            if ($I(root_elem).getAttribute("loaded")=="false") {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+tree.module_id+'_Networks',
                                     hook: '5'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString();
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_start == "ObjectGroupProperties")
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+tree.module_id+'_Networks',
                                     hook: '6'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString();       
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_start == "Users")
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+tree.module_id+'_Networks',
                                     hook: '7'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString();
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable",false);
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_start == "Groups")
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+tree.module_id+'_Networks',
                                     hook: '8'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString();
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        if (elem_id == "SystemSettingsUsers_"+this.module_id)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'CollectorMXTree_'+this.module_id+'_mail',
                                     hook: '3'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }

        if (elem_id == "Modules_"+this.module_id)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+this.module_id+'_Networks',
                                     hook: '10'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }

        if (elem_id == "Metadata_"+this.module_id)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+this.module_id+'_Networks',
                                     hook: '11'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                            $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                        }
                    });
            }
        }
        
        if (elem_id == "DocFlowIntegrator_"+this.module_id+"_Docs")
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                elem_arr = elem.id.split('_');
                elem_end = elem_arr.pop();
                root_elem = elem_arr.join('_');
                $I(root_elem.concat("_content")).innerHTML = '';
                $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
                $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'ControllerTree_'+this.module_id+'_Networks',
                                     hook: '12'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace("\n","");
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
            
        if (elem_end == "Networks")
        {
            getWindowManager().show_window(window_elem_id,elem_id,null,this.module_id,elem.id);
        }
        var elem_starts = new Array("DhcpSubnet","DhcpHost","FileServer","FileShare","ObjectGroup","ApacheUser",
                                    "User","Group","SystemSettings","HTMLBook","MailIntegrator","GatewayIntegrator","DocFlowIntegrator",
                                    "FTPHost","ModelConfig","MailApplicationModuleConfig","ControllerApplicationModuleConfig",
                                    "DocFlowApplicationModuleConfig","WebServerApplicationModuleConfig");
        var refs_starts = new Array("ReferenceFiles","ReferenceUserInfoCard","ReferenceGroupInfoCard","ReferenceObjectGroupInfoCard","ReferenceDhcpHostInfoCard","ReferenceDhcpSubnetInfoCard");
        var i=0;
		for (i=0;i<elem_starts.length;i++) {
			if (elem_start == elem_starts[i]) {
				getWindowManager().show_window(window_elem_id,elem_id,null,this.module_id,elem.id);
				break;
			}
		}
		var i=0;
		for (i=0;i<refs_starts.length;i++) {
			if (elem_start == refs_starts[i]) {
	            var params = new Object;
	            params["hook"] = "3";
				getWindowManager().show_window(window_elem_id,elem_id,params,this.module_id,elem.id,null,true);
				break;
			}
		}
		
        if (elem_start == "FullAuditReport")
        {
            getWindowManager().show_window("Window_FullAuditReportreport","FullAuditReport_"+this.module_id+"_report",null,'ControllerApplication_'+this.module_id,elem.id,null,false);
        }
        if (elem_start == "EventLog")
        {
            getWindowManager().show_window("Window_EventLog","EventLog_"+this.module_id+"_Events",null,'ControllerApplication_'+this.module_id,elem.id,null,false);
        }
        if (elem_start == "ShadowCopyManager")
        {
            getWindowManager().show_window("Window_ShadowCopyManagermanager","ShadowCopyManager_"+this.module_id+"_manager",null,'ControllerApplication_'+this.module_id,elem.id,null,false);
        }
        if (elem_start == "DeleteMarkedObjectsWindow")
        {
            getWindowManager().show_window(window_elem_id,elem_id,null,this.module_id,elem.id,null,true);
        }
        if (elem_start == "FileShares") {
            var params = new Object;
            params["useCase"] = "sharesEditor";
            params["hook"] = "3";
            getWindowManager().show_window("Window_FileManagerShares","FileManager_"+this.module_id+"_Shares",params,'ControllerApplication_'+this.module_id,elem.id);
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
        if (elem_end == "Networks")
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
        var elem_starts = new Array("DhcpSubnet","DhcpHost","FileServer","FileShare","ObjectGroup","ApacheUser","User",
								"FTPHost","Group","SystemSettings","HTMLBook","MailIntegrator","GatewayIntegrator","DocFlowIntegrator",
								"FullAuditReport","EventLog","ShadowCopyManager","FileShares","ModelConfig","ControllerApplicationModuleConfig",
                                "DocFlowApplicationModuleConfig","WebServerApplicationModuleConfig","MailApplicationModuleConfig","DeleteMarkedObjectsWindow",
                                "ReferenceFiles","ReferenceUserInfoCard","ReferenceGroupInfoCard","ReferenceObjectGroupInfoCard","ReferenceDhcpHostInfoCard","ReferenceDhcpSubnetInfoCard");
        var i=0;
		for (i=0;i<elem_starts.length;i++) {
			if (elem_start == elem_starts[i])
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
		}
        return 0;
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
        if (elem_end == "Networks")
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
        var elem_starts = new Array("DhcpSubnet","DhcpHost","FileServer","FileShare","ObjectGroup","ApacheUser","User",
								"FTPHost","Group","SystemSettings","HTMLBook","MailIntegrator","GatewayIntegrator","DocFlowIntegrator",
								"FullAuditReport","EventLog","ShadowCopyManager","FileShares","ModelConfig","ControllerApplicationModuleConfig",
                                "DocFlowApplicationModuleConfig","WebServerApplicationModuleConfig","MailApplicationModuleConfig","DeleteMarkedObjectsWindow",
                                "ReferenceFiles","ReferenceUserInfoCard","ReferenceGroupInfoCard","ReferenceObjectGroupInfoCard","ReferenceDhcpHostInfoCard","ReferenceDhcpSubnetInfoCard");
        var i=0;
		for (i=0;i<elem_starts.length;i++) {
			if (elem_start == elem_starts[i])	{
				var elems=elem.parentNode.getElementsByTagName("*");
				var el=0;
				for (el=0;el<elems.length;el++) {
					if (elems[el].parentNode !== elem.parentNode)
						continue;
					if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
						elems[el].setAttribute("class","tree_item");
				}
			}
		}
        return 0;
    },

    onContextMenu: function(event) {
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        var elem_id_start = elem_id.split("_").shift();
        event = event || window.event;
        event.cancelBubble = true;
        var elem_starts = new Array("DhcpServer","DhcpSubnet","DhcpHost","FileShare",
									"ObjectGroup","Users","Groups","Group","ApacheUser",
									"ControlPanel","FileServer","DhcpServer");
        var i=0;
		for (i=0;i<elem_starts.length;i++) {
			if (elem_id_start==elem_starts[i]) {
				var objectid = elem.getAttribute("object");
				var instanceid = elem.getAttribute("instance");
				$O(objectid,instanceid).show_context_menu(elem_starts[i]+"ContextMenu_"+elem_starts[i],cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
			}
		}
        if (elem_id_start=="SystemSettingsUsers")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("ApacheUsersContextMenu_user",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (elem_id_start=="User")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            var elem_arr = elem.id.split("_");
            elem_arr.pop();
            var username = elem_arr.pop();
            $O(objectid,instanceid).show_context_menu("UserContextMenu_"+this.module_id+"_"+username,cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (elem_id_start=="ObjectGroupProperties")
        {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("ObjectGroupsContextMenu_share",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue = false;
        return false;
    },

    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event=="CHANGE_HOST_STATUS") {
            var img = $I(this.node.id+"_tree_"+params["host"]+"_image");
            if (img!=null) {
                if (params["status"]=="yes") {
                    img.src = img.src.replace("_bw","");
                }
                if (params["status"]=="no") {
                    var src = img.src;
                    img.src = img.src.replace("_bw","");
                    var arr = img.src.split(".");
                    var ext = arr.pop();
                    src = arr.join(".");
                    src = src+"_bw."+ext;
                    img.src = src;
                }
            }
        }
    }
});