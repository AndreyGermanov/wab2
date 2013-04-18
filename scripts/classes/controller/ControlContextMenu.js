var ControlContextMenu = Class.create(ContextMenu, {
    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        var menu = this;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "console":
                var host = this.opener_item.getAttribute("target_object");
                var hostname = host.split("_").pop();
                var elem_id = "RemoteConsoleWindow_"+module_id+"_"+host;
                var elemid = "Window_"+getClientId(elem_id).replace(/_/g,"");
                var args = new Object;
                args["object_text"] = "Консоль "+hostname;
                args["hook"] = "3";
                getWindowManager().show_window(elemid,elem_id,args,menu.opener_object.id,menu.opener_item.id);
                break;
            case "desktop":
                var host = this.opener_item.getAttribute("target_object");
                var hostname = host.split("_").pop();
                var elem_id = "RemoteDesktopWindow_"+module_id+"_"+host;
                var elemid = "Window_"+getClientId(elem_id).replace(/_/g,"");
                var args = new Object;
                args["object_text"] = 'Рабочий стол '+hostname;
                args['hook'] = '3';
                getWindowManager().show_window(elemid,elem_id,args,menu.opener_object.id,menu.opener_item.id);
                break;
            case "web":
                var host = this.opener_item.getAttribute("target_object");
                var hostname = host.split("_").pop();
                new Ajax.Request("index.php",{
                    method:"post",
                    parameters: {ajax:true,object_id:host,hook:'3'},
                    onSuccess:function(transport) {
                        var response = trim(transport.responseText.replace("\n",""));
                        var arr = response.split(" ");
                        var protocol = arr[0];
                        var port = arr[1];
                        var address = arr[2];
                        if (arr[3]!=null)
                            var new_win = arr[3];
                        else
                            new_win = "0";
                        if (new_win!="1") {
                            var params = new Object;
                            var elem_id = "FrameWindow_"+menu.module_id+"_"+host;
                            var elemid = "Window_"+getClientId(elem_id).replace(/_/g,"");
                            params['hook'] = '3';
                            params['hostname'] = hostname;
                            params['port'] = port;
                            params['address'] = adress;
                            params['protocol'] = protocol;
                            getWindowManager().show_window(elemid,elem_id,params,menu.opener_object.id,menu.opener_item.id);                            
                        } else {
                            var params = new Array;
                            var elem_id = "FrameWindow_"+menu.module_id+"_"+host;
                            params[0] = "$object->init_string=$object->url=\""+protocol+"://"+address+":"+port+"\";";
                            window.open(protocol+"://"+address+":"+port);
                        }
                    }
                });                
                break;
        }
        topWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }            
});