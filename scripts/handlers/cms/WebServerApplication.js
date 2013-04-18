function updateCache() {
    if (globalTopWindow.cacheUpdateLinks.length>0) {
        globalTopWindow.cacheUpdateFrame.src = globalTopWindow.cacheUpdateLinks.shift();        
    }
}

mapp = $O(object_id,instance_id);
mapp.win='{window_id}'
wm = getWindowManager();
if (mapp.win!="")
{
    mapp.win = wm.windows['{window_id}'];
}

mapp.parent_object_id = '{parent_object_id}';
mapp.opener_item = $I(getClientId('{opener_item}'));
if (mapp.opener_item!=0) {
    opener_object = mapp.opener_item.getAttribute("object");
    opener_instance = mapp.opener_item.getAttribute("instance");
    mapp.opener_object = $O(opener_object,opener_instance);
}
window.onmousedown = mapp.onMouseDown;
window.onchange = mapp.onChange;
webitem_classes = ",{webitem_classes},";
globalTopWindow.cacheUpdateLinks = new Array;
globalTopWindow.cacheUpdateFrame = $I(mapp.node.id+"_cacheUpdateFrame");

setInterval('updateCache()',2000);