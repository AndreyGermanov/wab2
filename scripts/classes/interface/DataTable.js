var obj1 = null;
var DataTable = Class.create(Mailbox, {

    getSingleValue: function() {
        return "";
    },

    addRow: function(row,position,rebuild) {
        var maxrow = this.getMaxRow();
        while ($I(this.node.id+"_"+maxrow+"_"+getClientId(this.columns[0]["name"])+"_td")!=0) {
            maxrow = maxrow+1;
        };
        
        if (position==null)
            position = maxrow;
        var is_new_row = false;
        var is_new_column = false;
        var tr=null;
        if (row["node"]==null || rebuild==true) {
            tr = document.createElement("tr");
            tr.id = this.node.id+"_row_"+position;
            is_new_row = true;
        }
        else {
            tr = row["node"];
            is_new_row = false;
        }
        tr.setAttribute("class",row["class"]);
        tr.setAttribute("valign","top");
        tr.setAttribute("object",this.object_id);
        tr.setAttribute("instance",this.instance_id);
        if (row["properties"]!="" && row["properties"]!=null) {
            row_props = row["properties"].split(",");
            var c=0;
            for (c=0;c<row_props.length;c++) {
                row_prop = row_props[c].split("=");
                tr.setAttribute(row_prop[0],row_prop[1]);
            }
        }
        if (this.sortOrder!="") {
            var sort_parts = this.sortOrder.split(" ");
            var sortField = sort_parts[0];
            var sortColumn = this.getColumnNumber(sortField);
        }
        else
            sortColumn = "";
        row["node"] = tr;
        var cells = row["cells"];
        var is_new_control=false;
        for (c=0;c<cells.length;c++) {
            if (cells[c]["node"]==null || rebuild==true) {
                td = document.createElement("td");
                td.id = this.node.id+"_"+maxrow+"_"+getClientId(this.columns[c]["name"])+"_td";
                is_new_column = true;
                td.setAttribute("row",position);
            }
            else {
                td = cells[c]["node"];
                is_new_column = false;
            }
            td.setAttribute("column",this.columns[c]["name"]);
            td.setAttribute("object",this.object_id);
            td.setAttribute("onclick","$O('"+this.object_id+"','').cellClick(event)");
            td.setAttribute("ondblclick","$O('"+this.object_id+"','').cellDblClick(event)");
            cells[c]["node"] = td;
            var cls=null;
            if (cells[c]["class"]!=null && cells[c]["class"]!="")
                cls = cells[c]["class"];
            else
                cls = this.columns[c]["class"];
            td.setAttribute("class",cls);
            var props=null;
            if (cells[c]["properties"]!=null && cells[c]["properties"]!="")
                props = cells[c]["properties"];
            else
                props = this.columns[c]["properties"];
            if (props!=null && props!="") {
                props = props.split(",");
                var c1=0;
                for (c1=0;c1<props.length;c1++) {
                    var prop = props[c1].split("=");
                    td.setAttribute(prop[0],prop[1]);
                }
            }
            var control=null;
            if (cells[c]["control_node"]==null || rebuild == true) {
                control = document.createElement("control");
                control.id = this.node.id+"_"+maxrow+"_"+getClientId(this.columns[c]["name"]);
                is_new_control = true;
                cells[c]["control_node"] = control;
            } else {
                control = row["cells"][c]["control_node"];
                is_new_control = false;
            }
            var type=null;
            control.setAttribute("object",control.id);
            if (cells[c]["control"]!=null && cells[c]["control"]!="")
                type = cells[c]["control"];
            else
                type = this.columns[c]["control"];
            control.setAttribute("type",type);
				
            if (type=="header") {
                if (sortColumn==c) {
                    this.sortHeaderColumn = c;
                    this.sortHeaderRow = position;
                }
            }
            if (cells[c]["control_properties"]!=null && cells[c]["control_properties"]!="")
                props = cells[c]["control_properties"];
            else
                props = this.columns[c]["control_properties"];

            if (props!="" && props!=null) {
                props = props.split(",");
                var c1=0;
                for (c1=0;c1<props.length;c1++) {
                    prop = props[c1].split("=");
                    if (prop[1]==null)
                    	continue;
                    control.setAttribute(prop[0],prop[1].replace(/~/g,",").replace(/xyzxyz/g,',').replace(/zozo/g,'='));
                }
            }
            var must_set=null;
            if (cells[c]["must_set"]!=null && cells[c]["must_set"]!="")
                must_set = cells[c]["must_set"];
            else
                must_set = this.columns[c]["must_set"];
            if (must_set!=null && must_set!="")
                control.setAttribute("must_set",must_set);
            var unique=null;
            if (cells[c]["unique"]!=null && cells[c]["unique"]!="")
                unique = cells[c]["unique"];
            else
                unique = this.columns[c]["unique"];
            if (unique!=null && unique!="")
                control.setAttribute("unique",unique);
            var readonly=null;
            if (cells[c]["readonly"]!=null && cells[c]["readonly"]!="")
                readonly = cells[c]["readonly"];
            else
                readonly = this.columns[c]["readonly"];
            if (readonly!=null && readonly!="")
                control.setAttribute("readonly",readonly);
            var value=null;
            if (cells[c]["value"]!=null)
                value = cells[c]["value"];
            else
                value = this.columns[c]["value"];
            if (value!=null)
                control.setAttribute("value",value);
            cells[c]["control_node"] = control;
            if (is_new_control)
                td.appendChild(control);
            if (is_new_column)
                tr.appendChild(td);
        }
        if (is_new_row) {
            var before=0;
            if (position>0)
                    before = $I(this.node.id+"_row_"+position);
                if (before!=0) {
                    this.table.insertBefore(tr,before);
                }
                else {
                    this.table.appendChild(tr);
                }
            
        }
        is_new_row = true;
        is_new_column = true;
        is_new_control = true;
        var object = tr;
        var elems1 = object.getElementsByTagName('CONTROL');
        var c1=0;
        for (c1=0;c1<elems1.length;c1++) {
            var objectid = elems1[c1].id;
            var is_new = false;
            if ($O(objectid,'')==0 || $O(objectid,'')==null) {
                is_new = true;
                globalTopWindow.objects.add(new InputControl(objectid,''));
            }
            var ctl = $O(objectid,'');
            ctl.win = this.win;
            ctl.readonly = this.readonly;
            if (is_new) {
                if (elems1[c1].innerHTML!="")
                    continue;
            }
            if (typeof(ctl.parent_object_id)=="undefined" || ctl.parent_object_id==null)
            	ctl.parent_object_id = this.object_id;
            ctl.skinPath = this.skinPath;
            ctl.build(rebuild);
        }
    },

    getColumnNumber: function(name) {
    	var col_num=0;
        for (col_num=0;col_num<this.columns.length;col_num++) {
            if (this.columns[col_num]["name"]==name) {
                return col_num;
            }
        }
        return -1;
    },
    
    summColumn: function(column) {
    	if (column!=parseInt(column))
    		column = this.getColumnNumber(column);
    	var result = 0;
    	var i=0;
    	for (i=0;i<this.rows.length;i++) {
    		if (typeof(this.rows[i])=="function")
    			continue;
    		if (!isNaN(parseFloat(this.rows[i]['cells'][column]['value']))) {
    			result = result + parseFloat(this.rows[i]['cells'][column]['value']);
    		}
    	}
    	return result;
    },

    getItem: function(row,column) {
        if (column!=parseInt(column))
            column = this.getColumnNumber(column);        
        var item = $I(this.node.id+"_row_"+row);
        if (item!=0) {
            var els = item.getElementsByTagName("TD");
            var i=0;var ii=0;
            while (ii<=column) {
                if (els[i].parentNode===item) {
                    ii++;
                }
                i++;
            }
            if (i==0)
            	i=1;
            var item = els[i-1];
            if (item!=null) {
                els = item.getElementsByTagName("CONTROL");
                item = els[0];
                if (item!=null)
                    item = $O(item.getAttribute("object"),"");
                return item;
            }
        }
        return 0;
    },

    build: function(rebuild) {
        if (this.table==null || this.table==0)
            return 0;
        if (this.properties!="") {
            var props = this.properties.split(",");
            var c=0;
            for (c=0;c<props.length;c++) {
                var prop = props[c].split("=");
                this.table.setAttribute(prop[0],prop[1]);
            }
        }
        var count=0;
        for (count=0;count<this.rows.length;count++) {
            this.addRow(this.rows[count],count,rebuild);
        }
    },
    
    sort: function() {
        if (this.sortOrder!="") {
            var sort_parts = this.sortOrder.split(" ");
            var sortField = sort_parts[0];
            var sortDirection = "";
            var imgsrc="";
            this.sortField = sortField;
            if (sort_parts[1]!=null) {
                sortDirection = sort_parts[1];
                if (sortDirection=='DESC')
                    imgsrc = this.skinPath+"images/Buttons/sortdesc.png";
                else
                    imgsrc = this.skinPath+"images/Buttons/sortasc.png";
            }
            else {
                sortDirection = "ASC";
                imgsrc = this.skinPath+"images/Buttons/sortasc.png";
            }
            this.sortDirection = sortDirection;
            this.sortColumn = this.getColumnNumber(sortField);
            this.rows.sort(this.doCompare);
            //this.table.innerHTML = "";
            this.build();
            var header_ctl = this.getItem(this.sortHeaderRow,this.sortColumn);
            if (header_ctl!=0) {
                $I(header_ctl.node.id+"_image").src = imgsrc;
            }
            if (typeof(this.getSingleValue())=="string")
            	this.raiseEvent("DATATABLE_VALUE_CHANGED",$Arr('object_id='+this.object_id+',parent='+this.parent_object_id+',value='+this.getSingleValue().replace(/=/g,"xox").replace(/,/g,"yoy")));
        }
    },

    doCompare: function(a,b) {
        if (a["cells"][this.sortColumn].control=="header")
            return 0;
        var row_number = a["cells"][this.sortColumn]["node"].getAttribute("row");
        var column = a["cells"][this.sortColumn]["node"].getAttribute("column");
        var item1 = obj1.getItem(row_number,column);
        var value_a = "";
        if (item1!=0 && item1!=null) {
        	if (item1.getPresentation()!=null)
        		value_a = item1.getPresentation().toUpperCase();
        }

        row_number = b["cells"][this.sortColumn]["node"].getAttribute("row");
        column = b["cells"][this.sortColumn]["node"].getAttribute("column");
        item1 = obj1.getItem(row_number,column);
        var value_b = "";
        if (item1!=0 && item1!=null) {
        	if (item1.getPresentation()!=null)
        		value_b = item1.getPresentation().toUpperCase();
        }
        if (parseFloat(value_a)==value_a)
            value_a=parseFloat(value_a);
        if (parseFloat(value_b)==value_b)
            value_b=parseFloat(value_b);
        
        if (value_a>value_b) {
            if (this.sortDirection=="ASC") {
                return 1;
            }
            else
                return -1;
        }
        if (value_a<value_b) {
            if (this.sortDirection=="ASC") {
                return -1;
            }
            else
                return 1;
        }
        return 0;
    },

    copyRow: function(row) {
        var roww = new Array;
        roww["class"] = row["class"];
        roww["properties"] = row["properties"];
        roww["cells"] = new Array;
        var ce=0;
        for (ce=0;ce<row["cells"].length;ce++) {
            roww["cells"][ce] = new Array;
            roww["cells"][ce]["properties"] = row["cells"][ce]["properties"];
            roww["cells"][ce]["control"] = row["cells"][ce]["control"];
            roww["cells"][ce]["control_properties"] = row["cells"][ce]["control_properties"];
            if (row["cells"][ce]["value"]!=null)
            	roww["cells"][ce]["value"] = row["cells"][ce]["value"];
            else
            	roww["cells"][ce]["value"] = "";
        }
        return roww;
    },

    recalcRows: function() {
    	var i=0;
        for (i=0;i<this.rows.length;i++) {
            var node = this.rows[i]["node"];
            node.id = this.node.id+"_row_"+i;
            var cells = this.rows[i]["cells"];
            var i1=0;
            for (i1=0;i1<cells.length;i1++) {
                cells[i1]["node"].setAttribute("row",i);
            }
        }
    },

    appendRow: function(row,rebuild) {
        var roww = this.copyRow(row);
        this.addRow(roww,this.rows.length,rebuild);
        this.rows[this.rows.length] = roww;
        this.getItem(this.rows.length-1,0).setFocus();
    },

    insertRow: function(row,position) {
        var roww = this.copyRow(row);
        this.addRow(roww,position);
        this.rows.splice(position,0,roww);
        this.recalcRows();
        this.getItem(position,0).setFocus();
    },

    deleteRow: function(position) {
        var node = this.rows[position].node;
        var c1=0;
        for (c1=0;c1<this.rows[position]["cells"].length;c1++) {
            var item = this.getItem(position,c1);
            if (item!=null && item!=0) {     
                delete globalTopWindow.objects.objects[item.object_id];
                delete item;
            }
        }
        var elems = node.getElementsByTagName("CONTROL");
        var c=0;
        for (c=0;c<elems.length;c++) {
        	var objid = elems[c].getAttribute("id");
        	var obj = $O(objid,"");
        	if (obj!=null) {
        		delete globalTopWindow.objects.objects[objid];
                delete obj;
       		}
        }
        node.parentNode.removeChild(node);
        this.rows.splice(position,1);
        this.recalcRows();
        blur_error = "";
        if (typeof(this.getSingleValue())=="string")
        	this.raiseEvent("DATATABLE_VALUE_CHANGED",$Arr('object_id='+this.object_id+',parent='+this.parent_object_id+',value='+this.getSingleValue().replace(/=/g,"xox").replace(/,/g,'yoy')));
    },
    
    deleteRows: function() {
        while(this.rows.length>0)
            this.deleteRow(0);
    },

    moveRow: function(position,direction) {
        var node = this.rows[position].node;
        position = parseInt(position);
        if (direction=="up") {
            if (position>1) {
                node.parentNode.insertBefore(node,node.previousSibling);
                this.rows.splice(position-1,0,this.rows[position]);
                this.rows.splice(position+1,1);
                this.recalcRows();
            }
        }
        else {
            if (position<this.rows.length-1) {
                node.parentNode.insertBefore(node,node.nextSibling.nextSibling);
                this.rows.splice(position+2,0,this.rows[position]);
                this.rows.splice(position,1);
                this.recalcRows();
            }
        }         
        blur_error = "";
        if (typeof(this.getSingleValue())=="string")
        	this.raiseEvent("DATATABLE_VALUE_CHANGED",$Arr('object_id='+this.object_id+',parent='+this.parent_object_id+',value='+this.getSingleValue().replace(/=/g,"xox").replace(/,/g,'yoy')));
    },

    getMaxRow: function() {
        var mx = -1;
        var rw=0;
        for (rw=0;rw<this.rows.length;rw++) {
            var row1 = this.rows[rw];
            var cell1 = row1["cells"][0];
            if (cell1==null)
                return mx+1;
            var node1 = cell1["node"];
            if (node1==null)
                return mx+1;
            var elems1 = node1.getElementsByTagName("CONTROL");
            if (elems1.length>0) {
                var id = elems1[0].id;
                var arr1 = id.split("_");
                arr1.pop();
                var num1 = parseInt(arr1.pop());
                if (num1>mx)
                    mx = num1;
            }
        }
        return mx+1;
    },

    getColumnAtRow: function(object_id,row_id) {
        var obj = $O(object_id,"");
        var col_node = obj.node.parentNode;
        var col_id = col_node.getAttribute("column");
        var row_node = obj.node.parentNode.parentNode;
        
        while (row_node.previousSibling != null) {
                if (row_node.previousSibling==null)
                    break;
                row_node = row_node.previousSibling;
        }
        var i1=0;
        for (i1=0;i1<row_id;i1++) {
            if (row_node.nextSibling != null)
                row_node = row_node.nextSibling;
        }
        if (row_node==null)
            return 0;
        var td_nodes = row_node.getElementsByTagName("TD");
        var td_id=0;
        for (td_id=0;td_id<td_nodes.length;td_id++) {
            if (td_nodes[td_id].getAttribute("row")==row_id && td_nodes[td_id].getAttribute("column")==col_id) {
                var control_elems = td_nodes[td_id].getElementsByTagName("CONTROL");
                var ctl_obj = $O(control_elems[0].getAttribute("object"),"");
                if (ctl_obj!=0) {
                    if (ctl_obj.parent_object_id == this.object_id) {
                        return ctl_obj;
                    }
                }
            }
        }
        return 0;
    },

    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event=="CONTROL_VALUE_CHANGED") {
        	params["value"] = params["value"].replace(/xoxoxo/g,',');
            this.obje = $O(params["object_id"],"");
            if (this.obje!=0 && this.obje != null) {
            	if (this.obje.parent_object_id != this.object_id) {
            		if (this.obje.node.getAttribute("object")!=null) {
            			var ob = this.obje.node.getAttribute("object").split("_");
            			ob.pop();ob.pop();
            			ob = ob.join("_");
            			if (ob==this.object_id)
            				this.obje.parent_object_id = ob;
            		}
            	}
                if (this.obje.parent_object_id == this.object_id) {
                    var ctl = $O(params["object_id"],"");
                    var node = ctl.node.parentNode;
                    var column = this.getColumnNumber(node.getAttribute("column"));
                    var row = node.getAttribute("row");
                    if (ctl.unique=="true") {
                    	var i2=0;
                        for (i2=0;i2<this.rows.length;i2++) {
                            var ctl1 = this.getItem(i2,column);
                            if (ctl1==0 || ctl1==null)
                                continue;
                            if (ctl1.getValue() == ctl.getValue() && ctl.object_id!= ctl1.object_id) {
                                alert('Значение в столбце должно быть уникально !');
                                ctl.setFocus();
                            }
                        }
                    }
                    if (params["value"]!=null)
                    	this.rows[row]["cells"][column]["value"] = params["value"];
                    if (typeof(this.getSingleValue())=="string")                    
                    	this.raiseEvent("DATATABLE_VALUE_CHANGED",$Arr('object_id='+this.object_id+',parent='+this.parent_object_id+',value='+this.getSingleValue().replace(/=/g,"xox").replace(/,/g,"yoy")));
                }
            }
        }
        if (event=="CONTROL_HAS_FOCUSED") {
            this.obje = $O(params["object_id"],"");
            if (this.obje!=0) {
                if (this.obje.parent_object_id == this.object_id) {
                    var wm = getWindowManager();
                    if (wm!=null)
                        if (this.win!=null) {
                            if (params["skip_activation"]!="true")
                                wm.activate_window(this.win.id);                    
                        }
                    this.currentControl = $O(params["object_id"],'');
                    var node = this.obje.node;
                    var cll=null;
                    while (node.tagName!="TR") {
                        if (node.tagName=="TD")
                            cll = node;
                        node = node.parentNode;
                    }
                    var elems = node.getElementsByTagName("TD");
                    var ce=0;
                    for (ce=0;ce<elems.length;ce++)
                        if (elems[ce].getAttribute("class")!="hidden" && elems[ce].getAttribute("class")!="header" && elems[ce].getAttribute("class")!="header_text")
                            elems[ce].setAttribute("class","selected_cell");
                    if (this.previousRow!=null && this.previousRow!=node) {
                        elems = this.previousRow.getElementsByTagName("TD");
                        for (ce=0;ce<elems.length;ce++)
                            if (elems[ce].getAttribute("class")!="hidden" && elems[ce].getAttribute("class")!="header" && elems[ce].getAttribute("class")!="header_text")
                                elems[ce].setAttribute("class","cell");
                    }
                    if (cll!=null && cll.getAttribute("class")!="hidden" && cll.getAttribute("class")!="header" && cll.getAttribute("class")!="header_text")
                        cll.setAttribute("class","cursor_cell");                         
                    this.previousRow = node;
                }
            }
            if (window.getSelection()!=null)
            	window.getSelection().removeAllRanges();
        }
        if (event=="CONTROL_KEYPRESS") {
            this.obje = $O(params["object_id"],"");
            if (this.obje!=0) {
                if (this.obje.parent_object_id == this.object_id) {
                    var key_code = params["keycode"];
                    var alt_key = params["alt"];
                    if (key_code==45) {
                    	if (this.readonly=="true")
                    		return 0;
                        if (alt_key=="true")
                            this.insertButton_onClick(event);
                        else
                            this.addButton_onClick(event);
                    }
                    if (alt_key=="true") {
                    	if (this.readonly=="true")
                    		return 0;
                        if (key_code==43) {
                            this.copyButton_onClick(event);
                        }
                        if (key_code==46)
                            this.deleteButton_onClick(event);
                    }
                    if (key_code=="40") {
                        if (alt_key=="true") {
                            var col_node = this.obje.node.parentNode;
                            var row_id = parseInt(col_node.getAttribute("row"));
                            var ctl_obj = this.getItem(row_id,this.obje.node.parentNode.getAttribute("column"));
                            this.moveDownButton_onClick(event);
                            ctl_obj.setFocus();
                        }
                        else {
                            var col_node = this.obje.node.parentNode;
                            var row_id = parseInt(col_node.getAttribute("row"))+1;
                            var ctl_obj = this.getItem(row_id,this.obje.node.parentNode.getAttribute("column"));
                            if (ctl_obj!=0 && ctl_obj.type!="plaintext")
                                ctl_obj.setFocus();
                        }
                    }
                    if (key_code=="38") {
                        if (alt_key=="true") {
                        	if (this.readonly=="true")
                        		return 0;
                            var col_node = this.obje.node.parentNode;
                            var row_id = parseInt(col_node.getAttribute("row"));
                            var ctl_obj = this.getItem(row_id,this.obje.node.parentNode.getAttribute("column"));
                            this.moveUpButton_onClick(event);
                            ctl_obj.setFocus();
                        }
                        else {
                            var col_node = this.obje.node.parentNode;
                            var row_id = parseInt(col_node.getAttribute("row"))-1;
                            var ctl_obj = this.getItem(row_id,this.obje.node.parentNode.getAttribute("column"));
                            if (ctl_obj!=0 && ctl_obj.type!="plaintext")
                                ctl_obj.setFocus();
                        }
                    }
                }
            }
        }

        if (event=="TABLE_HEADER_CLICKED") {
            this.obje = $O(params["object_id"],"");
            if (this.obje!=0) {
                if (this.obje.parent_object_id == this.object_id) {
                    var column = this.obje.node.parentNode.getAttribute("column");
                    if (this.sortOrder=="") {
                        this.sortOrder = column+" ASC";
                    } else {
                        var sort_parts = this.sortOrder.split(" ");
                        if (sort_parts[1]==null)
                            sort_parts[1] = "ASC";
                        if (sort_parts[0]==column) {
                            if (sort_parts[1]=="ASC")
                                sort_parts[1] = "DESC";
                            else
                                sort_parts[1] = "ASC";
                            this.sortOrder = sort_parts[0]+" "+sort_parts[1];
                        } else {
                            this.sortOrder = column+" ASC";
                        }
                    }                    
                    this.sort();
                }
            }
        }
    },

    button_onMouseDown: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_clicked."+ext;
        var el = eventTarget(event);
        el.src = src;
    },

    button_onMouseUp: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        var el = eventTarget(event);
        el.src = src;
    },

    button_onMouseOver: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_hover."+ext;
        var el = eventTarget(event); 
        el.src = src;
    },

    button_onMouseOut: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        var el = eventTarget(event);
        el.src = src;
    },

    addButton_onClick: function(event) {
    	if (this.readonly=="true")
    		return 0;
        this.appendRow(this.emptyrow);
    },

    copyButton_onClick: function(event) {
    	if (this.readonly=="true")
    		return 0;
        if (this.currentControl!=0) {
            var ctl = this.currentControl;
            var num = ctl.node.parentNode.getAttribute("row");            
            this.appendRow(this.rows[num]);
        }
    },

    insertButton_onClick: function(event) {
    	if (this.readonly=="true")
    		return 0;
        if (this.currentControl!=0) {
            var ctl = this.currentControl;
            var num = ctl.node.parentNode.getAttribute("row");
            this.insertRow(this.emptyrow,num);            
        }
    },

    deleteButton_onClick: function(event) {
    	if (this.readonly=="true")
    		return 0;
        if (this.currentControl!=0) {
            var ctl = this.currentControl;
            var num = ctl.node.parentNode.getAttribute("row");
            this.deleteRow(num);
        }
    },

    moveUpButton_onClick: function(event) {
    	if (this.readonly=="true")
    		return 0;
        if (this.currentControl!=0) {
            var ctl = this.currentControl;
            var num = ctl.node.parentNode.getAttribute("row");
            this.moveRow(num,"up");
        }
    },

    moveDownButton_onClick: function(event) {
    	if (this.readonly=="true")
    		return 0;
        if (this.currentControl!=0) {
            var ctl = this.currentControl;
            var num = ctl.node.parentNode.getAttribute("row");
            this.moveRow(num,"down");
        }
    },

    findControlByValue: function(value) {
    	var ce=0;
        for (ce=0;ce<this.rows.length;ce++) {
            var row = this.rows[ce];
            var ce1=0;
            for (ce1=0;ce1<row["cells"].length;ce1++) {
                if (row["cells"][ce1]["value"]==value && row["cells"][ce1].node!=null) {
                    return this.getItem(row["cells"][ce1].node.getAttribute("row"),ce1);
				}
            }
        }
        return 0;
    },

    getColValues: function (valuesTable,col_id,byCurrent) {
        var result = new Array;
        var j=0;
        for (j=0;j<valuesTable.length;j++) {
            if (byCurrent==true)
                result[result.length] = this.getItem(j,col_id).getValue();
            else
                result[result.length] = valuesTable[j]["cells"][col_id]["value"];
        }
        return result;
    },

    cellClick: function(event) {
        var obj = this.getItem(eventTarget(event).getAttribute("row"),eventTarget(event).getAttribute("column"));
        if (obj!=0 && obj!=null)
            obj.setFocus();
    },

    cellDblClick: function(event) {
        var ev = new Array;
        var obj = this.getItem(eventTarget(event).getAttribute("row"),eventTarget(event).getAttribute("column"));
        if (obj!=0 && obj!=null) {
            ev["target"] = obj.node;
            obj.dblClick(ev);
        }
    },
    
    refreshButton_onClick: function(event) {
    	this.rebuild();
    },
    
    rebuild: function() {
    	this.build();
    }
});