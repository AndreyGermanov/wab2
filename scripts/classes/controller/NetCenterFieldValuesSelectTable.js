var NetCenterFieldValuesSelectTable = Class.create(Mailbox, {
    build: function() {
        if (this.data=="") {
            return 0;
        }
        var data_arr = this.data.split("|");
        var c=0;
        for (c=0;c<data_arr.length;c++) {
            var data_str = data_arr[c].split("~");
            var row = document.createElement("tr");
            row.setAttribute("object",this.object_id);
            var col = document.createElement("td");
            col.setAttribute("object",this.object_id);
            var checkbox = document.createElement("input");
            checkbox.setAttribute("object",this.object_id);
            checkbox.id = data_str[1];
            checkbox.type = "checkbox";
            if (data_str[0]=="checked")
                checkbox.setAttribute("checked",true);
            col.appendChild(checkbox);
            row.appendChild(col);
            col = document.createElement("td");
            if (data_str[2]!=null)
                col.innerHTML = data_str[2];
            else
                col.innerHTML = data_str[1];
            row.appendChild(col);
            row.setAttribute("valign","top");
            this.table.appendChild(row);
            tr = document.createElement("tr");
            tr.setAttribute("object",this.object_id);
            tr.style.height="100%";
            tr.setAttribute("bgcolor","#FFFFFF");
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            td.style.height="100%";
            td.innerHTML = "&nbsp;";
            td.setAttribute("colspan",4);
            tr.appendChild(td);            
            this.table.appendChild(tr);
        }
    }
});