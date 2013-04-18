mapp = $O(object_id,instance_id);
mapp.win='{window_id}';
mapp.module_id='{module_id}';
wm = getWindowManager();
if (mapp.win!="")
{
    mapp.win = wm.windows['{window_id}'];
}
mapp.opener_item = $I(getClientId('{opener_item}'));
if (mapp.opener_item!=0) {
    opener_object = mapp.opener_item.getAttribute("object");
    opener_instance = mapp.opener_item.getAttribute("instance");
    mapp.opener_object = $O(opener_object,opener_instance);
}
mapp.parent_object_id = '{parent_object_id}';
window.onmousedown = mapp.onMouseDown;
window.onchange = mapp.onChange;