var CreateObjectContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var docClass = get_elem_id(elem).replace("_text","");
        var opObject =  docClass+"_"+this.module_id+"_";
        var params = new Object;
        params["hook"] = "afterInit";
        var object_id = "";
        if (this.opener_object_id!="")
        	object_id = this.opener_object_id;
        else
        	object_id = this.opener_object.object_id;
        params["ownerObject"] = object_id;
		getWindowManager().show_window("Window_"+opObject.replace(/_/g,""),opObject,params,null,null,null);
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});