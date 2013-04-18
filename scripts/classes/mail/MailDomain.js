var MailDomain = Class.create(Mailbox, {
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

        if (data["name"]=="") {
            this.reportMessage('Укажите название почтового домена!',"error",true);
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
            parameters: {ajax: true, object_id: "MailDomain_"+this.module_id+"_"+data["old_name"],hook: '3', arguments: Object.toJSON(args)},
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
                    var old_id = mbox.module_id+"_"+getClientId(data["old_name"]);
                    var new_id = mbox.module_id+"_"+getClientId(data["name"]);
                    var new_target_id = mbox.module_id+"_"+data["name"];
                    var owner_id = getClientId("Mailboxes_"+mbox.module_id);
                    var params = new Array;
                    //if (mbox.opener_item.parentNode !=null)
                        params["object_id"] = old_id+"_domain";
                    params["new_object_id"] = new_id+"_domain";
                    params["target_id"] = new_target_id+"_domain";
                    params["text"] = data["name"];
                    params["old_text"] = data["old_name"];
                    params["parent"] = owner_id;
                    var icon = mbox.skinPath+"images/Window/mail-domain.gif";
                    params["image"] = icon;

                    if (data["old_name"]=="")
                        params["action"] = "add";
                    else
                        params["action"] = "change";
                    mbox.raiseEvent("NODE_CHANGED",params,true);                    
                    mbox.win.node.setAttribute('changed',"false");
                    mbox.win.opener_item = mbox.opener_item.id;
                    var elems = mbox.opener_item.parentNode.getElementsByTagName("*");
                    for (var c2=0;c2<elems.length;c2++) {
                        var elem = elems[c2];
                        elem.id.replace("_"+getClientId(data["old_name"]),"_"+getClientId(data["name"]));
                        elem.setAttribute("target_object",elem.getAttribute("target_object").replace("_"+data["old_name"],"_"+data["name"]));
                    }
                    if (new_id!=old_id) {
                        mbox.win.opener_item = mbox.win.opener_item.replace(getClientId(old_id),getClientId(new_id));
                    }
                    wm.remove_window(mbox.win.object_id);
                }
            }
        });
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector5","HTMLBook_"+this.module_id+"_collector_5",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }
});