var ObjectGroupsContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var params = new Array;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                getWindowManager().show_window("Window_ObjectGroup"+this.opener_object.module_id.replace(/_/g,""),"ObjectGroup_"+this.opener_object.module_id+"_",params,this.opener_object,this.opener_item.id);
                break;
            case "properties":
                getWindowManager().show_window("Window_ObjectGroupProperties"+this.opener_object.module_id.replace(/_/g,""),"ObjectGroupProperties_"+this.opener_object.module_id+"_Props",params,this.opener_object,this.opener_item.id);
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});