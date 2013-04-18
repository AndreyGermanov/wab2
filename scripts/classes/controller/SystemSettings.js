var SystemSettings = Class.create(Mailbox, {

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

        if (data["hostname"]=="") {
            this.reportMessage('Укажите имя сервера !',"error",true);
            return 0;
        }

        if (data["password1"]=="" && data["password2"]=="") {
            this.reportMessage('Укажите пароль пользователя root !',"error",true);
            return 0;
        }

        if (data["password1"] != data["password2"]) {
            this.reportMessage("Пароли не совпадают !","error",true);
            return 0;
        }

        if (data["ipaddr"]=="") {
            this.reportMessage('Укажите IP-адрес !',"error",true);
            return 0;
        }

        if (!check_ip(data["ipaddr"])) {
            this.reportMessage("IP-адрес указан неверно !","error",true);
            return 0;
        }

        if (data["netmask"]=="") {
            this.reportMessage('Укажите маску сети !',"error",true);
            return 0;
        }

        if (!check_ip(data["netmask"])) {
            this.reportMessage("Маска сети указана неверно !","error",true);
            return 0;
        }

        if (data["gateway"]!="" && !check_ip(data["gateway"])) {
            this.reportMessage("Шлюз по умолчанию указан неверно !","error",true);
            return 0;
        }

        if (data["dns1"]!="" && !check_ip(data["dns1"])) {
            this.reportMessage("Адрес сервера DNS указан неверно !","error",true);
            return 0;
        }

        if (data["dns2"]!="" && !check_ip(data["dns2"])) {
            this.reportMessage("Адрес сервера DNS указан неверно !","error",true);
            return 0;
        }

        if (data["dns3"]!="" && !check_ip(data["dns3"])) {
            this.reportMessage("Адрес сервера DNS указан неверно !","error",true);
            return 0;
        }

        if (data["ldap_host"]=="") {
            this.reportMessage('Укажите имя сервера БД !',"error",true);
            return 0;
        }
        
        if (data["ldap_port"]=="") {
            this.reportMessage('Укажите протокол сервера БД !',"error",true);
            return 0;
        }
        
        if (data["ldap_user"]=="") {
            this.reportMessage('Укажите имя администратора БД !',"error",true);
            return 0;
        }
        
        if (data["ldap_password"]=="") {
            this.reportMessage('Укажите пароль администратора БД !',"error",true);
            return 0;
        }
                
        // Формируем параметры, передаваемые серверу для записи
        var args = data.toObject();        
        args["password"] = data["password1"];
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
            parameters: {ajax: true, object_id: "SystemSettings_"+mbox.module_id+"_Settings",hook: '3', arguments: Object.toJSON(args)},
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
                    mbox.win.node.setAttribute('changed',"false");
                    wm.remove_window(mbox.win.id);
                }
            }
        });
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide4.1","HTMLBook_"+this.module_id+"_controller_4.1",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    },
    
    CONTROL_VALUE_CHANGED_processEvent: function(params) {
    	if (params["object_id"] == this.node.id+"_is_ldap_localhost") {
    		if (params["value"]=="0") 
    			$I(this.node.id+"_ldapHostRow").style.display = '';
    		else {
    			$I(this.node.id+"_ldapHostRow").style.display = 'none';
    			$I(this.node.id+"_ldap_host").value = "localhost"; 
    		}
    	}
    }
});