var DhcpHost = Class.create(Mailbox, {
    OK_onClick: function(event) {

        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true")
        {
            getWindowManager().remove_window(this.win.object_id);
            return 0;
        }
        // Проверяем корректность ввода данных
        var data = this.getValues();            

        if (trim(data["name"])=="") {
            this.reportMessage('Укажите имя узла!',"error",true);
            return 0;
        }
        
        if (trim(data["hw_address"])=="") {
            this.reportMessage('Укажите MAC-адрес!',"error",true);
            return 0;
        }

        if (data["hw_address"].match(/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/)==null) {
            this.reportMessage("MAC-адрес указан неверно!","error",true);
            return 0;
        }


        if (trim(data["fixed_address"])=="") {
            this.reportMessage('Укажите IP-адрес!',"error",true);
            return 0;
        }

        if (!check_ip(data["fixed_address"])) {
            this.reportMessage('IP-адрес указан неверно!',"error",true);
            return 0;
        }

        if ((data["allow_booting"]=="")) {
            data["allow_booting"] = "deny booting";
        } else data["allow_booting"] = "allow booting";

        if ((data["ip_forwarding"]==""))
            data["ip_forwarding"] = "false";
        else
            data["ip_forwarding"] = "true";

        if (data["subnet_mask"]!="" && !check_ip(data["subnet_mask"])) {
            this.reportMessage("Маска сети указана неверно !","error",true);
            return 0;
        }

        if (data["next_server"]!="" && !check_ip(data["next_server"])) {
            this.reportMessage("TFTP-сервер указан неверно !","error",true);
            return 0;
        }

        if (data["routers"]!="" && !check_ip(data["routers"])) {
            this.reportMessage("Шлюз по умолчанию указан неверно !","error",true);
            return 0;
        }

        if (data["domain_name_servers"]!="") {
            ip_arr1 = data["domain_name_servers"].split(",");
            for (c=0;c<ip_arr1.length;c++) {
                if (!check_ip(ip_arr1[c])) {
                    alert("Серверы DNS указаны неверно !");
                    return 0;
                }
            }
        }

        if (data["netbios_name_servers"]!="") {
            ip_arr1 = data["netbios_name_servers"].split(",");
            for (c=0;c<ip_arr1.length;c++) {
                if (!check_ip(ip_arr1[c])) {
                    alert("Серверы WINS указаны неверно !");
                    return 0;
                }
            }
        }

        if (data["interface_mtu"]!="" && data["interface_mtu"].match(/^[0-9]+$/)==null) {
            this.reportError("Максимвльный размер фрэйма указан неверно !","error",true)
            return 0;
        }
        if ($O("PortsRedirectTable_"+this.objectid,"") != null && $O("PortsRedirectTable_"+this.objectid,"")!="")
            var openPorts = $O("PortsRedirectTable_"+this.objectid,"").getSingleValue();
        else
            var openPorts = "";
        var args = data.toObject();
        args["openPorts"] = openPorts;

        if ($O(this.access_rules_table)!=0 && $O(this.access_rules_table)!=null)
            var access_rules = $O(this.access_rules_table).getChecked();
        else
            var access_rules = "";
        args["accessRules"] = new Object;
        args["smbShares"] = new Object;
        args["nfsShares"] = new Object;
        args["afpShares"] = new Object;
        if (access_rules!="") {
            var access_rules_arr = access_rules.split("|");
            var c=0;
            for (c=0;c<access_rules_arr.length;c++) {
                var access_rule = access_rules_arr[c].split('~');
                args["accessRules"][access_rule[0]] = new Object;
                args["accessRules"][access_rule[0]]["path"] = access_rule[1];
                args["accessRules"][access_rule[0]]["read_only"] = access_rule[2];                
                if (access_rule[3]!="") {
					args["smbShares"][access_rule[0]] = "yes";
				}
                if (access_rule[4]!="") {
					args["nfsShares"][access_rule[0]] = "yes";
				}
                if (access_rule[5]!="") {
					args["afpShares"][access_rule[0]] = "yes";
				}
            }
        }

        var mbox = this;
        var wm = getWindowManager();
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        
        this.node.appendChild(loading_img);
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "DhcpHost_"+this.module_id+"_"+data["old_subnet_name"]+"_"+data["old_name"],hook: '4', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response!="" && response!=parseInt(response))
                {
                    response = response.evalJSON(true);
                    if (response["error"]!=null)
                    {
                        mbox.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        mbox.reportMessage(response,"error",true);
                }
                else {                    
                    var old_id = "DhcpHost_"+mbox.module_id+"_"+getClientId(data["old_subnet_name"])+"_"+getClientId(data["old_name"]);
                    var new_id = "DhcpHost_"+mbox.module_id+"_"+getClientId(data["subnet_name"])+"_"+getClientId(data["name"]);
                    var old_target_id = "DhcpHost_"+mbox.module_id+"_"+data["old_subnet_name"]+"_"+data["old_name"];
                    var new_target_id = "DhcpHost_"+mbox.module_id+"_"+data["subnet_name"]+"_"+data["name"];
                    var old_owner_id = getClientId("DhcpSubnet_"+mbox.module_id+"_"+data["old_subnet_name"]);
                    var new_owner_id = getClientId("DhcpSubnet_"+mbox.module_id+"_"+data["subnet_name"]);
                    var params = new Array;
                    if (mbox.opener_item.parentNode !=null)
                        params["object_id"] = old_id;
                    params["new_object_id"] = new_id;
                    params["target_id"] = new_target_id;
                    params["text"] = data["name"];
                    params["old_text"] = data["old_name"];
                    params["title"] = data["title"];
                    params["old_title"] = data["old_title"];
                    params["parent"] = new_owner_id;
                        icon = mbox.host_types_icons[mbox.host_types_ids.indexOf(data["host_type"])];
                    params["image"] = icon;
                    mbox.win.node.setAttribute('changed',"false");
                    wm.remove_window(mbox.win.object_id);
                    if (data["old_name"]=="")
                    {
                        params["action"] = "add";
                        mbox.raiseEvent("NODE_CHANGED",params,true);
                    }
                    else
                    {
                        params["action"] = "change";
                        mbox.raiseEvent("NODE_CHANGED",params,true);
                    }
                    if (new_target_id!=old_target_id) {
                        mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_target_id),getClientId(new_target_id));
                    }
                }
            }
        })
    },
    onRemoveWindow: function (topWindow) {
            delete topWindow.objects.objects[this.tabset_id];
            delete topWindow.objects.objects[this.access_rules_table];
    },
    
    scanhost_onClick: function(event) {
       var data = this.getValues();
       if (data["fixed_address"]=="")
           return 0;
        var mbox = this;
        var wm = getWindowManager();
        var args = new Object;
        args["fixed_address"] = data["fixed_address"];        
        $I(mbox.node.id+"_scanarea").innerHTML = "Идет сканирование ...";
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "DhcpHost_"+this.module_id+"_"+data["subnet_name"]+"_"+data["name"],hook: '5', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                $I(mbox.node.id+"_scanarea").innerHTML = response;
            }
        });
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide6.4","HTMLBook_"+this.module_id+"_controller_6.4",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    },
    
	referenceButton_onClick: function(event) {
		var but = eventTarget(event);
		var elemid = but.getAttribute("fileid");
		var windowid = "Window_"+elemid.replace(/_/g,'');
		var args = new Object;		
		var data = this.getValues();
		args["title"]=data["name"];
		args["objectId"] = this.object_id;
		args["hook"] = "setParams";
		wm.show_window(windowid,elemid,args,this.object_id,but.id,null);
	}         
});