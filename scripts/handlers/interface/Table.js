tbl = $O(object_id,instance_id);
tbl.rows = '{rows}';
tbl.cols = '{cols}';
tbl.win='{window_id}';
tbl.module_id='{module_id}';

tbl.table = $(tbl.node.id+"_table");
wm = getWindowManager();
if (wm!=null) {
    if (tbl.win!="")
    {
        tbl.win = wm.windows['{window_id}'];
    }
}
tbl.cells_data = '{row_properties}'+'\n'+'{cell_properties}';
tbl.parent_object_id = '{parent_object_id}';
tbl.skinPath = '{skinPath}';
tbl.build();