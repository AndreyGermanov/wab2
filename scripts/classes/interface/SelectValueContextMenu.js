var SelectValueContextMenu = Class.create(SelectEntityFloatDiv, {    
    load: function(not_build) {
    	var tbl=null;
    	var row=null;
    	var td=null;
    	if (!not_build) {
	        tbl = getElementById(this.node,this.node.id+'_table');
	        tbl.setAttribute("bgcolor","#FFFFFF");
	        tbl.setAttribute("border","0");
	        row = getElementById(this.node,this.node.id+'_row');
	        row.setAttribute("bgcolor","#FFFFFF");
	        row.setAttribute("object",this.object_id);
	        row.setAttribute("id",this.node.id+"_row");
	        row.innerHTML = '';
	        td = document.createElement("td");
	        td.setAttribute("bgcolor","#FFFFFF");
	        td.style.width='100%';
	        td.setAttribute("object",this.object_id);
	        td.setAttribute("id",this.object_id+"_col");
	        iframe = document.createElement("iframe");
	        iframe.setAttribute("object",this.object_id);
	        iframe.setAttribute("id",this.node.id+"_frame");
	        iframe.setAttribute("frameborder",0);
    	}
        var src  = "index.php?object_id="+this.selectClass+"_"+this.module_id+"_select";
        var args = new Object;
        args["value"] = this.value;
        var arr = this.selectOptions.replace(/,/g,"~").replace(/xoxoxo/g,"=").split('~');
        var o=null;
        for (o in arr) {
        	if (typeof arr[o] != "function") {
        		var parts = arr[o].split("=");
        		args[parts[0]] = parts[1];
        	}        		
        }
        args["parent_object_id"] = this.object_id;
        iframe.src = src+"&hook=show&arguments="+Object.toJSON(args);
        if (!not_build) {
            iframe.style.width = '100%';
            iframe.style.height = '100%';
        	td.appendChild(iframe);
        	row.appendChild(td);
        	tbl.appendChild(row);
            this.node.appendChild(tbl);                
        }
    },
    
    selectBtnClick: function(event) {
        $O(this.parent_object_id,'').node.setAttribute("show_float_div","false");
        $O(this.parent_object_id,"").module_id = this.module_id;
        $O(this.parent_object_id,"").selectEntity();
        $O(this.parent_object_id,'').node.setAttribute("show_float_div","true");        
    }
});