tabset = $O(object_id,instance_id);

tabset.win='{window_id}'
wm = getWindowManager();
if (wm !=null && tabset.win!="")
{
    tabset.win = wm.windows['{window_id}'];
}

tabset.opener_item = $I(getClientId('{opener_item}'));
if (tabset.opener_item!=0) {
    opener_object = tabset.opener_item.getAttribute("object");
    opener_instance = tabset.opener_item.getAttribute("instance");
    tabset.opener_object = $O(opener_object,opener_instance);
}

window.onmousedown = tabset.onMouseDown;
window.onchange = tabset.onChange;

tabset.type = '{type}';
tabset.item = '{item}';
tabset.tabs_string = '{tabs_string}';
tabset.active_tab = '{active_tab}';
tabset.parent_object_id = '{parent_object_id}';
if (tabset.parent_object_id=='')
    tabset.parent_object_id = tabset.item;
tabset.skinPath = '{skinPath}';
tabset.initTabset();