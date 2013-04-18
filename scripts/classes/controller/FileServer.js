var FileServer = Class.create(Mailbox, {

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

        if (data["workgroup"]=="") {
            this.reportMessage('Укажите рабочую группу Windows !',"error",true);
            return 0;
        }

        var args = data.toObject();
        
        // Формируем параметры, передаваемые серверу для записи
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
            parameters: {ajax: true, object_id: "FileServer_"+mbox.module_id+"_Shares",hook: '3',arguments: Object.toJSON(args)},
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
                    if (mbox.mailIntegration && mbox.mailDomain!=domain) {
                        var old_id = mbox.mailModuleId+"_"+getClientId(mbox.mailDomain);
                        var new_id = mbox.mailModuleId+"_"+getClientId(domain);
                        var old_target_id = mbox.mailModuleId+"_"+mbox.mailDomain;
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
                }
            }
        });
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide5.2","HTMLBook_"+this.module_id+"_controller_5.2",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }
});