var FileSharesContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                getWindowManager().show_window("Window_FileShare"+this.opener_object.module_id.replace(/_/g,""),"FileShare_"+this.opener_object.module_id+"_",null,this.opener_object,this.opener_item.id);
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});