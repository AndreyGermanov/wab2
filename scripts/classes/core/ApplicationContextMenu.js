var ApplicationContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "setip":
                var moduleName = this.opener_item.id.split("_").pop();
                var args = new Object;
                args["moduleName"] = moduleName;
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'Application',
                                     hook: '3', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace("\n","");
                            var old_ip = response;
                            var new_ip = prompt("Введите IP-адрес модуля",old_ip);
                            if (new_ip != null && new_ip != old_ip && new_ip != "" && check_ip(new_ip)) {
                                var params = new Object;
                                params["moduleName"] = moduleName;
                                params["ip"] = new_ip;
                                new Ajax.Request("index.php",
                                    {
                                        method: "post",
                                        parameters: {ajax: true, object_id: 'Application', hook: '10', arguments: Object.toJSON(params)},
                                        onSuccess: function(transport) {
                                            var response = transport.responseText.toString().replace("\n","");
                                            if (response!="")
                                            	alert(response);
                                        }
                                    });                                            
                            }                                
                        }
                    });                
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});