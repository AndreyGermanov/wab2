var PrintContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var formName = get_elem_id(elem).replace("_text","");
        var opObject = "PrintWindow_"+this.module_id+"_"+this.opener_object.object_id+"_"+formName.replace(/ /g,"");
        var params = new Array;
		getWindowManager().show_window("Window_"+opObject.replace(/_/g,""),opObject,params,this.opener_object.object_id,this.opener_item.id,true);
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});