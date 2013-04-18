var ApplicationTabset = Class.create(Tabset, {

    activateTab: function($super,tab) {
        var app = $O("Application_","");
        if (typeof app != "undefined" && app!=null) {
        	var module_class = app.getModuleByTab(this.active_tab);     
        	$I(module_class+"_module_block").style.display = 'none';
        	var tree = $O("Tree_"+tab);
        	if (tree!=null) {
        		var root = tree.root_node;
        		if (root==null) {            
        			root = tree.initTree(object_id);
        			tree.fillTree();
        		}
        	}
        	$super(tab);
        	module_class = app.getModuleByTab(this.active_tab);
        	$I(module_class+"_module_block").style.display = '';
        } else
        	$super(tab);
    },
    
    onContextMenu: function(event,tab) {
        var elem = eventTarget(event);        
        event = event || window.event;
        event.cancelBubble = true;        
        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");
        var elem_end = elem.id.split("_").pop();
        if (elem_end != "Enterprise")
            $O(objectid,instanceid).show_context_menu("ApplicationContextMenu_ipaddress",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    }
});