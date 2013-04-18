var entity = $O(object_id,instance_id);
entity.win='{window_id}';
entity.module_id='{module_id}';
wm = globalTopWindow.getWindowManager();
if (wm!=null) {
    if (entity.win!="")
    {
        entity.win = wm.windows['{window_id}'];  
        if (entity.win!=null) {
            entity.readOnly = entity.win.readOnly;
            entity.node.setAttribute("keyup","keyUp");
            window.onkeyup = entity.addHandler;      
        }
    }
}
entity.opener_item = $I(getClientId('{opener_item}'));
entity.opener_object = $O('{opener_object}','');
if (entity.opener_object==0 || entity.opener_object==null) {
    if (entity.opener_item!=0) {
        opener_object = entity.opener_item.getAttribute("object");
        opener_instance = entity.opener_item.getAttribute("instance");
        entity.opener_object = $O(opener_object,opener_instance);
    }
}
entity.module_id = '{module_id}';
entity.skinPath = '{skinPath}';
entity.active_tab = '{active_tab}';
entity.classTitle = '{classTitle}';
entity.classListTitle = '{classListTitle}';
entity.parent_object_id = '{parent_object_id}';
entity.name = '{name}';
entity.className = '{clientClass}';
entity.instance = '{instance}';
entity.presentation = '{presentation}';
entity.tabsetName = '{tabsetName}';
entity.icon = '{icon}';
entity.isStatic = {staticStr};
entity.appUser = '{appUser}';
entity.user = '';
entity.role = '{roleStr}';
entity.profileClass = '{profileClass}';
entity.entityDataTableClass = '{entityDataTableClass}';
entity.createObjectsCount = '{createObjectsCount}';
entity.helpGuideId = '{helpGuideId}';
entity.helpButtonDisplay = '{helpButtonDisplay}';
entity.receiveBarCodes = '{receiveBarCodes}';
if (entity.role[0]=='{')
	entity.role = entity.role.evalJSON();
entity.fieldDefaults = '{fieldDefaultsStr}';
if (entity.fieldDefaults[0]=='{')
	entity.fieldDefaults = entity.fieldDefaults.evalJSON();
entity.fieldAccess = '{fieldAccessStr}';
if (entity.fieldAccess[0]=='{')
	entity.fieldAccess = entity.fieldAccess.evalJSON();
entity.links = '{linksStr}';
if (entity.links[0]=='{')
	entity.links = entity.links.evalJSON();
window.onmousedown = entity.onMouseDown;
window.onchange = entity.onChange;
if (entity.role["showGroupField"]=="false") {
	var groupRow = $I(entity.object_id+"_groupRow");
	if (groupRow!=0)
		groupRow.style.display = "none";
}
entity.onLoad();
entity.setReadOnly();
entity.buildControls();
tabset = $O('{tabset_id}');
if (tabset!=null && entity.active_tab!=null && entity.active_tab!="") 
	tabset.activateTab(entity.active_tab);