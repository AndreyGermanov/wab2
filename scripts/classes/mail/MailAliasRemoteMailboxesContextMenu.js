var MailAliasRemoteMailboxesContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var mboxid = this.opener_item.getAttribute("target_object");
        var mbox_arr = mboxid.split("_");
        mbox_arr.shift();
        var module_id = mbox_arr.shift()+"_"+mbox_arr.shift();
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add_remote_mailbox":
                var owner = this.opener_item.getAttribute("target_object").replace("MailAlias_","").replace("_RemoteMailboxes","").replace(module_id+"_","");
                owner = owner.split("_");
                var owner_end = owner.pop();
                owner = owner.join("_")+"@"+owner_end;
                var params = new Object;
                params["hook"] = "setParams";
                params["owner"] = owner;
                getWindowManager().show_window("Window_RemoteMailbox"+module_id.replace(/_/g,""),"RemoteMailbox_"+module_id+"_",params,this.opener_object,this.opener_item.id);
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});