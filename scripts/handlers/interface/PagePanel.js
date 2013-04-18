page_panel = $O(object_id,instance_id);
page_panel.current_page = '{current_page}';
page_panel.num_pages = '{num_pages}';
page_panel.items_per_page = '{items_per_page}';
page_panel.display_fields = '{display_fields}';
page_panel.parent_item = '{parent_item}';
page_panel.parent_object_id ='{parent_object_id}';
page_panel.win='{window_id}';
wm = getWindowManager();
if (wm!=null) {
    if (page_panel.win!="")
    {
        page_panel.win = wm.windows['{window_id}'];
    }
}
if (page_panel.parent_item!=null) {
    page_panel.parent_item = $I(page_panel.parent_item);
    if (page_panel.parent_item!=null)
        page_panel.parent_object = $O(page_panel.parent_item.getAttribute("object"),page_panel.parent_item.getAttribute("instance"));
}
//if (page_panel.parent_object!=null && page_panel.items_per_page>0)
    page_panel.build();
window.onmousedown = obj.onMouseDown;
window.onchange = obj.onChange;