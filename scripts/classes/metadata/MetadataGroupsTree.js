var MetadataGroupsTree = Class.create(Tree, {

    onExpandClick: function(event)
    {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
         elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');
        elem_arr = elem_id.split("_");
        elem_arr.shift();
        var tree = this;

        if (elem_id == "Modules_"+this.module_id)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                var elem_arr = elem.id.split('_');
                elem_arr.pop();
                var root_elem = elem_arr.join('_');
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
        this.toggleTreeNode(elem_id);
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
        window_elem_id = "Window_"+elem_id.replace(/_/g,"");
        if (elem_end == "HIDED")
            return 0;
        if (elem_end == "Networks")
        {
            var elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
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
		for (var i=0;i<elem_starts.length;i++) {
			if (elem_start == elem_starts[i])
			{
				var elems=elem.parentNode.getElementsByTagName("*");
				for (var el=0;el<elems.length;el++) {
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
            for (var el=0;el<elems.length;el++) {
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
		for (var i=0;i<elem_starts.length;i++) {
			if (elem_start == elem_starts[i])	{
				var elems=elem.parentNode.getElementsByTagName("*");
				for (var el=0;el<elems.length;el++) {
					if (elems[el].parentNode !== elem.parentNode)
						continue;
					if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
						elems[el].setAttribute("class","tree_item");
				}
			}
		}
        return 0;
    }
});