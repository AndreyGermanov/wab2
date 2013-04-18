var ObjectsSelectTable = Class.create(GroupMembersTable, {
    getChecked: function() {
        var res = new Array;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox') {
                if (elems[c].checked)
                    res[res.length] = elems[c].id;
            }
        }
        return res.join(",");
    }
});