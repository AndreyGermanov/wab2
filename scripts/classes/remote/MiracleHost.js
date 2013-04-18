var MiracleHost = Class.create(Mailbox, {

    serverSettingsButton_onClick: function(event) {
        var serverSettingsRow = $I(this.node.id+"_serverSettingsRow");
        if (serverSettingsRow.style.display == "none")
            serverSettingsRow.style.display = "";
        else
            serverSettingsRow.style.display = "none";
    },

    virtualServerSettingsButton_click: function(serverName) {
        var serverSettingsRow = $I(this.node.id+"_"+serverName+"SettingsRow");
        if (serverSettingsRow.style.display == "none")
            serverSettingsRow.style.display = "";
        else
            serverSettingsRow.style.display = "none";
    },

    virtualServerSaveButton_click: function(serverName) {
        var data = this.getValues();

        if (check_ip(data[serverName+"IP"])==false) {
            this.reportMessage('IP-адрес сервера '+this.virtual_servers[serverName]['title']+' указан неверно !',"error",true);
            return 0;
        }

        if (check_port(data[serverName+"RDesktopPort"])==false) {
            this.reportMessage('Порт для подключения к монитору сервера '+this.virtual_servers[serverName]['title']+' указан неверно !',"error",true);
            return 0;
        }

        if (trim(data[serverName+"ControlPanelURL"])=="") {
            this.reportMessage('Не указан путь к панели управления сервером '+this.virtual_servers[serverName]['title']+' !',"error",true);
            return 0;
        }

        this.virtual_servers[serverName]['ip_address'] = data[serverName+"IP"];
        this.virtual_servers[serverName]['rdesktop_port'] = data[serverName+"RDesktopPort"];
        this.virtual_servers[serverName]['controlpanel_url'] = data[serverName+"ControlPanelURL"];
        var virtual_servers = new Object;
        virtual_servers[serverName] = new Object;
        virtual_servers[serverName]['ip_address'] = data[serverName+"IP"];
        virtual_servers[serverName]['rdesktop_port'] = data[serverName+"RDesktopPort"];
        virtual_servers[serverName]['controlpanel_url'] = data[serverName+"ControlPanelURL"];
        var args = new Object;
        args["virtual_servers"] = virtual_servers;
        var obj=this;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MiracleHost_MiracleHost_BootUp",hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                if (response!="")
                    obj.reportMessage(response,"error",true);
                else
                    obj.reportMessage('Данные успешно сохранены.',"info",false);
            }
        });
    },

    virtualServerDisplayButton_click: function(serverName) {
    	var args = new Object;
    	args["serverName"] = serverName;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MiracleHost_MiracleHost_BootUp",hook: '4', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                location.href = response;
            }
        });
    },

    serverSettingsSaveButton_onClick: function(event) {
        var data = this.getValues();

        if (check_ip(data["managementModuleIP"])==false) {
            this.reportMessage('IP-адрес модуля управления указан неверно !',"error",true);
            return 0;
        }

        if (check_ip(data["managementModuleNetmask"])==false) {
        	this.reportMessage('Сетевая маска модуля управления указана неверно !',"error",true);
            return 0;
        }

        if (check_ip(data["managementModuleGateway"])==false) {
        	this.reportMessage('Шлюз по умолчанию модуля управления указан неверно !',"error",true);
            return 0;
        }

        var dnsses = data["managementModuleDNSServers"].split(" ");
        for (var c=0;c<dnsses.length;c++) {
            if (check_ip(dnsses[c])==false) {
            	this.reportMessage("DNS-серверы модуля управления указаны неверно !","error",true);
                return 0;
            }
        }

        if (check_ip(data["serverIP"])==false) {
        	this.reportMessage('IP-адрес для подключения к серверу указан неверно !',"error",true);
            return 0;
        }

        if (check_mac(data["serverMAC"])==false) {
        	this.reportMessage('MAC-адрес сервера указан неверно !',"error",true);
            return 0;
        }

        if (trim(data["rdesktopCommand"])=="") {
        	this.reportMessage('Не указана командая для подключения к экрану сервера !',"error",true);
            return 0;
        }

        if (trim(data["rootPassword"])=="") {
        	this.reportMessage('Не указан пароль пользователя root !',"error",true);
            return 0;
        }
        var args =  data.toObject();
        var obj=this;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MiracleHost_MiracleHost_BootUp",hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                if (response!="")
                    obj.reportMessage(response,"error",true);
                else
                    obj.reportMessage('Данные успешно сохранены.',"info",false);
            }
        });
    },

    virtualServerPowerOnButton_click: function(serverName,operation) {
        var args = new Object;
        args["serverName"] = serverName;
        args["operation"] = operation;
        if (serverName=='rootHost' && operation=="off") {
            args["shutdown"] = "true";
            this.shutting_down = true;
        }
        else {
            args["shutdown"] = "false";
        }
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MiracleHost_MiracleHost_BootUp",hook: '5', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
            }
        });
    },

    applyStatus: function(mhost) {
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MiracleHost_MiracleHost_BootUp",hook: '6'},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                var resp_array = response.split("|");
                mhost.status = resp_array.shift();
                var server_power_button = $I(mhost.node.id+"_serverPowerButton");
                var server_power_label = $I(mhost.node.id+"_serverPowerLabel");
                if (mhost.status=="on") {
                    server_power_button.src = mhost.skinPath+"images/MiracleHost/poweron.png";
                    server_power_button.title = "Выключить";
                    server_power_label.innerHTML = "Сервер (питание включено)";
                } else {
                    server_power_button.src = mhost.skinPath+"images/MiracleHost/poweroff.png";
                    server_power_button.title = "Включить";
                    server_power_label.innerHTML = "Сервер (питание выключено)";
                }
            }
        });
    },  

    showHosts: function() {
        var tbl = $I(this.node.id+"_mhost_table");
        var c=null;
        for (c in this.virtual_servers) {
            if (typeof(this.virtual_servers[c])!="object")
                continue;
            var tr = document.createElement("tr");
            var td = document.createElement("td");
            td.setAttribute("nowrap","");
            var span = document.createElement("span");
            span.id = this.node.id+"_"+c+"Label";
            span.setAttribute("class","serverOff");
            span.innerHTML = this.virtual_servers[c]["title"]+" (не загружен)";
            td.appendChild(span);
            tr.appendChild(td);

            td = document.createElement("td");
            td.setAttribute("nowrap","");
            var img = document.createElement("img");
            img.id = this.node.id+"_"+c+"PowerOnButton";
            img.setAttribute("align", "middle");
            img.setAttribute("width",30);
            img.src = this.skinPath+"images/MiracleHost/poweroff.png";
            img.setAttribute("title","Включить сервер");
            img.setAttribute("onclick","$O('"+mhost.object_id+"','').virtualServerPowerOnButton_click('"+c+"','on')");
            td.appendChild(img);
            tr.appendChild(td);

            td = document.createElement("td");
            td.setAttribute("nowrap","");
            img = document.createElement("img");
            img.id = this.node.id+"_"+c+"RemoteDesktopButton";
            img.setAttribute("align", "middle");
            img.setAttribute("width",30);
            img.src = this.skinPath+"images/spacer.gif";
            img.setAttribute("title","Подключиться к монитору сервера");
            img.setAttribute("onclick","$O('"+mhost.object_id+"','').virtualServerDisplayButton_click('"+c+"')");
            td.appendChild(img);
            tr.appendChild(td);
            td = document.createElement("td");
            td.setAttribute("nowrap","");
            img = document.createElement("img");
            img.id = this.node.id+"_"+c+"ControlPanelButton";
            img.setAttribute("align", "middle");
            img.setAttribute("width",30);
            img.setAttribute("title","Подключиться к панели управления сервером");
            img.src = this.skinPath+"images/spacer.gif";
            img.setAttribute("onclick","window.open('"+this.virtual_servers[c]["controlpanel_url"]+"')");
            td.appendChild(img);
            tr.appendChild(td);
            td = document.createElement("td");
            td.setAttribute("nowrap","");
            img = document.createElement("img");
            img.id = this.node.id+"_"+c+"SettingsButton";
            img.setAttribute("align", "middle");
            img.setAttribute("width",30);
            img.setAttribute("title","Параметры подключения к серверу");
            img.setAttribute("onclick","$O('"+mhost.object_id+"','').virtualServerSettingsButton_click('"+c+"')");
            img.src = this.skinPath+"images/MiracleHost/settings.png";
            td.appendChild(img);
            tr.appendChild(td);
            tbl.appendChild(tr);
            tr = document.createElement("tr");
            tr.id = this.node.id+"_"+c+"SettingsRow";
            tr.style.display = "none";
            td = document.createElement("td");
            td.setAttribute("colspan","6");
            var innertbl = document.createElement("table");
            innertbl.setAttribute("width","100%");
            innertbl.setAttribute("class","inner");
            var innertr = document.createElement("tr");
            innertr.setAttribute("class","inner");
            var innertd = document.createElement("td");
            innertd.setAttribute("nowrap","");
            innertd.innerHTML = "IP-адрес сервера:";
            innertr.appendChild(innertd);
            innertd = document.createElement("td");
            innertd.setAttribute("width","100%");
            var input = document.createElement("input");
            input.setAttribute("class","wide");
            input.setAttribute("id",this.node.id+"_"+c+"IP");
            input.setAttribute("value",this.virtual_servers[c]["ip_address"]);
            innertd.appendChild(input);
            innertr.appendChild(innertd);
            innertbl.appendChild(innertr);

            innertr = document.createElement("tr");
            innertr.setAttribute("class","inner");
            innertd = document.createElement("td");
            innertd.setAttribute("nowrap","");
            innertd.innerHTML = "Порт для подключения к монитору сервера:";
            innertr.appendChild(innertd);
            innertd = document.createElement("td");
            innertd.setAttribute("width","100%");
            input = document.createElement("input");
            input.setAttribute("class","wide");
            input.setAttribute("id",this.node.id+"_"+c+"RDesktopPort");
            input.setAttribute("value",this.virtual_servers[c]["rdesktop_port"]);
            innertd.appendChild(input);
            innertr.appendChild(innertd);
            innertbl.appendChild(innertr);

            innertr = document.createElement("tr");
            innertr.setAttribute("class","inner");
            innertd = document.createElement("td");
            innertd.setAttribute("nowrap","");
            innertd.innerHTML = "Путь к панели управления сервером:";
            innertr.appendChild(innertd);
            innertd = document.createElement("td");
            innertd.setAttribute("width","100%");
            input = document.createElement("input");
            input.setAttribute("class","wide");
            input.setAttribute("id",this.node.id+"_"+c+"ControlPanelURL");
            input.setAttribute("value",this.virtual_servers[c]["controlpanel_url"]);
            innertd.appendChild(input);
            innertr.appendChild(innertd);
            innertbl.appendChild(innertr);
            innertr = document.createElement("tr");
            innertd = document.createElement("td");
            innertd.setAttribute("class","category");
            innertd.setAttribute("colspan",2);
            var div = document.createElement("div");
            div.setAttribute("align","right");
            var button = document.createElement("input");
            button.setAttribute("type","button");
            button.setAttribute('value',"Сохранить");
            button.setAttribute('onclick',"$O('"+mhost.object_id+"','').virtualServerSaveButton_click('"+c+"')");
            div.appendChild(button);
            innertd.appendChild(div);
            innertr.appendChild(innertd);
            innertbl.appendChild(innertr);
            td.appendChild(innertbl);
            tr.appendChild(td);
            tbl.appendChild(tr);
        }
    }
});