var DhcpSubnet = Class.create(Mailbox, {
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

        if (trim(data["title"])=="") {
            this.reportMessage('Укажите название сети!',"error",true);
            return 0;
        }

        if (trim(data["name"])=="") {
            this.reportMessage('Укажите IP-адрес сети!',"error",true);
            return 0;
        }

        if (!check_ip(data["name"])) {
            this.reportMessage("IP-адрес сети указан неверно !","error",true);
            return 0;
        }

        if (trim(data["subnet_mask"])=="") {
            this.reportMessage('Укажите маску сети!',"error",true);
            return 0;
        }

        if ((trim(data["range_start"])=="" && trim(data["range_end"])!="") || (trim(data["range_start"])!="" && trim(data["range_end"])=="")) {
            this.reportMessage('Неверно указан диапазон адресов!',"error",true);
            return 0;
        }
        if (trim(data["range_start"])!="" && trim(data["range_end"])!="")
            data["range"] = trim(data["range_start"]) + " " + trim(data["range_end"]);
        else
            data["range"] = "";

        if ((data["allow_unknown_clients"]==""))
            data["allow_unknown_clients"] = "deny-unknown-clients";
        else
            data["allow_unknown_clients"] = "allow-unknown-clients";

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
            var ip_arr1 = data["domain_name_servers"].split(",");
            var c=0;
            for (c=0;c<ip_arr1.length;c++) {
                if (!check_ip(ip_arr1[c])) {
                    this.reportMessage("Серверы DNS указаны неверно !","error",true);
                    return 0;
                }
            }
        }

        if (data["netbios_name_servers"]!="") {
            var ip_arr1 = data["netbios_name_servers"].split(",");
            var c=0;
            for (c=0;c<ip_arr1.length;c++) {
                if (!check_ip(ip_arr1[c])) {
                    this.reportMessage("Серверы WINS указаны неверно !","error",true);
                    return 0;
                }
            }
        }

        if (!check_ip(data["range_start"])) {
            this.reportMessage("Диапазон адресов указан неверно !","error",true);
            return 0;
        }

        if (!check_ip(data["range_end"])) {
            this.reportMessage("Диапазон адресов указан неверно !","error",true);
            return 0;
        }

        // Формируем параметры, передаваемые серверу для записи
        var args = data.toObject();

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
            parameters: {ajax: true, object_id: "DhcpSubnet_"+this.module_id+"_"+data["old_name"],hook: '4', arguments: Object.toJSON(args)},
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
                    var old_id = "DhcpSubnet_"+mbox.module_id+"_"+getClientId(data["old_name"]);
                    var new_id = "DhcpSubnet_"+mbox.module_id+"_"+getClientId(data["name"]);
                    var old_target_id = "DhcpSubnet_"+mbox.module_id+"_"+data["old_name"];
                    var new_target_id = "DhcpSubnet_"+mbox.module_id+"_"+data["name"];
                    var owner_id = getClientId("DhcpServer_"+mbox.module_id+"_Networks");
                    var icon = mbox.skinPath+"images/Tree/network.gif";                    
                    var params = new Array;
                    if (mbox.opener_item.parentNode !=null)
                        params["object_id"] = old_id;
                    params["new_object_id"] = new_id;
                    params["target_id"] = new_target_id;
                    params["title"] = data["name"];
                    params["old_title"] = data["old_name"];
                    params["text"] = data["title"];
                    params["old_text"] = data["old_title"];
                    params["parent"] = owner_id;
                    params["image"] = icon;
                    if (data["old_name"]!=data["name"] && data["old_name"]!="") {
                        mbox.win.node.setAttribute('changed',"false");
                        wm.remove_window(mbox.win.object_id);
                        var elems = mbox.opener_item.parentNode.getElementsByTagName("*");
                        var el=0;
                        for (el=0;el<elems.length;el++) {
                            if (elems[el].id!=null)
                                elems[el].id = elems[el].id.replace(getClientId(data["old_name"]),getClientId(data["name"]));
                            if (elems[el].getAttribute("target_object")!=null)
                                elems[el].setAttribute("target_object",elems[el].getAttribute("target_object").replace(data["old_name"],data["name"]));
                        }
                    }
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
                    if (mbox.win!=null) {
                        mbox.win.node.setAttribute('changed',"false");
                        wm.remove_window(mbox.win.object_id);
                    }
                }
            }
        });
    },

    scanHosts_onClick: function() {
        var data = this.getValues();
        delete topWindow.objects.objects["ScanNetworkTable_"+this.module_id+"_"+data["name"]];
        delete topWindow.objects.objects["ScanNetworkTable_"+this.module_id+"_"+data["old_name"]];
        var frame = $I(this.node.id+"_scanframe");
        var args = new Object;
        args["subnet"] = data["name"];
        args["window_id"] = this.win.id;
        args["mask"] = data["subnet_mask"];
        args["opener_item"] = this.opener_item.id;
        var frame_source = "index.php?object_id=ScanNetworkTable_"+this.module_id+"_Table&hook=show&arguments="+Object.toJSON(args);
        frame.src = frame_source;
    },
    
    onRemoveWindow: function (topWindow) {
        var data = this.getValues();
        delete topWindow.objects.objects[this.tabset_id];
        delete topWindow.objects.objects["ScanNetworkTable_"+this.module_id+"_"+data["old_name"]];
        delete topWindow.objects.objects["ScanNetworkTable_"+this.module_id+"_"+data["name"]];
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide6.3","HTMLBook_"+this.module_id+"_controller_6.3",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    },
    
	referenceButton_onClick: function(event) {
		var but = eventTarget(event);
		var elemid = but.getAttribute("fileid");
		var windowid = "Window_"+elemid.replace(/_/g,'');
		var args = new Object;		
		var data = this.getValues();
		args["title"]=data["title"];
		args["objectId"] = this.object_id;
		args["hook"] = "setParams";
		wm.show_window(windowid,elemid,args,this.object_id,but.id,null);
	}             
});