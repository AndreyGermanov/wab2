var WebSitesContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var params = new Array;
                getWindowManager().show_window("Window_WebSite","WebSite_"+this.opener_object.module_id+"_",params,this.opener_object,this.opener_item.id);
                break;
        }
        topWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});