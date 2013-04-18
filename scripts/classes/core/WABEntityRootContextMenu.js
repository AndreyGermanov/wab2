var WABEntityRootContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        var tree = $O(this.opener_item.getAttribute("object"),"");
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
            	var item = null;
                if (this.module_id=="") {
                    item = tree.className+"_"+tree.module_id+"_";
                } else
                    item = tree.className+"_";
                getWindowManager().show_window("Window_Window"+item.replace(/_/g,''),item,null,null,null);
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
        return false;
    }
});