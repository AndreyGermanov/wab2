var {clientObjectId}tbl = $O(object_id,instance_id);
{clientObjectId}tbl.win='{window_id}';
{clientObjectId}tbl.module_id='{module_id}';

{clientObjectId}tbl.table = $I({clientObjectId}tbl.node.id+"_table");
var wm = getWindowManager();
if (wm!=null) {
    if ({clientObjectId}tbl.win!="")
    {
    	{clientObjectId}tbl.win = wm.windows['{window_id}'];
    }
}
{clientObjectId}tbl.opener_item = $I(getClientId('{opener_item}'));
{clientObjectId}tbl.opener_object = $O('{opener_object}');
{clientObjectId}tbl.parent_object_id = '{parent_object_id}';
{clientObjectId}tbl.skinPath = '{skinPath}';
{clientObjectId}tbl.currentControl = 0;
{clientObjectId}tbl.currentPage = '{currentPage}';
{clientObjectId}tbl.properties = "bgcolor=#000000,cellpadding=0,cellspacing=1,width=100%";
{clientObjectId}tbl.sortOrder = "{sortOrder}";
{clientObjectId}tbl.readonly = '{readonly}';
{data}

{clientObjectId}tbl = $O(object_id,instance_id);
var args = new Object;
args["window_id"] = '{window_id}';
args["current_page"] = '{currentPage}';
args["num_pages"] = "{numPages}";
args["parent_item"] = '{object_id}';
args["parent_object_id"] = '{object_id}';
{clientObjectId}tbl.frameSrc = "index.php?object_id={pagePanelId}&hook=show&arguments="+Object.toJSON(args);
{clientObjectId}tbl.onLoad();