var CollectorMXTree = Class.create(Tree, {

    onExpandClick: function(event)
    {
        var elem = eventTarget(event);        
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');        
        var tree = this;
        var hooks = new Array('4','5','3');
        var elem_ids = new Array("Mailboxes","AddressBook","SystemSettingsUsers");
        var i=0;
        for (i=0;i<hooks.length;i++) {
			if (elem_id == elem_ids[i]+"_"+this.module_id)
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
							parameters: {ajax: true, object_id: 'CollectorMXTree_'+tree.module_id+'_mail',
										 hook: hooks[i]},
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
        var elem_starts = new Array("Mailbox","ApacheUser","Address","AddressBook","RemoteMailbox","MailQueue",
									"MailScannerConfig","RepFilterConfig","MailSettings","HTMLBook","AddressBookDefaultFields","EventLog");
									
		for (var i=0;i<elem_starts.length;i++) {
			if (elem_start == elem_starts[i])
			{            
				getWindowManager().show_window(window_elem_id,elem_id,null,'MailApplication_'+this.module_id,elem.id);
				elem.setAttribute('class',"tree_item_selected");
			}
		}
        
        if (elem_start == "MailAlias" && (elem_end!="RemoteMailboxes" && elem_end!="Addresses"))
        {
            var params = null;
            var arr = elem_id.split("_");
            var elem_id_arr = new Array;
            var found = false;
            for (var counter=0;counter<arr.length;counter++) {   
                if (arr[counter]=="Addresses") {
                    params = new Object;
                    elem_end = arr.slice(counter+1,arr.length).join("_");
                    params["address"] = elem_end;
                    params["instance"] = this.module_id.replace(/_/g,'')+getClientId(elem_end).replace(/_/g,'');
                    params["template"] = "templates/mail/MailAliasAddress.html";
                    params["object_text"] = elem_end;
                    params["icon"] = this.skinPath+"images/Tree/mailbox_alias.gif'";
                    params["hook"] = "setParams";
                    elem_id = elem_id_arr.join("_");                   
                    window_elem_id = "Window_"+elem_id.replace(/_/g,"")+"Address"+this.module_id.replace(/_/g,'')+getClientId(elem_end).replace(/_/g,"");                     
                    getWindowManager().show_window(window_elem_id,elem_id,params,'MailApplication_'+this.module_id,elem.id,"Address");
                    found = true;
                    break;
                }
                elem_id_arr[elem_id_arr.length] = arr[counter];
            }
            if (!found)
                getWindowManager().show_window(window_elem_id,elem_id,params,'MailApplication_'+this.module_id,elem.id);
            elem.setAttribute('class',"tree_item_selected");
        }
       
        if (elem_end == "domain") {   
            getWindowManager().show_window("Window_MailDomain"+elem_id.replace("_domain","").replace(/_/g,""),"MailDomain_"+elem_id.replace("_domain",""),null,'Application_'+this.module_id,elem.id);
            elem.setAttribute('class',"tree_item_selected");
        }
                
        if (elem_start == "ControlPanel") {
            getWindowManager().show_window("Window_ControlPanelPropertiesProps","ControlPanelProperties_Props",null,'EnterpriseApplication_'+this.module_id,elem.id);
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
        
        var elem_starts = new Array("Mailbox","ApacheUser","Address","AddressBook","RemoteMailbox","MailQueue",
									"MailScannerConfig","RepFilterConfig","MailSettings","HTMLBook","ControlPanel","AddressBookDefaultFields","EventLog");
		for (var i=0;i<elem_starts.length;i++) {
			if (elem_start == elem_starts[i]) {
				elems=elem.parentNode.getElementsByTagName("*");
				for (var el=0;el<elems.length;el++) {
					if (elems[el].parentNode != elem.parentNode)
						continue;
					if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
						elems[el].setAttribute("class","tree_item_hover");
				}
			}
		}
        if (elem_start == "MailAlias" && (elem_end!="RemoteMailboxes" && elem_end!="Addresses")) {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }
        if (elem_end == "domain") {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
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
        var elems = this.root_node.getElementsByClassName('tree_item_selected');
        for (var counter=0;counter<elems.length;counter++) {
            if (elems[counter].getAttribute("class")!="tree_item_selected")
                elems[counter].setAttribute('class','tree_item');
        }
        var elem_start = elem_id.split('_').shift();
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        var elem_starts = new Array("Mailbox","ApacheUser","Address","AddressBook","RemoteMailbox","MailQueue",
									"MailScannerConfig","RepFilterConfig","MailSettings","HTMLBook","ControlPanel","AddressBookDefaultFields","EventLogutn");
        var i=0;
		for (i=0;i<elem_starts.length;i++) {
			if (elem_start == elem_starts[i]) {
				elems=elem.parentNode.getElementsByTagName("*");
				for (var el=0;el<elems.length;el++) {
					if (elems[el].parentNode !== elem.parentNode)
						continue;
					if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
						elems[el].setAttribute("class","tree_item");
				}
			}
		}
        if (elem_start == "MailAlias" && (elem_end!="RemoteMailboxes" && elem_end!="Addresses")) {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
        if (elem_end == "domain") {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
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
        var elem_id_end = elem_id.split("_").pop();
        event = event || window.event;
        event.cancelBubble = true;
        if (elem_id_end=="domain") {   
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("MailboxesContextMenu_domain",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        var elem_starts = new Array("Mailbox","RemoteMailbox","Address","ApacheUser","ControlPanel");
        var i=0;
		for (i=0;i<elem_starts.length;i++) {
			if (elem_id_start==elem_starts[i]) {
				var objectid = elem.getAttribute("object");
				var instanceid = elem.getAttribute("instance");
				$O(objectid,instanceid).show_context_menu(elem_starts[i]+"ContextMenu_mailbox",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
			}
		}
        
        if (elem_id_start=="MailAlias" && elem_id_end=="RemoteMailboxes") {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("MailAliasRemoteMailboxesContextMenu_mailbox",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        else
        if (elem_id_start == "MailAlias" && elem_id_end!="RemoteMailboxes" && elem_id_end!="Addresses") {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");

            var arr = elem_id.split("_");
            var elem_id_arr = new Array;
            var found = false;
            for (var counter=0;counter<arr.length;counter++) {
                if (arr[counter]=="Addresses") {
                    params = new Array;
                    elem_end = arr[counter+1];
                    elem_id = elem_id_arr.join("_");
                    $O(objectid,instanceid).show_context_menu("MailAliasAddressContextMenu_mailbox",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
                    found = true;
                    break;
                }
                elem_id_arr[elem_id_arr.length] = arr[counter];
            }
            if (!found)
                $O(objectid,instanceid).show_context_menu("MailAliasContextMenu_mailbox",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        else {
            if (elem_id_end == "Addresses") {
                var objectid = elem.getAttribute("object");
                var instanceid = elem.getAttribute("instance");
                $O(objectid,instanceid).show_context_menu("MailAliasAddressesContextMenu_mailbox",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
            }
        }
        if (elem_id_start=="Mailboxes") {            
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("DomainsContextMenu_mailbox",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
        if (elem_id_start=="SystemSettingsUsers") {
            var objectid = elem.getAttribute("object");
            var instanceid = elem.getAttribute("instance");
            $O(objectid,instanceid).show_context_menu("ApacheUsersContextMenu_user",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
        }
		if (elem_id_start=="AddressBook") {
			var objectid = elem.getAttribute("object");
			var instanceid = elem.getAttribute("instance");
			$O(objectid,instanceid).show_context_menu("AddressBookTreeContextMenu_address",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
		}
        if (event.preventDefault)
            event.preventDefault();
        else
            event.returnValue= false;
        return false;
    }
});