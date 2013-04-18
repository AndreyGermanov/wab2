menu = $O(object_id,instance_id);
menu.opener_item = $I('{opener_item}');
if ('{opener_object}'!="")
	menu.opener_object_id = '{opener_object}';
else
	menu.opener_object_id = '';
object = menu.opener_item.getAttribute("object");
instance = menu.opener_item.getAttribute("instance");
menu.opener_object = $O(object,instance);
menu.parent_object_id = object;
menu.node.style.left = '{left}px';
menu.node.style.top = '{top}px';
$I(menu.node.id+"_table").style.width = '{width}px';
menu.node.style.position = 'absolute';
menu.node.style.zIndex = 200;
menu.module_id = '{module_id}';
menu.notHide = {notHide};
menu.load();