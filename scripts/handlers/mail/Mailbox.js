mbox = $O(object_id,instance_id);
mbox.win='{window_id}';
mbox.module_id='{module_id}';
mbox.name = '{name}';
wm = globalTopWindow.getWindowManager();
if (wm!=null) {
    if (mbox.win!="")
    {
        mbox.win = wm.windows['{window_id}'];
        if (mbox.win!=null)
            mbox.readOnly = mbox.win.readOnly;
    }
}
mbox.opener_item = $I(getClientId('{opener_item}'));
if (mbox.opener_item!=0) {
    opener_object = mbox.opener_item.getAttribute("object");
    opener_instance = mbox.opener_item.getAttribute("instance");
    mbox.opener_object = $O(opener_object,opener_instance);
}
mbox.module_id = '{module_id}';
mbox.skinPath = '{skinPath}';
mbox.parent_object_id = '{parent_object_id}';
mbox.buildControls();
window.onmousedown = mbox.onMouseDown;
window.onchange = mbox.onChange;
mbox.setReadOnly();
obj = mbox;