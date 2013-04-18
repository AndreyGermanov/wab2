qt = $O(object_id,instance_id);
qt.skinPath = '{skinPath}';
qt.module_id = '{module_id}';
qt.win='{window_id}';
wm = globalTopWindow.getWindowManager();
if (wm!=null) {
    if (qt.win!="")
    {
        qt.win = wm.windows['{window_id}'];
    }
}
qt.data = '{queue}';qt.data = qt.data.replace(/\'/g,"'");
qt.parent_object_id = '{parent_object_id}';
window.onmousedown = qt.onMouseDown;
window.onchange = qt.onChange;
qt.build(qt.data);

function updateQueueTable() {
    elems = $I(qt.node.id+"_table").getElementsByTagName("INPUT");
    arra = new Array;
    for (var c=0;c<elems.length;c++) {
        arra[elems[c].id] = elems[c].checked;
    }
    elems = $I(qt.node.id+"_table").getElementsByTagName("TR");
    arrb = new Array;
    for (var c=0;c<elems.length;c++) {
        arrb[elems[c].id] = elems[c].style.display;
        if (arrb[elems[c].id]==null)
            arrb[elems[c].id] = "";
    }
    new Ajax.Request("index.php", {
        method:"post",
        parameters: {ajax: true, object_id: "MailQueueTable_"+qt.module_id+"_Mail",hook: '6'},
        onSuccess: function(transport)
        {
            data = trim(transport.responseText.replace("\n",""));
            qt.build(data);
            for (c in arra) {
                if ([arra[c]]=="true") {
                    if (getElementById(qt.node,c)!=0)
                        getElementById(qt.node,c).checked = arra[c];
                }
            }             
            for (c in arrb) {
                if ([arrb[c]]=="" || [arrb[c]]==null) {
                    if (getElementById(qt.node,c)!=0)
                        getElementById(qt.node,c).style.display = arrb[c];
                }
            }
        }
    });
}
setInterval('updateQueueTable()',10000);