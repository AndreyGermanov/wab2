wm = getWindowManager();
wnd = $O('{object_id}','');
if (wnd.id!="ApplicationNew" && wnd.id!="TaskbarMain" && wnd.id!="InfoPanelNew") {
	left = parseInt('{left}');
	if (left<=0 || isNaN(left))		
		if ($O("ApplicationNew","")!=null)
			left = Math.round(Math.random() * (window.innerWidth-parseInt('{width}')-parseInt($O("ApplicationNew","").node.style.width)) + parseInt($O("ApplicationNew","").node.style.width));
		else
			left = Math.round(Math.random()* (window.innerWidth-parseInt('{width}')));    
    wnd.left = left+"px";
    topy = parseInt('{top}');
    if (topy<=0 || isNaN(topy))
    	if ($O(wm.mainMenuName,"")!=null) { 
    		var height = getElementPosition($O(wm.mainMenuName,"").node.id).height;
    		topy = Math.round(Math.random() * (window.innerHeight-parseInt('{height}')-60));
    		if (topy<height)
    			topy=height;
    	}
    	else
    		topy = Math.round(Math.random() * (window.innerHeight-parseInt('{height}')-60));
    wnd.topy = topy+"px";
} else {
    wnd.left = '{left}';
    wnd.topy = '{top}';
}
wnd.width = '{width}';
wnd.height = '{height}';
wnd.min_left = '{min_left}';
wnd.min_top = '{min_top}';
wnd.max_right = '{max_right}';
wnd.max_bottom = '{max_bottom}';
wnd.moveable = '{moveable}';
wnd.resizeable = '{resizeable}';
wnd.resizeable_left = '{resizeable_left}';
wnd.resizeable_right = '{resizeable_right}';
wnd.resizeable_top = '{resizeable_top}';
wnd.resizeable_bottom = '{resizeable_bottom}';
wnd.dock_left = '{dock_left}';
wnd.dock_right = '{dock_right}';
wnd.dock_top = '{dock_top}';
wnd.dock_bottom = '{dock_bottom}';
wnd.has_close = '{has_close}';
wnd.has_minimize = '{has_minimize}';
wnd.has_maximize = '{has_maximize}';
wnd.has_border = '{has_border}';
wnd.php_object_id = '{php_object_id}';
wnd.icon = '{icon}';
wnd.object_text = '{object_text}';
wnd.objectHook = '{objectHook}';
wnd.objectArguments = new Object;
wnd.objectArguments['window_id'] = '{object_id}';
wnd.objectArguments['opener_object'] = '{opener_object}';
wnd.objectArguments['opener_item'] = '{opener_item}';
wnd.objectArguments['opener_instance'] = '{opener_instance}';
wnd.objectArguments['readOnly'] = '{readOnly}';
wnd.objectArguments['arguments'] = '{objectArguments}';
wnd.skinPath = '{skinPath}';
wnd.opener_object = '{opener_object}';
wnd.opener_item = '{opener_item}';
wnd.parent_object_id = '{parent_object_id}';
if (wnd.opener_object=="" || wnd.opener_object==null)
    if (wnd.opener_item!="" && wnd.opener_item!='undefined' && wnd.opener_item!=null && wnd.opener_item!="") {
		if ($I(wnd.opener_item)!=null && $I(wnd.opener_item)!="")
			wnd.opener_object = $I(wnd.opener_item).getAttribute("object");
	}
if (wnd.parent_object_id=='') {    
    if (wnd.opener_item!="" && wnd.opener_item!='undefined' && wnd.opener_item!=null) {
        if ($I(wnd.opener_item)!=0)
            wnd.parent_object_id = $I(wnd.opener_item).getAttribute("object");     
    } else if (wnd.opener_object!="" && wnd.opener_object !=null) {
        wnd.parent_object_id = wnd.opener_object;
    }
}
wnd.readOnly = '{readOnly}';
wnd.ignoreChanging = {ignoreChangingStr};
wnd.add();