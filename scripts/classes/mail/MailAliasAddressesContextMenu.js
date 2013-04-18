var MailAliasAddressesContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var owner = this.opener_item.getAttribute("target_object").replace("MailAlias_","").replace("_Addresses","").replace(this.opener_object.module_id+"_","");
                var owner_id = owner;
                owner = owner.split("_");
                params = new Object;
                params["hook"] = "setParams";
                params["address"] = "";
                params["name"] = owner[0];
                params["domain"] = owner[1];
                params["instance"] = "Address";
                params["template"] = "templates/mail/MailAliasAddress.html";
                params["object_text"] = "";
                params["icon"] = 'images/Tree/mailbox_alias.gif';
                getWindowManager().show_window("Window_MailAlias"+this.opener_object.module_id.replace(/_/g,"")+getClientId(owner_id).replace(/_/g,"")+"Address","MailAlias_"+this.opener_object.module_id+"_"+owner_id,params,this.opener_object,this.opener_item.id);
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});