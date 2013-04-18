var DhcpServer = Class.create(Mailbox, {

            OK_onClick: function(event) {

        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true")
        {
            getWindowManager().remove_window(this.win.id);
            return 0;
        }

        // Проверяем корректность ввода данных
        var data = this.getValues();

        if (data["ldap_base"]=="") {
            this.reportMessage('Укажите имя домена !',"error",true);
            return 0;
        }

        if (data["dns_server"]=="") {
            this.reportMessage("Укажите DNS-сервер !","error",true);
            return 0;
        }
        
        if (!check_ip(data["dns_server"])) {
            this.reportMessage("DNS-сервер указан неверно !","error",true);
            return 0;
        }

        if (topWindow.online_network_monitor!=data["online_monitor"])
            topWindow.online_network_monitor_changed = true;
        topWindow.online_network_monitor = data["online_monitor"];

        var domain = data["ldap_base"];
        var ldap_base_arr = data["ldap_base"].split(".");
        var counter=0;
        for (counter=0;counter<ldap_base_arr.length;counter++) {
            ldap_base_arr[counter] = "dc="+ldap_base_arr[counter];
        }
        // Формируем параметры, передаваемые серверу для записи
        data["ldap_base"] = ldap_base_arr.join(",");
        var args = data.toObject();
        args["ldap_base"] = data["ldap_base"];
        
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
            parameters: {ajax: true, object_id: "DhcpServer_"+mbox.module_id+"_Networks",hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response!="" && response != parseInt(response)) {
                    response = response.evalJSON(true);
                    if (response["error"]!=null) {
                        mbox.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        mbox.reportMessage(response,"error",true);
                }
                else {
                    mbox.win.node.setAttribute('changed',"false");
                    wm.remove_window(mbox.win.id);                    
                    if (mbox.mailIntegration && mbox.mailDomain!=domain) {
                        var old_id = mbox.mailModuleId+"_"+getClientId(mbox.mailDomain);
                        var new_id = mbox.mailModuleId+"_"+getClientId(domain);
                        var new_target_id = mbox.mailModuleId+"_"+domain;
                        var owner_id = getClientId("Mailboxes_"+mbox.mailModuleId);
                        var params = new Array;
                        params["object_id"] = old_id+"_domain";
                        params["new_object_id"] = new_id+"_domain";
                        params["target_id"] = new_target_id+"_domain";
                        params["text"] = domain;
                        params["old_text"] = mbox.mailDomain;
                        params["parent"] = owner_id;
                        var icon = mbox.skinPath+"images/Window/mail-domain.gif";
                        params["image"] = icon;

                        if (mbox.new_maildomain)
                            params["action"] = "add";
                        else
                            params["action"] = "change";
                        mbox.raiseEvent("NODE_CHANGED",params,true); 
                        var el = $I("Tree_"+mbox.mailModuleId+"_Mail_tree_"+mbox.mailModuleId+"_"+getClientId(domain)+"_domain");
                        if (el!=0) {
                            var elems = el.getElementsByTagName("*");
                            var c2=0;
                            for (c2=0;c2<elems.length;c2++) {
                                var elem = elems[c2];
                                elem.id.replace("_"+getClientId(mbox.mailDomain),"_"+getClientId(domain));
                                elem.setAttribute("target_object",elem.getAttribute("target_object").replace("_"+mbox.mailDomain,"_"+domain));
                            }                        
                        }
                    }
                    clearInterval(topWindow.monitorTimer);
                    topWindow.monitorTimer = setInterval('updateHostsInfo()',data["online_monitor_update_period"]);
                }
            }
        });
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide6.2","HTMLBook_"+this.module_id+"_controller_6.2",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    },
    
    TAB_CHANGED_processEvent: function(params) {
    	if (params["tabset_id"] == this.tabset_id) {
            var text_field = this.node.id+"_manualDNSEntries_value";
            if (params["tab"]!="main") {
                editAreaLoader.toggle(text_field,"off");
                editAreaLoader.toggle(text_field,"on");
            }    		
    	}
    }
});