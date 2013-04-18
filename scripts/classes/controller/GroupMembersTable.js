var GroupMembersTable = Class.create(Mailbox, {
    build: function() {
        var table = $(this.node.id+"_table");
        table.setAttribute('bgcolor','#BBBBBB');
        table.setAttribute('cellspacing','1');
        table.setAttribute('cellpadding','5');
        table.style.height="100%";
        table.setAttribute("object",this.object_id);
        var rows = this.table_rows.split('|');
        var cols = new Array;
        var c=0;   
        var td=null;
        for (c=0;c<rows.length;c++) {
            var row = document.createElement("tr");
            row.setAttribute("object",this.object_id);
            cols = rows[c].split("#");
            var c1=0;
            for (c1=0;c1<cols.length;c1++) {
                var data = cols[c1].split('~');
                if (data[0]!="row_attrs") {
                    td = document.createElement('td');
                    td.innerHTML = data[0];
                    td.setAttribute("object",this.object_id);
                    if (data[1]!=null) {
                        var attrs_arr = data[1].split("&");
                        var c2=0;
                        for (c2=0;c2<attrs_arr.length;c2++) {
                            var attr_parts = attrs_arr[c2].split("=");
                            td.setAttribute(attr_parts[0],attr_parts[1]);
                        }
                    }
                }
                else {
                    if (data[1]!=null) {
                        var attrs_arr = data[1].split("&");
                        var c2=0;
                        for (c2=0;c2<attrs_arr.length;c2++) {
                            var attr_parts = attrs_arr[c2].split("=");
                            row.setAttribute(attr_parts[0],attr_parts[1]);
                        }
                    }
                }
                if (td!=null)
                	row.appendChild(td);
            }
            table.appendChild(row);            
        }
        var tr = document.createElement("tr");
        tr.setAttribute("object",this.object_id);
        tr.style.height="100%";
        tr.setAttribute("bgcolor","#FFFFFF");
        var td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.style.height="100%";
        td.innerHTML = "&nbsp;";
        td.setAttribute("colspan",cols.length);
        tr.appendChild(td);
        table.appendChild(tr);
    },

    checkAll: function(event) {
        var item = eventTarget(event);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox') {
                elems[c].checked = item.checked;
            }
        }
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },

    check: function(event) {
        var ev = new Array;
        ev["target"] = this.opener_item;
        if (this.opener_object != null)
            this.opener_object.onChange(ev);
    },

    getChanged: function() {
        var res = new Array;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox') {
                if (elems[c].checked && elems[c].getAttribute('value')!='checked') {
                    res[res.length] = elems[c].id.replace("ObjectGroup_"+this.module_id+"_"+this.idnumber+"_ObjectGroup_"+this.idnumber+"_","")+"=yes";
                } else
                if (!elems[c].checked && elems[c].getAttribute('value')=='checked') {
                    res[res.length] = elems[c].id.replace("ObjectGroup_"+this.module_id+"_"+this.idnumber+"_ObjectGroup_"+this.idnumber+"_","")+"=no";
                }
            }
        }
        return res.join("|");
    }
});