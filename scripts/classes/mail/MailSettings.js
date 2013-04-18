var MailSettings = Class.create(Entity, {

    networkSettingsOK_onClick: function(event,close) {
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed !="true" && close==null)
            getWindowManager().remove_window(this.win.id,this.instance_id);
        var data = this.getValues();
        
        if (data["ipaddr"]=="") {
            this.reportMessage('Не указан IP-адрес',"error",true);
            return 0;
        }

        if (data["netmask"]=="") {
            this.reportMessage("Не указана маска сети","error",true);
            return 0;
        }
        if (trim(data["dns1"])=="" && trim(data["dns2"])!="") {
            data["dns1"] = trim(data["dns2"]);
            data["dns2"] = "";
        }

        if (trim(data["dns1"])=="" && trim(data["dns3"])!="") {
            data["dns1"] = trim(data["dns3"]);
            data["dns3"] = "";
        }

        if (trim(data["dns2"])=="" && trim(data["dns3"])!="") {
            data["dns2"]=trim(data["dns3"]);
            data["dns3"] = "";
        }

        data["dns1"] = trim(data["dns1"]);
        data["dns2"] = trim(data["dns2"]);
        data["dns3"] = trim(data["dns3"]);
        var dns = new Array;
        dns[0] = data["dns1"];
        dns[1] = data["dns2"];
        dns[2] = data["dns3"];

        if (trim(data["dns1"])!="" && trim(data["dns2"])!="")
            if (data["dns1"]==data["dns2"] || data["dns1"]==data["dns3"] || data["dns2"]==data["dns3"]) {
                this.reportMessage('Указаны одинаковые серверы DNS',"error",true);
                return 0;
        }

        if (trim(data["password"])!=trim(data["password1"])) {
            this.reportMessage("Пароли не совпадают !","error",true);
            return 0;
        }

        if (!$(this.node.id+"_bootproto").checked)
            data["bootproto"] = "static";
        else
            data["bootproto"] = "dhcp";
        var result_dns = new Array;
        for (var c=0;c<dns.length;c++) {
            if (dns[c]!="") {
                result_dns[result_dns.length] = dns[c];
            }
        }
        result_dns = "dns-nameservers "+result_dns.join(" ");
        var params = data.toObject();
        params["dns"] = result_dns;
        var app = this;
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);

        this.node.appendChild(loading_img);
        new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: "MailSettings_"+this.module_id+"_Settings",
                             hook: '3', arguments: Object.toJSON(params)},
                onSuccess: function(transport) {
                    var response = trim(transport.responseText.replace("\n",""));
                    app.node.removeChild(loading_img);
                    if (response.length>1)
                        app.reportMessage(response,"error",true);
                    else {
                        app.win.node.setAttribute('changed','false');
                        if (close==null)
                            getWindowManager().remove_window(app.win.id,app.instance_id);
                    }
                }
            });
    },

    bootproto_onChange: function(event) {
        var node_id = this.node.id;
        if (this.instance_id=="")
            return 0;
        if ($(node_id+"_bootproto").checked) {
            $I(node_id+"_ipaddr").disabled = true;
            $I(node_id+"_netmask").disabled = true;
            $I(node_id+"_gateway").disabled = true;
            $I(node_id+"_dns1").disabled = true;
            $I(node_id+"_dns2").disabled = true;
            $I(node_id+"_dns3").disabled = true;
        }
        else {
            $I(node_id+"_ipaddr").disabled = false;
            $I(node_id+"_netmask").disabled = false;
            $I(node_id+"_gateway").disabled = false;
            $I(node_id+"_dns1").disabled = false;
            $I(node_id+"_dns2").disabled = false;
            $I(node_id+"_dns3").disabled = false;
        }
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector4","HTMLBook_"+this.module_id+"_collector_4",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }   
});