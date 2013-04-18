var MetadataRootContextMenu = Class.create(ContextMenu, {
    onClick: function(event) {
    	var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var params = new Object;
            	params["fullGroup"] = this.target;
            	params["hook"] = "setParams";
            	var target = this.metadataClass+"_"+this.opener_object.module_id+"_"+(Math.round(Math.random() * 50000))+"_";
            	var win = "Window_"+target.replace(/_/g,"");
                getWindowManager().show_window(win,target,params,this.opener_object,this.opener_item.id);
            	break;     
            case "addGroup":
                var params = new Object;
            	params["fullGroup"] = this.target;
            	params["hook"] = "setParams";
            	var target = this.metadataGroupClass+"_"+this.opener_object.module_id+"_"+(Math.round(Math.random() * 50000))+"_";
            	var win = "Window_"+target.replace(/_/g,"");
                getWindowManager().show_window(win,target,params,this.opener_object,this.opener_item.id);
            	break;     
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});