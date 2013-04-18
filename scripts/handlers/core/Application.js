app = $O(object_id,instance_id);
app.win='{window_id}';
wm = getWindowManager();
if (app.win!="")
{
    app.win = wm.windows['{window_id}'];
}
app.opener_item = $I(getClientId('{opener_item}'));
if (app.opener_item!=0) {
    opener_object = app.opener_item.getAttribute("object");
    opener_instance = app.opener_item.getAttribute("instance");
    app.opener_object = $O(opener_object,opener_instance);
}
app.modules_string = '{modules_string}';
app.active_tab = '{active_tab}';
app.parent_object_id = '{parent_object_id}';
app.root_path = '{root_path}';
app.initModules();
app.user = '{User}';
window.onmousedown = app.onMouseDown;
window.onchange = app.onChange;
webitem_classes = "{webitem_classes}";