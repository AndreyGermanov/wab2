var UserAccessRulesTable = Class.create(HostAccessRulesTable, {
    getChecked: function() {
        var res = new Array;        
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var r = "";var w="";var found_read = false; var found_write = false;
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox') {
                if (elems[c].getAttribute("column")=='read') {
                    found_read = true;
                    if (elems[c].checked)
                        r = "r";
                    else
                        r = "";
                }
                if (elems[c].getAttribute("column")=='write') {
                    found_write = true;
                    if (elems[c].checked)
                        w = "w";
                    else
                        w = "";
                }
                if (found_read && found_write) {
                    res[res.length] = elems[c].getAttribute("share")+"~"+elems[c].getAttribute("path")+"~"+r+w;
                    found_read = false; found_write = false;r="";w="";
                }
            }
        }
        return res.join("|");
    }
});