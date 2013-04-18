var SelectEntityFloatDiv = Class.create(ContextMenu, {
    
    load: function() {
        var tbl = getElementById(this.node,this.node.id+'_table');
        tbl.setAttribute("bgcolor","#FFFFFF");
        tbl.setAttribute("border","0");
        var row = getElementById(this.node,this.node.id+'_row');
        row.setAttribute("bgcolor","#FFFFFF");
        row.setAttribute("object",this.object_id);
        row.setAttribute("id",this.node.id+"_row");
        row.innerHTML = '';
        var td = document.createElement("td");
        td.setAttribute("bgcolor","#FFFFFF");
        td.style.width='100%';
        td.setAttribute("object",this.object_id);
        td.setAttribute("id",this.object_id+"_col");
        var iframe = document.createElement("iframe");
        iframe.setAttribute("object",this.object_id);
        iframe.setAttribute("id",this.node.id+"_frame");
        iframe.setAttribute("frameborder",0);
        var treeClass = null;
        var src = "";
        var treeEnd = "";
        if (this.treeClassName!="")
            treeClass = this.treeClassName;
        else
            treeClass = "EntityTree";
        if (treeClass.split("_").length>1) {
            var arr = treeClass.split("_");
            treeClass = arr.shift();
            treeEnd ="_"+arr.join("_");
        } else
            treeEnd = "";
        if (treeEnd=="_")
            return 0;
        if (this.module_id=="") {
            src  = "?object_id="+treeClass+treeEnd+"_"+this.object_id.replace(/_/g,'');
        }
        else {
            src  = "?object_id="+treeClass+"_"+this.module_id+treeEnd+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,'');
        }
        var args = new Object;
        args["className"] = this.className;
        args["condition"] = this.condition.replace(/\#/g,'xoxo');
        args["childCondition"] = this.childCondition.replace(/\#/g,'xoxo');
        args["condition"] = this.condition.replace(/\@/g,'yoyo');
        args["childCondition"] = this.childCondition.replace(/\#/g,'yoyo');
        args["condition"] = this.condition.replace(/\=/g,'zozo');
        args["childCondition"] = this.childCondition.replace(/\=/g,'zozo');
        args["entityId"] = this.entityId;
        args["sortOrder"] = this.sortOrder;
        args["fieldList"] = this.fieldList;
        args["fieldAccess"] = this.fieldAccess;
        args["fieldDefaults"] = this.fieldDefaults;
        if (this.additionalFields)
        	args["additionalFields"] = this.additionalFields;
        args["adapterId"] = this.adapterId;
        args["tableId"] = this.tableId;
        if (this.editorType=="entityDataTable")
        	args["editorType"] = this.editorType;
        else
        	args["editorType"] = 'none';
        args["forEntitySelect"] = "true";
        args["selectGroup"] = this.selectGroup;
        args["parent_object_id"] = this.object_id;
        args["result_object_id"] = this.object_id;
        args["title"] = this.classTitle;
        args["hierarchy"] = this.hierarchy;
        args["icon"] = this.skinPath+"images/Tree/folder.gif";
        iframe.src = src+"&hook=4&arguments="+Object.toJSON(args);

        iframe.style.width = '100%';
        iframe.style.height = '100%';
        td.appendChild(iframe);
        row.appendChild(td);
        tbl.appendChild(row);
        if (this.editorType!="" && this.editorType!="none" && this.editorType!="entityDataTable") {
            var row1 = document.createElement("row");
            row1.setAttribute("bgcolor","#FFFFFF");
            row1.setAttribute("object",this.object_id);
            row1.setAttribute("id",this.node.id+"_row2");
            td = document.createElement("td");
            td.style.backgroundColor = "#FFFFFF";
            td.setAttribute("object",this.object_id);
            td.setAttribute("id",this.object_id+"_col1");
            td.style.width = "100%";
            var input = document.createElement("input");
            input.setAttribute("object",this.object_id);
            input.setAttribute("id",this.object_id+"_selectBtn");
            input.setAttribute("type","button");
            input.style.width = "100%";
            input.setAttribute("value","Подробнее");
            input.setAttribute("onclick","$O('"+this.object_id+"','').selectBtnClick(event)");
            td.appendChild(input);
            row1.appendChild(td);
            tbl.appendChild(row1);
        }
        this.node.appendChild(tbl);                
    },
    
    dispatchEvent: function($super,event,params) {
        $super(event,params);
        var elem = null;
        if (event=="NODE_CLICKED") {
            if (params["result_object_id"]==this.object_id) {
                var obj = $O(params["object_id"]);
                elem = $I(obj.node.id+"_tree_"+params["node_id"]);
                if (elem!=0) {
                    this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.parent_object_id+",value="+elem.getAttribute("target_object")+",valueTitle="+$I(obj.node.id+"_tree_"+params["node_id"]+"_text").innerHTML+",old_value="+$O(this.parent_object_id,"").getValue()));
                }
                else {
                    elem = $I(obj.node.id+"_tree");
                    this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.parent_object_id+",value=,valueTitle=,old_value="+$O(this.parent_object_id,"").getValue()));
                }
                var ev = new Array;
                ev.target = $O(this.parent_object_id,'').node;
                removeContextMenu(ev);
            }
        }
    },
    
    selectBtnClick: function(event) {
        $O(this.parent_object_id,'').node.setAttribute("show_float_div","false");
        $O(this.parent_object_id,"").module_id = this.module_id;
        $O(this.parent_object_id,"").selectEntity();
        $O(this.parent_object_id,'').node.setAttribute("show_float_div","true");        
    }
});