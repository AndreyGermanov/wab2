var UsersContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var params = new Array;
                getWindowManager().show_window("Window_Users"+this.opener_object.module_id.replace(/_/g,""),"User_"+this.opener_object.module_id+"_",params,this.opener_object,this.opener_item.id);
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});