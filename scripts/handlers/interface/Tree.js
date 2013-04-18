tree = $O(object_id,instance_id);
root = tree.root_node;

tree.module_id = '{module_id}';
tree.entityId = '{entityId}'; 
tree.loaded = {loadedStr};
tree.skinPath = "{skinPath}";

 if (root==null || typeof(root)=="undefined") root = tree.initTree(object_id);
 if (tree.loaded)
    tree.fillTree();

tree.win='{window_id}';
wm = getWindowManager();
if (wm!=null && tree.win!="")
{
    tree.win = wm.windows['{window_id}'];
}

tree.opener_item = $I(getClientId('{opener_item}'));
if (tree.opener_item!=0) {
    opener_object = tree.opener_item.getAttribute("object");
    opener_instance = tree.opener_item.getAttribute("instance");
    tree.opener_object = $O(opener_object,opener_instance);
}

tree.module_id = '{module_id}';
tree.parent_object_id = '{parent_object_id}';
tree.target_object = '{target_object}';
window.onmousedown = tree.onMouseDown;
window.onchange = tree.onChange;