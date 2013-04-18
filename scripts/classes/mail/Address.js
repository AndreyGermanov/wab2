var Address = Class.create(Mailbox, {
    OK_onClick: function(event) {
        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true") {
            getWindowManager().remove_window(this.win.id);
            return 0;
        }

        // Проверяем корректность ввода данных
        var data = this.getValues();        

        if (data["name"]=="") {
            this.reportMessage('Укажите адрес',"error",true);
            return 0;
        }

        // Формируем параметры, передаваемые серверу для записи
        var cells=$O("Table_"+this.module_id+"_"+data["old_name"]).getCellsData();
        cells.shift();
        cells=cells.join("~");  
        var args = new Object;      
        args['name'] = data['name'];
        args['cells'] = cells;
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
            parameters: {ajax: true, object_id: "Address_"+mbox.module_id+"_"+data["old_name"],hook: '3',
						 arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response.length>1)
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
                    var old_id = "Address_"+mbox.module_id+"_"+getClientId(data["old_name"]);
                   
                    var new_id = "Address_"+mbox.module_id+"_"+getClientId(data["name"]);
                    var new_target_id = "Address_"+mbox.module_id+"_"+data["name"];
                    var owner_id = getClientId("AddressBook_"+mbox.module_id);

                    var params = new Array;
                    if (mbox.opener_item.parentNode !=null)
                        params["object_id"] = old_id;
                    params["new_object_id"] = new_id;
                    params["target_id"] = new_target_id;
                    params["text"] = data["name"];
                    params["old_text"] = data["old_name"];
                    params["parent"] = owner_id;
                    var icon = mbox.skinPath+"images/Tree/address.gif";
                    params["image"] = icon;
                    if (data["old_name"]=="" || getElementById(mbox.opener_object.node,"Tree_"+mbox.module_id+"_Mail_tree_Address_"+mbox.module_id+"_"+getClientId(data["old_name"]))==0) {
                        params["action"] = "add";
                    }
                    else {
                        params["action"] = "change";
                    }
                    mbox.raiseEvent("NODE_CHANGED",params,true);
                    mbox.win.node.setAttribute('changed',"false");
                    mbox.win.opener_item = mbox.opener_item.id;
                    if (new_id!=old_id) {
                        mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_id),getClientId(new_id));
                    }
                    wm.remove_window(mbox.win.id);
                }
            }
        });
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector9","HTMLBook_"+this.module_id+"_collector_9",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }
});