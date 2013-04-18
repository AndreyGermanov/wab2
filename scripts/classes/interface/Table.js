var Table = Class.create(Entity, {

    postInit: function() {
       
    },
    
    getClassName: function() {
        return "Table";
    },   

    addSettings: function(item,settings) {
        settings = settings.split("|");
        for (var counter1=0;counter1<settings.length;counter1++) {
            if (settings[counter1]=="")
                continue;            
            var parts = settings[counter1].split(":");
            if (parts[0]=="innerHTML") {
                parts.shift();
                parts = parts.join(":");
                item.innerHTML = parts;
            }
            else {               
                item.setAttribute(parts[0],parts[1]);
            };
        };
    },

    addRow: function(row_settings,cells,before) {
        var row = document.createElement("tr");
        row.setAttribute("class","col_lines");
        this.addSettings(row,row_settings);
        cells = cells.split("~");
        var column=null;
        for (var counter2=0;counter2<cells.length;counter2++) {
            if (!row.hasChildNodes()) {
                column = document.createElement("td");
                column.innerHTML="<img src='images/spacer.gif' width='2' border='0'>";
                column.setAttribute("class","col_lines");
                column.setAttribute("object",this.object_id);
                column.setAttribute("instance",this.instance_id);
                row.appendChild(column);
            }
            column = document.createElement("td");
            this.addSettings(column,cells[counter2]);
            column.setAttribute("object",this.object_id);
            column.setAttribute("instance",this.instance_id);
            column.setAttribute("onclick","$O('"+this.object_id+"','"+this.instance_id+"').cellClick(event)");
            column.setAttribute("onkeyup","$O('"+this.object_id+"','"+this.instance_id+"').onKeyUp(event)");
            column.setAttribute("oncontextmenu","$O('"+this.object_id+"','"+this.instance_id+"').onContextMenu(event)");
            column.id = this.node.id.concat("_").concat(column.id);
            this.cols = counter2+1;
            $(this.node.id+"_firstcol").colSpan = this.cols*2+1;
            row.appendChild(column);
            column = document.createElement("td");
            column.setAttribute("class","col_lines");
            column.innerHTML="<img src='images/spacer.gif' width='2' border='0'>";
            column.setAttribute("object",this.object_id);
            column.setAttribute("instance",this.instance_id);
            row.appendChild(column);
        }
        row.id = this.node.id.concat("_").concat(row.id);
        if (typeof(before)!="undefined") {
            this.table.insertBefore(row,before);
            var row1 = document.createElement("tr");
            row1.setAttribute("class","row_lines");
            row1.setAttribute("object",this.object_id);
            row1.setAttribute("instance",this.instance_id);
            column = document.createElement("td");
            column.setAttribute("colspan",2*this.cols+1);
            column.setAttribute("class","row_lines");
            column.setAttribute("object",this.object_id);
            column.setAttribute("instance",this.instance_id);
            row1.appendChild(column);
            this.table.insertBefore(row1,before);
        }
        else {
            this.table.appendChild(row);
            var row1 = document.createElement("tr");
            row1.setAttribute("class","row_lines");
            row1.setAttribute("object",this.object_id);
            row1.setAttribute("instance",this.instance_id);
            column = document.createElement("td");
            column.setAttribute("colspan",2*this.cols+1);
            column.setAttribute("class","row_lines");
            column.setAttribute("object",this.object_id);
            column.setAttribute("instance",this.instance_id);
            row1.appendChild(column);
            this.table.appendChild(row1);
        }
        return row;
    },

    build: function() {
        this.table.innerHTML = '';
        var firstrow = document.createElement("tr");
        firstrow.id = this.node.id+"_firstrow";
        firstrow.setAttribute("object",this.object_id);
        firstrow.setAttribute("instance",this.instance_id);
        firstrow.setAttribute("class","row_lines");
        var firstcol = document.createElement("td");
        firstcol.setAttribute("colspan",2*this.cols+1);
        firstcol.setAttribute("class","row_lines");
        firstcol.setAttribute("object",this.object_id);
        firstcol.setAttribute("instance",this.instance_id);
        firstcol.id = this.node.id+"_firstcol";
        firstrow.appendChild(firstcol);
        this.table.appendChild(firstrow);        
        var object_id = tbl.object_id.split("_");
        object_id.shift();
        object_id = object_id.join("_");
        var tbl = this;                
        var response = this.cells_data.replace(/\\'/g,"'").split("\n");

        if (response[0]=="")
            response.shift();
        var row_array = response[0].split("~");
        var row_cells_array = new Array;
        if (response[1]!=null)
            row_cells_array = response[1].split("&");
        var cells_array = new Array;
        for (var counter=0;counter<row_cells_array.length;counter++) {
            var parts = row_cells_array[counter].split("#");
            cells_array[parts[0]] = parts[1];
        }
        for (var counter=0;counter<row_array.length;counter++) {
            if (cells_array[counter]!=null)
                tbl.addRow(row_array[counter],cells_array[counter]);
        }
    },

    addEmptyRow: function(item) {
        var parentNode = item.parentNode;
        var parentSibling = parentNode.nextSibling;
        var new_node=parentNode.cloneNode(true);
        var new_sibling = parentSibling.cloneNode(true);
        this.rows = parseInt(this.rows)+1;
        
        new_node.id = this.node.id+"_row"+this.rows;
        this.table.appendChild(new_node);
        this.table.appendChild(new_sibling);
        var elems1 = new_node.getElementsByTagName("td");
        var counter1 = -1;
        for (var counter=0;counter<elems1.length;counter++) {
            if (elems1[counter].id!="") {
                counter1 = counter1+1;
                elems1[counter].innerHTML="&nbsp;";
                elems1[counter].id = this.node.id.concat("_col").concat(this.rows).concat("_").concat(counter1);
            }
        }
        var evt = document.createEvent("MouseEvents");
        evt.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, false, false, false, false, 0, null);
        elems1[1].dispatchEvent(evt);
        elems1[1].setAttribute("new_elem","true");
    },

    applyCell:function(event,direction) {
        var item = eventTarget(event);
        if (item.tagName=="INPUT" || item.tagName=="SELECT") {
            item = item.parentNode;
            if (item.tagName=="DIV")
                item = item.parentNode;
        }
        if (direction=="down") {
            var sible = item;
            while (1==1) {
                sible = sible.nextSibling.nextSibling;
                if (sible==null || sible.getAttribute("editable")=="true")
                    break;
            }
            if (sible!=null) {                
                if (this.onBlur(event)!=0) {
                    var evt = document.createEvent("MouseEvents");
                    evt.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, false, false, false, false, 0, null);
                    sible.dispatchEvent(evt);
                }
            }
            else {
                sible = item.parentNode.nextSibling.nextSibling;
                if (sible!=null) {
                    var elems = sible.getElementsByTagName("td");
                    if (elems[1]!=null) {
                        if (this.onBlur(event)!=0) {
                            var evt = document.createEvent("MouseEvents");
                            evt.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, false, false, false, false, 0, null);
                            elems[1].dispatchEvent(evt);
                        }
                    }
                }
                else {
                    if (this.onBlur(event)!=0) {                        
                        this.addEmptyRow(item);
                    }
                }
            }
        }
        else {
            var sible = item;
            while (1==1) {
                sible = sible.previousSibling.previousSibling;
                if (sible==null || sible.getAttribute("editable")=="true")
                    break;
            }
            if (sible!=null) {
                if (this.onBlur(event)!=0) {
                    var evt = document.createEvent("MouseEvents");
                    evt.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, false, false, false, false, 0, null);
                    sible.dispatchEvent(evt);
                }
            }
            else {
                sible = item.parentNode.previousSibling.previousSibling;
                if (sible!=null) {
                    var elems = sible.getElementsByTagName("td");
                    if (elems[1]!=null && elems[1].id!=this.node.id+"_col0_0") {
                        if (this.onBlur(event)!=0) {
                            var evt = document.createEvent("MouseEvents");
                            evt.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, false, false, false, false, 0, null);
                            if (elems[elems.length-2].getAttribute("editable")=="true")
                                elems[elems.length-2].dispatchEvent(evt);
                            else {
                                var el = elems[elems.length-2];
                                while (1==1) {
                                    el = el.previousSibling.previousSibling;
                                    if (el==null || sible.getAttribute("editable")==true)
                                        break;
                                }
                                if (el!=null)
                                el.dispatchEvent(evt);
                            }
                        }
                    }
                }
            }
        }
    },

    cancelCell: function(event) {
        var target_elem = eventTarget(event);
        
        if (target_elem.parentNode.getAttribute("new_elem")!="true") {
            target_elem.value = eventTarget(event).getAttribute("prev_value");
            target_elem.blur();
        }
        else {
            this.table.removeChild(target_elem.parentNode.parentNode.nextSibling);
            this.table.removeChild(target_elem.parentNode.parentNode);
        }
    },

    deleteCell: function(event) {
        var target_elem = eventTarget(event);
        if (this.win!=null && this.win!="") {
            var obj_id = this.win.php_object_id.split("_");
            obj_id.pop();
            obj_id = obj_id.join("_");
            $O(obj_id,'').onChange(event);
        }
        this.table.removeChild(target_elem.parentNode.parentNode.nextSibling);
        this.table.removeChild(target_elem.parentNode.parentNode);
    },

    removeRow: function(event) {
        var target_elem = eventTarget(event);
        if (target_elem.tagName=="INPUT" || target_elem.tagName=="SELECT")
            target_elem = target_elem.parentNode;
        if (this.win!=null && this.win!="") {
            var obj_id = this.win.php_object_id.split("_");
            obj_id.pop();
            obj_id = obj_id.join("_");
            if ($O(obj_id,'')!=null)
                if ($O(obj_id,'').onChange!=null)
                    $O(obj_id,'').onChange(event);
        }
        this.table.removeChild(target_elem.parentNode.nextSibling);
        this.table.removeChild(target_elem.parentNode);
    },

    insertRow: function(event) {
        var target_elem = eventTarget(event);
        if (target_elem.tagName=="INPUT" || target_elem.tagName=="SELECT")
            target_elem = target_elem.parentNode;
        var parentNode = target_elem;
        var parentSibling = parentNode.parentNode.nextSibling;
        if (this.onBlur(event)!=0) {
            var new_node=parentNode.parentNode.cloneNode(true);
            var new_sibling = parentSibling.cloneNode(true);
            this.rows = parseInt(this.rows)+1;
            new_node.id = this.node.id+"_row"+this.rows;
            this.table.insertBefore(new_node,parentNode.parentNode);
            this.table.insertBefore(new_sibling,parentNode.parentNode);
            var elems = new_node.getElementsByTagName("td");
            for (var counter=0;counter<elems.length;counter++) {
                if (elems[counter].id!="") {
                    elems[counter].innerHTML="&nbsp;";
                    elems[counter].id = this.node.id.concat("_col").concat(this.rows).concat("_").concat(counter);
                }
            }
            var evt = document.createEvent("MouseEvents");
            evt.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, false, false, false, false, 0, null);
            elems[1].dispatchEvent(evt);
            elems[1].setAttribute("new_elem","true");
            var obj_id = this.win.php_object_id.split("_");
            obj_id = obj_id.join("_");
            var event = new Array;
            event.target = $O(obj_id,'').node;
            $O(obj_id,'').onChange(event);
        }
    },

    moveRow: function(event,direction,block_id) {
        var target_elem = eventTarget(event);
        var inline_input = null;
        if (target_elem.tagName=="INPUT" || target_elem.tagName=="SELECT") {
            inline_input = target_elem;
            target_elem = target_elem.parentNode;
        }

        var parentNode = target_elem.parentNode;
        var parentSibling = parentNode.nextSibling;

        var sibling1=null;var sibling2=null;var sibling3=null;
        if (direction=="down") {
            sibling1 = parentSibling.nextSibling;
            sibling2 = parentSibling.nextSibling.nextSibling;
            sibling3 = parentSibling.nextSibling.nextSibling.nextSibling;
        }
        else {
            sibling1 = parentSibling.previousSibling;
            sibling2 = parentSibling.previousSibling.previousSibling;
            sibling3 = parentSibling.previousSibling.previousSibling.previousSibling;
        }
        if (sibling1==null || sibling2==null)
            return 0;
        var full_id = this.node.id.concat("_").concat(block_id);
        if (sibling1.id==full_id || sibling2.id==full_id)
            return 0;
        if (sibling3!=null)
            if (sibling3.id == full_id)
                return 0;
        var beforeNode = sibling3;
        this.table.insertBefore(parentNode,beforeNode);
        this.table.insertBefore(parentSibling,beforeNode);
        if (inline_input!=null)
            inline_input.focus();

        var obj_id = this.win.php_object_id.split("_");      
        obj_id = obj_id.join("_");
        var event = new Array;
        event.target = $O(obj_id,'').node;
        $O(obj_id,'').onChange(event);
        return 1;
    },

    getCell: function(row,number) {
        var cells = row.getElementsByTagName("TD");
        for (var counter1=0;counter1<cells.length;counter1++) {
            if (this.getEntityNumber(cells[counter1])==number)
                return cells[counter1];
        }
        return 0;
    },

    getCellsData: function() {
        var rows = this.table.getElementsByTagName("tr");
        var result = new Array;
        for (var counter=0;counter<rows.length;counter++) {
            if (rows[counter].getAttribute("class")!="row_lines") {
                var cells = rows[counter].getElementsByTagName("td");
                var col_array = new Array;
                for (var counter1=0;counter1<cells.length;counter1++) {
                    if (cells[counter1].getAttribute("onclick")!=null && cells[counter1].getAttribute("onclick")!="") {
                        var divs = cells[counter1].getElementsByTagName("DIV");
                        if (divs[0]!=null) {
                            var inputs = divs[0].getElementsByTagName("INPUT");
                            if (inputs[0]!=null)
                            {
                                var str = new Array;
                                for (var c1=0;c1<inputs.length;c1++) {
                                    if (inputs[c1].getAttribute("type")=="checkbox") {
                                        if (inputs[c1].checked)
                                            str[str.length] = "1";
                                        else
                                            str[str.length] = "0";
                                    }
                                    if (inputs[c1].getAttribute("type")=="hidden")
                                        str[str.length] = inputs[c1].value.toString();
                                }
                                col_array[col_array.length] = str.join(";");
                            }
                        }
                        else {
                            col_array[col_array.length] = cells[counter1].innerHTML;
                        }
                    }
                }
                result[result.length] = col_array.join("|");
            }
        }
        return result;
    },

    onKeyUp: function(event) {
        var key =event.which;
        event = event || window.event;
        event.cancelBubble = true;
        if (key==27)
            this.cancelCell(event);
        if (key==13) {
            if (!event.altKey)
                this.applyCell(event,"down");
            else
                this.applyCell(event,"up");
        }
        if (event.altKey) {
            if (key==46)
                this.removeRow(event);
            if (key==45)
                this.insertRow(event);
            if (key==40) {                
                this.moveRow(event,"down","none");
            }
            if (key==38)
                this.moveRow(event,"up","row0");
        }
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    },

    onBlur: function(event) {
        var target_elem = eventTarget(event);
        if (target_elem.tagName!="INPUT" && target_elem.tagName!="SELECT")
            return 1;
        if (target_elem.parentNode.getAttribute("must_set")=="true")
        {
            if (trim(target_elem.value)=="") {
                alert('Значение поля не может быть пустым!');
                target_elem.focus();
                return 0;
            }
        }
        if (target_elem.parentNode.getAttribute("unique")=="true") {
            var cur_elem = target_elem.parentNode;
            var cur_elem_number = this.getEntityNumber(cur_elem);
            var fields = this.table.getElementsByTagName("TD");

            for (var counter1=0;counter1<fields.length;counter1++) {
                var elem = fields[counter1];
                if (this.getEntityNumber(elem)!=cur_elem_number)
                    continue;
                if (elem.id==this.node.id+"_col0_0")
                    continue;
                if (elem.innerHTML==target_elem.value) {
                    alert('Поле с указанным именем уже существует!');
                    target_elem.focus();
                    return 0;
                }
            }
        }
        eventTarget(event).parentNode.innerHTML = trim(eventTarget(event).value);
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return 1;
    },

    cellClick: function(event) {
        if (eventTarget(event).getAttribute("editable")=="true" && eventTarget(event).tagName=="TD" && eventTarget(event).getAttribute("class")!="row_lines" && eventTarget(event).getAttribute("class")!="col_lines")
        {
            if (this.prev_cell != null) {
                var elems = this.prev_cell.getElementsByTagName("INPUT");
                if (elems[0]!=null)
                    this.prev_cell.innerHTML=elems[0].value;
                else {
                    elems = this.prev_cell.getElementsByTagName("SELECT");
                    if (elems[0]!=null)
                        this.prev_cell.innerHTML=elems[0].value;
                }
            }
                this.prev_cell = eventTarget(event);
                if (this.prev_cell.getAttribute("collection")!=null) {
                    var inplace = document.createElement("select");
                    var collection = this.prev_cell.getAttribute("collection").split(",");
                    for (var c1=0;c1<collection.length;c1++) {
                        inplace.options[c1] = new Option(collection[c1],collection[c1]);
                    }
                }
                else
                    var inplace = document.createElement("input");
                inplace.id = eventTarget(event).id;
                inplace.setAttribute("object",this.object_id);
                inplace.setAttribute("instance",this.instance_id);
                inplace.setAttribute("onblur","$O('"+this.object_id+"','"+this.instance_id+"').onBlur(event)");
                inplace.setAttribute("onkeyup","$O('"+this.object_id+"','"+this.instance_id+"').onKeyUp(event)");
                inplace.setAttribute("oncontextmenu","$O('"+this.object_id+"','"+this.instance_id+"').onContextMenu(event)");
                inplace.value = this.prev_cell.innerHTML.replace(/&nbsp;/g,'');
                inplace.setAttribute("prev_value",inplace.value);
                this.prev_cell.innerHTML = "";
                this.prev_cell.removeAttribute("new_elem");
                this.prev_cell.appendChild(inplace);
                inplace.style.width="100%";
                inplace.style.borderWidth = "0px";
                inplace.focus();
        }
        else {
            if (this.prev_cell != null) {
                var elems = this.prev_cell.getElementsByTagName("INPUT");
                if (elems[0]!=null)
                    elems[0].focus();
                else {
                    elems = this.prev_cell.getElementsByTagName("SELECT>");
                    if (elems[0]!=null)
                        elems[0].focus();
                }
            }
        }        
        var obj_id = this.node.parentNode.getAttribute("object");
        var event = new Array;
        event.target = $O(obj_id,'').node;
        $O(obj_id,'').onChange(event);
    },

    getEntityNumber: function(entity) {
        var entities = entity.parentNode.getElementsByTagName("td");
        var number = 0;
        for (var counter=0;counter<entities.length;counter++) {
            if (counter%2!=0) {
               
                if (entities[counter].id == entity.id)
                    return number;
                number = number+1;
            }
        }
        return -1;
    }
});